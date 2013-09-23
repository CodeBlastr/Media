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
				//console.log('yep');
				return false;
			},
			mouseleave: function(event) {
				//console.log('nope');
				return false;
			},
			click: function(event) {
				console.log('.cb_placeholder click');
				
				var clickedObject = CanvasObjectCollection.get($(this).attr('data-cid'));
				if ( $(this).attr('data-model') === 'TextObject' ) {
					textEditHandler(event, clickedObject);
				}
				if ( $(this).attr('data-model') === 'ImageObject' ) {
					imageEditHandler(event, clickedObject);
				}

				return false;
			},
			mousedown: function(event) {
				// attach binding for object movement
				var clickedObject = CanvasObjectCollection.get($(this).attr('data-cid'));
				$("#cb_canvasWrapper").bind('mousemove', function(event) {
					console.log('moving object');
					console.log(clickedObject);
					clickedObject
						.set('x', event.clientX - $("#cb_canvasWrapper").offset().left)
						.set('y', event.clientY - $("#cb_canvasWrapper").offset().top);
					// stop the event that launches the Object Editors while dragging
					//stopPropogation('.cb_placeholder', 'click');
					return false;
				});
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
			dblclick: function(event) {
				if ( $(this).hasClass("cb_ph_topLeft") ) {
					
					var clickedObject = CanvasObjectCollection.get($(this).parent().attr('data-cid'));
					if ( clickedObject.get('type') === 'image' ) {
						clickedObject.autoResize();
					}
				}
			},
			mousedown: function(event) {
				var clickedObject = CanvasObjectCollection.get($(this).parent().attr('data-cid'));
				
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
					if ( clickedObject.get('type') === 'image' ) {
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


function stopPropogation(selector, event) {
    $(selector).on(event, function(e) {
        e.stopPropagation();
        return false;
    });
}
