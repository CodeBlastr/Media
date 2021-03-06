// wrap the canvas
element.wrap('<div id="cb_canvasWrapper" />');
$("#cb_canvasWrapper").css('width', element.attr('width'));

// create our main action menu
$("#cb_canvasWrapper").after('<div id="cb_circleMenu" />');
$("#cb_circleMenu").append('<a id="cb_addText">Abc</a> <a id="cb_addImage">img</a>');
$("#cb_circleMenu").append('<a id="cb_cancel">&times;</a>');

var dragged;

var mainMenuHandler = function( event ) {
	// get rid of open editors
	$(".cb_addEditImage, .cb_addEditText").remove();
	// show the menu
	$('#cb_circleMenu').css({'top': event.pageY - 50, 'left': event.pageX - 50});
	$('#cb_circleMenu').show();
	// save the coords of the initial click, where canvas top-left is 0,0
	click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
	click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
};

$("canvas#canvas").on('click', function( event ) {
	mainMenuHandler(event);
	return false;
});

/**
 * Main menu click actions
 */
$("#cb_addText").click(function( e ) {
	$('#cb_circleMenu').fadeOut(150);
	textEditHandler(e);
	return false;
});

$("#cb_addImage").click(function( e ) {
	$('#cb_circleMenu').fadeOut(150);
	imageEditHandler(e);
	return false;
});

$("#cb_cancel").click(function( e ) {
	$('#cb_circleMenu').fadeOut(150);
	return false;
});


$("#cb_canvasWrapper").parent().parent()
		.on({
			mouseup: function(event) {
				$("#cb_canvasWrapper").unbind('mousemove');
				return false;
			}
		})
		.on({
			mouseenter: function(event) {
				var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
				if ( clickedObject.get('isEditable') === true ) {
					$(this).addClass('cb_placeholderHover');
				}
				return false;
			},
			mouseleave: function(event) {
				$(this).removeClass('cb_placeholderHover');
				return false;
			},
			click: function(event) {
				var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
				if ( clickedObject.get('isEditable') === false ) {
					return false;
				}
				if ( dragged === true ) {
					dragged = false;
				} else {
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
				var clickedObject = AppModel.get('collection').get($(this).attr('data-cid'));
				if ( clickedObject.get('isEditable') === false ) {
					return false;
				}
				// attach binding for object movement
				var cursorPosition = {
					originalX: $("#cb_canvasWrapper").offset().left - event.pageX,
					originalY: $("#cb_canvasWrapper").offset().top - event.pageY
				};
				var objectPosition = {
					originalX: clickedObject.get('x'),
					originalY: clickedObject.get('y')
				};
				$("#cb_canvasWrapper").bind('mousemove', function(event) {
					dragged = true;
					clickedObject.set({
						x: objectPosition.originalX - (($("#cb_canvasWrapper").offset().left - event.pageX) - cursorPosition.originalX),
						y: objectPosition.originalY - (($("#cb_canvasWrapper").offset().top - event.pageY) - cursorPosition.originalY)
					});
					return false;
				});
				return false;
			}
		}, ".cb_placeholder");


/**
 * corner clicks
 */
$("#cb_canvasWrapper").parent().parent()
		.on({
			click: function(event) {
				return false;
			},
			dblclick: function(event) {
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
			mousedown: function(event) {
				var clickedObject = AppModel.get('collection').get($(this).parent().attr('data-cid'));
				
				if ( $(this).hasClass("cb_ph_topRight") ) {
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
						clickedObject.set('rotation', newRotation);
					});
				}
				
				if ( $(this).hasClass("cb_ph_topLeft") ) {
					if ( clickedObject.get('type') === 'ImageObject' ) {
						clickedObject.resize();
					}
				}
				
				if ( $(this).hasClass("cb_ph_bottomLeft") ) {
					clickedObject.set('scale', [clickedObject.get('scale')[0] * -1, 1]);
				}
				
				if ( $(this).hasClass("cb_ph_bottomRight") ) {
					clickedObject.set('scale', [1, clickedObject.get('scale')[1] * -1]);
				}
				
				return false;
			},
			mouseup: function(event) {
				$("#cb_canvasWrapper").unbind('mousemove');
				return false;
			}

		}, ".cb_ph_corner");

