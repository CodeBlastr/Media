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
		  // wrap the canvas
		  element.wrap('<div id="cb_canvasWrapper" />');
		  $("#cb_canvasWrapper").css('width', element.attr('width'));
		  $("#cb_canvasWrapper").append('<div id="cb_circleMenu" />');
		  // create our main action menu
		  $("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
		  $("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');
		}

		function initClickHandlers() {
		  $("#cb_canvasWrapper").click(function (e) {
			console.log('wrapper clicked');
			// show the menu
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

			// create a wrapper for a text input
			var newAddEditText = $('<div class="cb_addEditText" />');
			$("#cb_canvasWrapper").append(newAddEditText);
			newAddEditText.css({'top':e.pageY-50,'left':e.pageX-50, 'position':'absolute'});

			// create the fontList for the textToolbar
			var fontList = $('<ul id="fontList"><li class="init">- choose font -</li><li id="ABeeZee" style="font-family:\'ABeeZee\';">ABeeZee</li><li id="Abel" style="font-family:\'Abel\';">Abel</li></ul>');

			// make the fontList act like a select box
			fontList.on("click", ".init", function() {
				$(this).closest("ul").children('li:not(.init)').toggle();
			});
			var allOptions = fontList.children('li:not(.init)');
			fontList.on("click", "li:not(.init)", function() {
				allOptions.removeClass('selected');
				$(this).addClass('selected');
				fontList.children('.init').html($(this).html());
				allOptions.toggle();
			});

			// create the textToolbar
			var textToolBar = $('<div class="cb_textToolbar" />');
			textToolBar
					.append('<button>colors</button>')
					.append(fontList)
			;
			newAddEditText.append(textToolBar);

			// create the text input
			var newTextInput = $('<input type="text" />');
			newAddEditText.append(newTextInput);
			var newElement = new newTextObject({x: click.x, y: click.y});

			// ?
			elements.push( newElement );

			// bind events to the new text input
			newTextInput
					.on('change keyup', function() {
					  newElement.content = newTextInput.val();
					  elements.push( newElement );
					  writeText( newElement );
					})
					.on('keydown', function(event){
					  if ( event.which === 27 ) {
						// ESC pressed
						newAddEditText.hide();
					  }
					})
//					.on('blur', function(){
//					  newAddEditText.hide();
//					  return false;
//					})
				;
			
			newAddEditText.click(function(){
			  return false;
			});

			// show the text wrapper & input
			newAddEditText.show();
			newTextInput.focus();

			return false;
		  });

		  $("#cb_addImage").click(function (e) {
			console.log('cb_addImage clicked');
			return false;
		  });

		  // hide the menu when close button clicked
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
