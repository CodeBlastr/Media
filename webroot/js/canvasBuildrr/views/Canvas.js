// Filename: views/Canvas
define([
	'jquery',
	'underscore',
	'backbone',
	'models/CollectionContainer',
	'models/ImageObject',
	'models/TextObject',
	'simplecolorpicker'
], function( $, _, Backbone, AppModel, ImageObject, TextObject ) {

	var CanvasView = Backbone.View.extend({
		
		initialize: function( attrs ) {
			console.log('CV init');
			this.options = attrs;
			this.render();
			
			// not sure if I should be tying to Backbone here, but did so as a last resort.
			var element = $("#canvas");
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
				var collection = AppModel.get('collection');	
				collection.add(image);
			}, false);

			/**
			 * LISTEN FOR UN-SELECTED IMAGES
			 */
			document.addEventListener('mediaBrowserMediaUnSelected', function (e) {
				var model = e.detail;
				var collection = AppModel.get('collection');
				var relativeFilePath = "/theme/default/media/images/" + model.attributes.filename + "." + model.attributes.extension;
				var deselectedObject = collection.findWhere({content: relativeFilePath});
				collection.remove( deselectedObject );
			}, false);

			/**
			 * BACKGROUND CONTROLS
			 */
			$(function() {
				$('select[name="bgColorpicker"]').simplecolorpicker({picker: true});
				$("select[name='bgColorpicker']").change(function() {
					AppModel.set('backgroundColor', $(this).val());
				});
			});

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
			"mouseup .cb_ph_corner": 'cornerMouseUp'
		},
		
		textEditHandler: function( event, text ) {
			console.log('clicked'); // not firing at all....
			Backbone.click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
			Backbone.click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
			if ( text === undefined ) {
				text = new TextObject({x: Backbone.click.x, y: Backbone.click.y});
				AppModel.get('collection').add(text);
			}
			var textEditor = new TextEditView({
				model: text,
				el: $("#cb_canvasWrapper").parent(),
				top: text.get('y') + $("#cb_canvasWrapper").offset().top + 10,
				left: text.get('x') + $("#cb_canvasWrapper").offset().left,
				content: text.get('content')
			});
		},
				
		placeholderMouseUp: function( e ) {
			$("#cb_canvasWrapper").unbind('mousemove');
			return false;
		},
				
		placeholderMouseEnter: function( e ) {
			var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
			if ( clickedObject.get('isEditable') === true ) {
				$(this).addClass('cb_placeholderHover');
			}
			return false;
		},
				
		placeholderMouseLeave: function( e ) {
			$(this).removeClass('cb_placeholderHover');
			return false;
		},
				
		placeholderClick: function( e ) {
			var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
			if ( clickedObject.get('isEditable') === false ) {
				return false;
			}
			if ( this.dragged === true ) {
				this.dragged = false;
			} else {
				if ( $(this).attr('data-model') === 'TextObject' ) {
					textEditHandler(e, clickedObject);
				}
				if ( $(this).attr('data-model') === 'ImageObject' ) {
					imageEditHandler(e, clickedObject);
				}
			}
			return false;
		},
				
		placeholderMouseDown: function( e ) {
			var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
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
				this.dragged = true;
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
			var clickedObject = AppModel.get('collection').get($(this).parent().attr('data-cid'));
			if ( $(this).hasClass("cb_ph_topLeft") ) {
				if ( clickedObject.get('type') === 'ImageObject' ) {
					clickedObject.autoResize();
				}
			}
			if ( $(this).hasClass("cb_ph_topRight") ) {
				clickedObject.set('rotation', 0);
			}
			return false;
		},
		
		cornerMouseDown: function( e ) {
			var clickedObject = AppModel.get('collection').get($(this).parent().attr('data-cid'));

			if ( $(this).hasClass("cb_ph_topRight") ) {
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

			if ( $(this).hasClass("cb_ph_topLeft") ) {
				if ( clickedObject.get('type') === 'ImageObject' ) {
					clickedObject.resize();
				}
			}

			if ( $(this).hasClass("cb_ph_bottomLeft") ) {
				clickedObject.set('scale', [ clickedObject.get('scale')[0] * -1, 1 ]);
			}

			if ( $(this).hasClass("cb_ph_bottomRight") ) {
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
