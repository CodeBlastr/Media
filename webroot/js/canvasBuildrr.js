/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */
var element = $("#canvas");
//(function( $ ) {
//
//	$.fn.canvasBuildrr = function( options ) {
//
//		var settings = $.extend({
//			debug: true
//		}, options);
//
//		$.fn.canvasBuildrr.init(this);
//
//	};
//
//	$.fn.canvasBuildrr.init = function( element ) {
		/**
		 * set up the necessary pointers
		 */
		var canvas = document.getElementById(element.attr('id'));
		var context = canvas.getContext("2d");

		var click = {x: '', y: ''};

		var TextObjects = Backbone.Collection.extend({
			model: TextObject
		});
		var TextObjectCollection = new TextObjects();

		var ImageObjects = Backbone.Collection.extend({
			model: ImageObject
		});
		var ImageObjectCollection = new ImageObjects();

		var TextObject = Backbone.Model.extend({
			defaults: {
				type: 'text',
				content: '',
				fontFamily: 'Arial',
				fontColor: '#333333',
				fontSize: 16,
				width: '',
				x: '',
				y: '',
				rotation: ''
			},
			initialize: function() {
				this.on("change:content", refreshCanvas);
				this.on("change:fontColor", refreshCanvas);
				this.on("change:fontSize", refreshCanvas);
			},
			write: function() {
				console.log('write()');
				context.lineWidth = 1;
				context.fillStyle = this.get('fontColor');
				context.lineStyle = this.get('fontColor');
				context.font = this.get('fontSize') + 'px ' + this.get('fontFamily');
				context.fillText(this.get('content'), this.get('x'), this.get('y'));
				this.set("width", context.measureText(this.get('content')).width);
				//debug
				console.log('writing text at: ' + this.get('x') + ', ' + this.get('y'));
			}
		});

		var ImageObject = Backbone.Model.extend({
			defaults: {
				type: 'image',
				content: '',
				x: '',
				y: '',
				rotation: ''
			},
			initialize: function() {
				this.on("change", refreshCanvas);
			},
			draw: function() {

			}
		});


		var TextEditView = Backbone.View.extend({
			initialize: function(attrs){
				this.options = attrs;
				this.render();
				$('select[name="colorpicker"]').simplecolorpicker();
			},
			render: function(){
				// Compile the template using underscore
				var template = _.template( $("#template-textedit").html(), this.options );
				// Load the compiled HTML into the Backbone "el"
				this.$el.append( template );

				$("#cb_canvasWrapper").unbind();
//				$("#cb_canvasWrapper").bind('mousemove', function( event ) {
//					mousemoveHandler(event);
//				});
				return this;
			},
			events: {
				"keyup .asdf":	"updateText",
				"click .cb_addEditText": "falseHandler",
				"click .cb_close": "close",
				'change select[name="colorpicker"]': 'updateColor',
				'change select[name="fontsizepicker"]': 'updateFontsize'
			},
			updateText: function( event ){
				console.log('updateText()');
				this.model.set('content', event.target.value);
			},
			updateColor: function( event ){
				console.log('updateColor()');
				this.model.set('fontColor', event.target.value);
			},
			updateFontsize: function( event ){
				console.log('updateFontsize()');
				this.model.set('fontSize', event.target.value);
			},
			falseHandler: function( event ){
				console.log('falseHandler()');
				return false;
			},
			close: function( event ){
				$('select[name="colorpicker"]').simplecolorpicker('destroy');
				this.$el.find('.cb_addEditText').remove();
				$("#cb_canvasWrapper").bind('click', function( event ) {
					mainMenuHandler(event);
				});
			}
		});


		/**
		 * hook up our point & click menu
		 */
		initControlElements(element);
		initClickHandlers();

		function initControlElements( element ) {
			// wrap the canvas
			element.wrap('<div id="cb_canvasWrapper" />');
			$("#cb_canvasWrapper").css('width', element.attr('width'));
			$("#cb_canvasWrapper").after('<div id="cb_circleMenu" />');
			// create our main action menu
			$("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
			$("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');
		}

		var mainMenuHandler = function( event ) {
			// show the menu
			$('#cb_circleMenu').css({'top': event.pageY - 50, 'left': event.pageX - 50, 'position': 'absolute'});
			$('#cb_circleMenu').show();
			// save the coords of the initial click
			click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
			click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
			//debug
			console.log('wrapper clicked at: ' + click.x + ', ' + click.y);
		};

		var textEditHandler = function( event, text) {
			if ( text === undefined ) {
				text = new TextObject({x: click.x, y: click.y});
				TextObjectCollection.add(text);
				//debug
				console.log('text added to textCollection at: ' + click.x + ', ' + click.y)
			}
			var textEditor = new TextEditView({
				model: text,
				el: $("#cb_canvasWrapper"),
				top: event.pageY,
				left: event.pageX
			});
			
		};

		function initClickHandlers() {
			$("#cb_canvasWrapper").bind('click', function( event ) {
				mainMenuHandler(event);
			});

			$("#cb_addText").click(function( e ) {
				console.log('cb_addText clicked');
				// hide the menu
				$('#cb_circleMenu').hide();
				$("#cb_canvasWrapper").unbind('click');
				textEditHandler(e);

//				// create a wrapper for a text input
//				var newAddEditText = $('<div class="cb_addEditText" />');
//				$("#cb_canvasWrapper").append(newAddEditText);
//				newAddEditText.css({'top': e.pageY - 50, 'left': e.pageX - 50, 'position': 'absolute'});
//
//				// create the fontList for the textToolbar
//				var fontList = $('<ul id="fontList"><li class="init">- choose font -</li><li id="ABeeZee" style="font-family:\'ABeeZee\';">ABeeZee</li><li id="Abel" style="font-family:\'Abel\';">Abel</li></ul>');
//
//				// make the fontList act like a select box
//				fontList.on("click", ".init", function() {
//					$(this).closest("ul").children('li:not(.init)').toggle();
//				});
//				var allOptions = fontList.children('li:not(.init)');
//				fontList.on("click", "li:not(.init)", function() {
//					allOptions.removeClass('selected');
//					$(this).addClass('selected');
//					fontList.children('.init').html($(this).html());
//					allOptions.toggle();
//				});
//
//				// create the textToolbar
//				var textToolBar = $('<div class="cb_textToolbar" />');
//				textToolBar
//						.append('<button>colors</button>')
//						.append(fontList)
//						;
//				newAddEditText.append(textToolBar);
//
//				// create the text input
//				var newTextInput = $('<input type="text" />');
//				newAddEditText.append(newTextInput);

//				var text = new TextObject({x: click.x, y: click.y});
//				TextObjectCollection.add(text);

				// bind events to the new text input
//				newTextInput
//						.on('change keyup', function() {
//							text.set("content", newTextInput.val());
//						})
////						.on('keydown', function( event ) {
////							if ( event.which === 27 ) {
////								// ESC pressed
////								newAddEditText.remove();
////							}
////						})
////					.on('blur', function(){
////					  newAddEditText.hide();
////					  return false;
////					})
//						;
//
//				newAddEditText.click(function() {
//					return false;
//				});
//
//				// show the text wrapper & input
//				newAddEditText.show();
//				newTextInput.focus();

				return false;
			});

			$("#cb_addImage").click(function( e ) {
				console.log('cb_addImage clicked');
				return false;
			});

			// hide the menu when close button clicked
			$("#cb_cancel").click(function( e ) {
				$('#cb_circleMenu').hide();
				return false;
			});
		}

		function clear() {
			console.log('clear()');
			context.save();
			context.clearRect(0, 0, canvas.width, canvas.height);
			context.restore();
		}

		function refreshCanvas() {
			console.log('refreshCanvas');
			clear();
			TextObjectCollection.each(function( textObject ) {
				console.log(TextObjectCollection);
				console.log(textObject);
				textObject.write();
			});
		}


		function rotateAndWrite() {
			context.save();
			context.translate(newx, newy);
			context.rotate(-Math.PI/2);
			context.textAlign = "center";
			context.fillText("Your Label Here", labelXposition, 0);
			context.restore();
		}


		$("#cb_canvasWrapper").bind('mousemove', function(event){
			mousemoveHandler(event);
		});

		function mousemoveHandler ( event ) {
			if ( TextObjectCollection.length > 0 ) {
				var x = event.pageX - $("#cb_canvasWrapper").offset().left;
				var y = event.pageY - $("#cb_canvasWrapper").offset().top;
//				console.log(x + ', ' + y);
				TextObjectCollection.each(function( textObject ) {
					// get the bounds of the text object.
					// @todo account for rotation
					// @todo the objectY should not need fontsize deducted. original value in the object is wrong.
					var objectXlow = textObject.get('x');
					var objectXhigh = textObject.get('x') + textObject.get('width');
					var objectYlow = textObject.get('y') - textObject.get('fontSize');
					var objectYhigh = textObject.get('y');

//					console.log(objectXlow + ', ' + objectYlow + ' : ' + objectXhigh + ', ' + objectYhigh);

					// do something if mouse is within bounds
					if (
							x > objectXlow &&
							x < objectXhigh &&
							y > objectYlow &&
							y < objectYhigh
					) {
						$("#cb_canvasWrapper").css('cursor', 'pointer');
						$("#cb_canvasWrapper")
								.unbind('click')
								.bind('click', function( event ) {
									textEditHandler(event, textObject);
								});
					} else {
						$("#cb_canvasWrapper").css('cursor', 'crosshair');
						$("#cb_canvasWrapper")
								.unbind('click')
								.bind('click', function( event ) {
									mainMenuHandler(event);
								});
					}
				});
			}
		}
//
//	};
//
//}(jQuery));
