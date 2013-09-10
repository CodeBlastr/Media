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
				// attach binding for object movement
				var clickedObject = getClickedObject($(this));
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
					var xPrev;
					$("#cb_canvasWrapper").bind('mousemove', function(event) {
						console.log('resizing');

						//var newWidth = clickedObject.get('width') + (event.clientX - $("#cb_canvasWrapper").offset().left) - clickedObject.get('x'); // good X
						//var newWidth = clickedObject.get('width') + (event.clientY - $("#cb_canvasWrapper").offset().top) - clickedObject.get('y'); // same as above..?				        
				        
				        if ( xPrev < event.pageX ) {
				        	// mouse moving right
				        	var newWidth = clickedObject.get('width') + 1;
				        } else {
				        	// mouse moving left
				        	var newWidth = clickedObject.get('width') - 1;
				        }
				        xPrev = event.pageX;

						if ( newWidth > 40 ) {
							clickedObject
								.set('width', newWidth)
								.set('height', newWidth * (clickedObject.get('aspectRatio')));
						}
					});
					return false;
				}
				return false;
			},
			mouseup: function(event) {
				$("#cb_canvasWrapper").unbind('mousemove');
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
