// Filename: views/Canvas
define([
	'jquery',
	'underscore',
	'backbone',
	'models/ImageObject',
	'models/TextObject',
	'views/TextEdit',
	'simplecolorpicker'
], function( $, _, Backbone, ImageObject, TextObject, TextEditView ) {

	var CanvasView = Backbone.View.extend({
		
		el: '.canvasBuildrr',
		
		initialize: function( attrs ) {
			console.log('CV init');
			this.options = attrs;
			this.render();
			
			// setup global pointers to the canvas
			var element = this.$el.find("#canvas");
			Backbone.canvas = document.getElementById(element.attr('id'));
			Backbone.context = Backbone.canvas.getContext('2d');
			
			Backbone.click = {x: '', y: ''};
			Backbone.dragged = false;

			/**
			 * LISTEN FOR SELECTED IMAGES
			 */
			document.addEventListener('mediaBrowserMediaSelected', function (e) {
				var model = e.detail;
				var image = new ImageObject({
					'type': 'ImageObject',
					'content': "/theme/default/media/images/" + model.attributes.filename + "." + model.attributes.extension
				});
				var collection = Backbone.AppModel.get('collection');	
				collection.add(image);
			}, false);

			/**
			 * LISTEN FOR UN-SELECTED IMAGES
			 */
			document.addEventListener('mediaBrowserMediaUnSelected', function (e) {
				var model = e.detail;
				var collection = Backbone.AppModel.get('collection');
				var relativeFilePath = "/theme/default/media/images/" + model.attributes.filename + "." + model.attributes.extension;
				var deselectedObject = collection.findWhere({content: relativeFilePath});
				collection.remove( deselectedObject );
			}, false);

			/**
			 * BACKGROUND CONTROLS
			 */
			$('select[name="bgColorpicker"]').simplecolorpicker({picker: true});
			$("select[name='bgColorpicker']").change(function() {
				Backbone.AppModel.set('backgroundColor', $(this).val());
			});
			
			/**
			 * SAVE BUTTON
			 */
			this.$el.parent().find("#saveCanvas").click(function(e){
				var options = {silent: true};
				var method;
				if ( $(e.currentTarget).attr('data-saved') === 'false' ) {
					method = 'create';
				} else {
					method = 'update';
				}
			
				// update screenshot
				var hasScreenshot = false;
				Backbone.AppModel.get('collection').each(function( canvasObject, index ) {
					if ( canvasObject.get('type') === 'screenshot' ) {
						hasScreenshot = true;
						canvasObject.set('content', Backbone.canvas.toDataURL());
						Backbone.AppModel.get('collection')[index] = canvasObject;
					}
				});
				if ( hasScreenshot === false ) {
					image = new ImageObject({
						'type': 'screenshot',
						'content': canvas.toDataURL(),
						'isEditable': false
						});
					Backbone.AppModel.get('collection').add(image);
				}
			
				Backbone.AppModel.sync(method, Backbone.AppModel, options);
				$(e.currentTarget).attr('data-saved', 'true');
			});

			if ( canvasData !== null ) {
				Backbone.AppModel.get('collection').reset();
				Backbone.AppModel.reload( canvasData );
			}

		},
		
		render: function() {
			var template = _.template($("#template-canvas").html(), this.options);
			$('.canvasBuildrr').append(template);
			return this;
		},
				
		events: {
			"click #canvas": 'textEditHandler',
			"mouseup .cb_placeholder": 'placeholderMouseUp',
			"mouseenter .cb_placeholder": 'placeholderMouseEnter',
			"mouseleave .cb_placeholder": 'placeholderMouseLeave',
			"click .cb_placeholder": 'placeholderClick',
			"mousedown .cb_placeholder": 'placeholderMouseDown',
			"click .cb_ph_corner": 'cornerClick',
			"dblclick .cb_ph_corner": 'cornerDblClick',
			"mousedown .cb_ph_corner": 'cornerMouseDown',
			"mouseup": 'cornerMouseUp'
		},
		
		textEditHandler: function( event, text ) {
			Backbone.click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
			Backbone.click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
			if ( text === undefined ) {
				text = new TextObject({x: Backbone.click.x, y: Backbone.click.y});
				Backbone.AppModel.get('collection').add(text);
			}
			var textEditor = new TextEditView({
				model: text,
				el: $("#cb_canvasWrapper").parent(),
				top: text.get('y') + $("#cb_canvasWrapper").offset().top + 10,
				left: text.get('x') + $("#cb_canvasWrapper").offset().left,
				content: text.get('content')
			});
			this.delegateEvents();
		},
		
		imageEditHandler: function( event, image ) {
			if ( image === undefined ) {
				image = new ImageObject();
				Backbone.AppModel.get('collection').add(image);
			}
			var imageEditor = new ImageEditView({
				model: image,
				el: $("#cb_canvasWrapper").parent(),
				top: image.get('y') + $("#cb_canvasWrapper").offset().top + 10,
				left: image.get('x') + $("#cb_canvasWrapper").offset().left,
				content: image.get('content')
			});
			this.delegateEvents();
		},
		
		placeholderMouseUp: function( e ) {
			$("#cb_canvasWrapper").unbind('mousemove');
			return false;
		},
		
		placeholderMouseEnter: function( e ) {
			var clickedObject = Backbone.AppModel.get('collection').get($(e.currentTarget).attr('data-cid'));
			if ( clickedObject.get('isEditable') === true ) {
				$(e.currentTarget).addClass('cb_placeholderHover');
			}
			return false;
		},
		
		placeholderMouseLeave: function( e ) {
			$(e.currentTarget).removeClass('cb_placeholderHover');
			return false;
		},
		
		placeholderClick: function( e ) {
			var clickedObject = Backbone.AppModel.get('collection').get($(e.currentTarget).attr('data-cid'));
			if ( clickedObject.get('isEditable') === false ) {
				return false;
			}
			if ( Backbone.dragged === true ) {
				Backbone.dragged = false;
			} else {
				if ( $(e.currentTarget).attr('data-model') === 'TextObject' ) {
					this.textEditHandler(e, clickedObject);
				}
				if ( $(e.currentTarget).attr('data-model') === 'ImageObject' ) {
					this.imageEditHandler(e, clickedObject);
				}
			}
			return false;
		},
				
		placeholderMouseDown: function( e ) {
			var clickedObject = Backbone.AppModel.get('collection').get($(e.currentTarget).attr('data-cid'));
			if ( clickedObject.get('isEditable') === false ) {
				return false;
			}
			// attach binding for object movement
			var cursorPosition = {
				originalX: $("#cb_canvasWrapper").offset().left - e.pageX,
				originalY: $("#cb_canvasWrapper").offset().top - e.pageY
			};
			var objectPosition = {
				originalX: clickedObject.get('x'),
				originalY: clickedObject.get('y')
			};
			$("#cb_canvasWrapper").bind('mousemove', function( event ) {
				Backbone.dragged = true;
				clickedObject.set({
					x: objectPosition.originalX - ( ( $("#cb_canvasWrapper").offset().left - event.pageX ) - cursorPosition.originalX ),
					y: objectPosition.originalY - ( ( $("#cb_canvasWrapper").offset().top - event.pageY ) - cursorPosition.originalY )
				});
				return false;
			});
			return false;
		},
				
		cornerClick: function( e ) {
			return false;
		},
				
		cornerDblClick: function( e ) {
			var clickedObject = Backbone.AppModel.get('collection').get($(e.currentTarget).parent().attr('data-cid'));
			if ( $(e.currentTarget).hasClass("cb_ph_topLeft") ) {
				if ( clickedObject.get('type') === 'ImageObject' ) {
					clickedObject.autoResize();
				}
			}
			if ( $(e.currentTarget).hasClass("cb_ph_topRight") ) {
				clickedObject.set('rotation', 0);
			}
			return false;
		},
		
		cornerMouseDown: function( e ) {
			var clickedObject = Backbone.AppModel.get('collection').get($(e.currentTarget).parent().attr('data-cid'));

			if ( $(e.currentTarget).hasClass("cb_ph_topRight") ) {
				var xPrev;
				$("#cb_canvasWrapper").bind('mousemove', function( event ) {
					if ( xPrev < event.pageX ) {
						// mouse moving right
						var newRotation = clickedObject.get('rotation') + 2;
					} else {
						// mouse moving left
						var newRotation = clickedObject.get('rotation') - 2;
					}
					xPrev = event.pageX;
					clickedObject.set('rotation', newRotation);
				});
			}

			if ( $(e.currentTarget).hasClass("cb_ph_topLeft") ) {
				if ( clickedObject.get('type') === 'ImageObject' ) {
					clickedObject.resize();
				}
			}

			if ( $(e.currentTarget).hasClass("cb_ph_bottomLeft") ) {
				clickedObject.set('scale', [ clickedObject.get('scale')[0] * -1, 1 ]);
			}

			if ( $(e.currentTarget).hasClass("cb_ph_bottomRight") ) {
				clickedObject.set('scale', [ 1, clickedObject.get('scale')[1] * -1 ]);
			}

			return false;
		},
		
		cornerMouseUp: function( e ) {
			$("#cb_canvasWrapper").unbind('mousemove');
			return false;
		}

	});

	// Our module now returns our view
	return CanvasView;
});
