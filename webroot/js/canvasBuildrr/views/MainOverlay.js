// wrap the canvas
element.wrap('<div id="cb_canvasWrapper" />');
$("#cb_canvasWrapper").css('width', element.attr('width'));
$("#cb_canvasWrapper").after('<div id="cb_circleMenu" />');

// create our main action menu
$("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
$("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');

var dragged;

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
	console.log('#cb_addImage clicked');
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
				return false;
			},
			mouseleave: function(event) {
				return false;
			},
			click: function(event) {
				console.log('.cb_placeholder click');
				
				if ( dragged === true ) {
					dragged = false;
				} else {
					var clickedObject = CanvasObjectCollection.get($(this).attr('data-cid'));
					if ( $(this).attr('data-model') === 'TextObject' ) {
						textEditHandler(event, clickedObject);
					}
					if ( $(this).attr('data-model') === 'ImageObject' ) {
						imageEditHandler(event, clickedObject);
					}
				}
				return false;
			},
			mousedown: function(event) {
				// attach binding for object movement
				var clickedObject = CanvasObjectCollection.get($(this).attr('data-cid'));
				var cursorPosition = {
					originalX: $("#cb_canvasWrapper").offset().left - event.pageX,
					originalY: $("#cb_canvasWrapper").offset().top - event.pageY
				};
				var objectPosition = {
					originalX: clickedObject.get('x'),
					originalY: clickedObject.get('y')
				};
				$("#cb_canvasWrapper").bind('mousemove', function(event) {
					console.log('moving object');
					dragged = true;
					clickedObject
						.set('x', objectPosition.originalX - (($("#cb_canvasWrapper").offset().left - event.pageX) - cursorPosition.originalX))
						.set('y', objectPosition.originalY - (($("#cb_canvasWrapper").offset().top - event.pageY) - cursorPosition.originalY));
					return false;
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
				return false;
			},
			dblclick: function(event) {
				if ( $(this).hasClass("cb_ph_topLeft") ) {
					var clickedObject = CanvasObjectCollection.get($(this).parent().attr('data-cid'));
					if ( clickedObject.get('type') === 'ImageObject' ) {
						clickedObject.autoResize();
					}
				}
			},
			mousedown: function(event) {
				var clickedObject = CanvasObjectCollection.get($(this).parent().attr('data-cid'));
				
				if ( $(this).hasClass("cb_ph_topRight") ) {
					console.log('rotate tool');
					var xPrev;
					$("#cb_canvasWrapper").bind('mousemove', function(event) {
						console.log('rotating');
						if ( xPrev < event.pageX ) {
				        	// mouse moving right
				        	var newRotation = clickedObject.get('rotation') + 2;
				        } else {
				        	// mouse moving left
				        	var newRotation = clickedObject.get('rotation') - 2;
				        }
				        xPrev = event.pageX;
						clickedObject.set('rotation', newRotation );
					});
					return false;
				}
				
				if ( $(this).hasClass("cb_ph_topLeft") ) {
					console.log('resize tool');
					if ( clickedObject.get('type') === 'ImageObject' ) {
						clickedObject.resize();
					}
				}
				
				if ( $(this).hasClass("cb_ph_bottomLeft") ) {
					console.log('flip horizontal tool');
					clickedObject.set('scale', [clickedObject.get('scale')[0] * -1, 1]);
					return false;
				}
				
				if ( $(this).hasClass("cb_ph_bottomRight") ) {
					console.log('flip vertical tool');
					clickedObject.set('scale', [1, clickedObject.get('scale')[1] * -1]);
					return false;
				}
				
				return false;
			},
			mouseup: function(event) {
				$("#cb_canvasWrapper").unbind('mousemove');
				return false;
			}

		}, ".cb_ph_corner");

