/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */

(function ( $ ) {

	$.fn.canvasBuildrr = function( options ) {

		var settings = $.extend({
			debug: true
		}, options );

		$.fn.canvasBuildrr.init(this);

	};

	$.fn.canvasBuildrr.init = function(element) {
		/**
		 * set up the necessary pointers
		 */
		var canvas = document.getElementById(element.attr('id'));
		var context = canvas.getContext("2d");
		var elements = [];

		var click = {x: '', y: ''};

		var imageElement = {
		  type: 'image'
		};

		function newTextObject( options ) {
		  var settings = $.extend({
			type : 'text',
			content : '',
			fontFamily : 'Arial',
			fontColor : '#333333',
			fontSize : '16px',
			x: '',
			y: ''
		  }, options );
		  this.type = 'text';
		  this.x = settings.x;
		  this.y = settings.y;
		  this.content = settings.content;
		  this.fontFamily = settings.fontFamily;
		  this.fontColor = settings.fontColor;
		  this.fontSize = settings.fontSize;
		}

		/**
		 * hook up our point & click menu
		 */
		initControlElements(element);
		initClickHandlers();
		
		function initControlElements(element) {
		  element.wrap('<div id="cb_canvasWrapper" />');
		  $("#cb_canvasWrapper").css('width', element.attr('width'));
		  $("#cb_canvasWrapper").append('<div id="cb_circleMenu" />');
		  $("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
		  $("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');
		}

		function initClickHandlers() {
		  $("#cb_canvasWrapper").click(function (e) {
			console.log('wrapper clicked');
			$('#cb_circleMenu').css({'top':e.pageY-50,'left':e.pageX-50, 'position':'absolute'});
			$('#cb_circleMenu').show();

			// save the coords of the initial click
			click.x = e.pageX - $("#cb_canvasWrapper").offset().left;
			click.y = e.pageY - $("#cb_canvasWrapper").offset().top;

		  });

		  $("#cb_addText").click(function (e) {
			console.log('cb_addText clicked');
			// hide the menu
			$('#cb_circleMenu').hide();

			// display a text element
			var newAddEditText = $('<div class="cb_addEditText" />');
			$("#cb_canvasWrapper").append(newAddEditText);
			newAddEditText.css({'top':e.pageY-50,'left':e.pageX-50, 'position':'absolute'});
			var newTextInput = $('<input type="text" />');
			newAddEditText.append(newTextInput);
			var newElement = new newTextObject({x: click.x, y: click.y});

			elements.push( newElement ); // ?

			newTextInput.on('change keyup', function() {
			  newElement.content = newTextInput.val();
			  elements.push( newElement );
			  writeText( newElement );
			});
			newTextInput.on('blur', function(){
			  newAddEditText.hide();
			  return false;
			});
			newAddEditText.click(function(){
			  return false;
			});
			newAddEditText.show();

			return false;
		  });

		  $("#cb_addImage").click(function (e) {
			console.log('cb_addImage clicked');
			return false;
		  });

		  $("#cb_cancel").click(function (e) {
			$('#cb_circleMenu').hide();
			return false;
		  });
		}

		function writeText(options) {
			clear();
			context.lineWidth	= 1;
			context.fillStyle	= "#CC00FF";
			context.lineStyle	= options.fontColor;
			context.font		= options.fontSize + ' ' + options.fontFamily;
			context.fillText(options.content, options.x, options.y);
		}


		function rect(x, y, w, h) {
			context.beginPath();
			context.rect(x, y, w, h);
			context.closePath();
			context.fill();
		}

		function clear() {
			context.clearRect(0, 0, $("#cb_canvasWrapper").css('width'), $("#cb_canvasWrapper").css('height'));
			context.fillStyle = "#FFFFFF";
			rect(0, 0, $("#cb_canvasWrapper").css('width'), $("#cb_canvasWrapper").css('height'));
		}

	};

}( jQuery ));
