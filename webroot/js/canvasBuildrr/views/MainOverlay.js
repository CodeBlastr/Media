// wrap the canvas
element.wrap('<div id="cb_canvasWrapper" />');
$("#cb_canvasWrapper").css('width', element.attr('width'));
$("#cb_canvasWrapper").after('<div id="cb_circleMenu" />');
// create our main action menu
$("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
$("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');

var mainMenuHandler = function( event ) {
	// show the menu
	$('#cb_circleMenu').css({'top': event.pageY - 50, 'left': event.pageX - 50});
	$('#cb_circleMenu').show();
	// save the coords of the initial click, where canvas top-left is 0,0
	click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
	click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
	//debug
	console.log('wrapper clicked at: ' + click.x + ', ' + click.y);
};

$("canvas#canvas").on('click', function( event ) {
	mainMenuHandler(event);
	return false;
});

/**
 * Main menu click actions
 */
$("#cb_addText").click(function( e ) {
	$('#cb_circleMenu').hide();
	textEditHandler(e);
	return false;
});

$("#cb_addImage").click(function( e ) {
	$('#cb_circleMenu').hide();
	imageEditHandler(e);
	return false;
});

$("#cb_cancel").click(function( e ) {
	$('#cb_circleMenu').hide();
	return false;
});


$("#cb_canvasWrapper").parent()
		.on({
			mouseup: function(event) {
				//console.log('mouseUp');
				$("#cb_canvasWrapper").unbind('mousemove');
				return false;
			}
		})
		.on({
			mouseenter: function(event) {
				//console.log('yep');
				return false;
			},
			mouseleave: function(event) {
				//console.log('nope');
				return false;
			},
			click: function(event) {
				console.log('click');

				if ( $(this).attr('data-model') === 'TextObject' ) {
					var clickedObject = TextObjectCollection.get($(this).attr('data-cid'));
					textEditHandler(event, clickedObject);
				}
				if ( $(this).attr('data-model') === 'ImageObject' ) {
					var clickedObject = ImageObjectCollection.get($(this).attr('data-cid'));
					imageEditHandler(event, clickedObject);
				}

				return false;
			},
			mousedown: function(event) {
				var clickedObject = getClickedObject($(this));

				// detect if they clicked in a corner
				var divClickX = event.pageX - $(this).offset().left;
				var divClickY = event.pageY - $(this).offset().top;
				//console.log(divClickX+', '+divClickY);

				var sizeOfCorner = 10;

				if ( divClickX < sizeOfCorner && divClickY < sizeOfCorner ) {
					// fire top-left corner click action
					console.log('top-left mousedown');
					return false;
				}
				if ( ($(this).width() - divClickX) < sizeOfCorner && (($(this).height() - divClickY) < sizeOfCorner) ) {
					// fire bottom-right corner click action
					console.log('bottom-right mousedown');
					return false;
				}
				if ( ($(this).width() - divClickX) < sizeOfCorner && divClickY < sizeOfCorner ) {
					// fire top-right corner click action
					console.log('top-right mousedown');
					return false;
				}
				if ( ($(this).height() - divClickY) < sizeOfCorner && divClickX < sizeOfCorner ) {
					// fire bottom-left corner click action
					console.log('bottom-left mousedown');
					return false;
				}

				// attach binding for image rotation
//				var TO_RADIANS = Math.PI/180;
//				function drawRotatedImage(image, x, y, angle) { 
//
//					// save the current co-ordinate system 
//					// before we screw with it
//					context.save(); 
//
//					// move to the middle of where we want to draw our image
//					context.translate(x, y);
//
//					// rotate around that point, converting our 
//					// angle from degrees to radians 
//					context.rotate(angle * TO_RADIANS);
//
//					// draw it up and to the left by half the width
//					// and height of the image 
//					context.drawImage(image, -(image.width/2), -(image.height/2));
//
//					// and restore the co-ords to how they were when we began
//					context.restore(); 
//				}


				// attach binding for image resizing
//				var aspectRatio = clickedObject.get('width') / clickedObject.get('height');
//				var newHeight = newWidth * aspectRatio;
//				$("#cb_canvasWrapper").bind('mousemove', function(event) {
//					clickedObject
//						.set('x', event.pageX - $("#cb_canvasWrapper").offset().left)
//						.set('height', newHeight);
//				});

				// attach binding for image movement
				$("#cb_canvasWrapper").bind('mousemove', function(event) {
					clickedObject
						.set('x', event.clientX - $("#cb_canvasWrapper").offset().left)
						.set('y', event.clientY - $("#cb_canvasWrapper").offset().top);
				});
				return false;
			}
		}, ".cb_placeholder");

/**
 * corner clicks
 */
$("#cb_canvasWrapper").parent()
		.on({
			click: function(event) {
				console.log('corner click');
				return false;
			},
			mousedown: function(event) {
				var clickedObject = getClickedObject( $(this).parent() );
				if ( $(this).hasClass("cb_ph_topRight") ) {
					console.log('rotate tool');
					$("#cb_canvasWrapper").bind('mousemove', function(event) {
						console.log('rotating');
						clickedObject.set('rotation', clickedObject.get('x') - (event.clientX-canvas.offsetLeft) );
					});
					return false;
				}
				if ( $(this).hasClass("cb_ph_topLeft") ) {
					console.log('resize tool');
					$("#cb_canvasWrapper").bind('mousemove', function(event) {
						console.log('resizing');
						clickedObject.set('scale', clickedObject.get('y') - (event.clientY-canvas.offsetTop) );
					});
					return false;
				}
				return false;
			}

		}, ".cb_ph_corner");


function getClickedObject($element) {
	console.log( 'clicked ' + $element.attr('data-cid') );
	var clickedObject = false;
	if ( $element.attr('data-model') === 'TextObject' ) {
		clickedObject = TextObjectCollection.get($element.attr('data-cid'));
	}
	if ( $element.attr('data-model') === 'ImageObject' ) {
		clickedObject = ImageObjectCollection.get($element.attr('data-cid'));
	}

	return clickedObject;
}
