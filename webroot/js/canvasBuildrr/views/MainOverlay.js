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
	// save the coords of the initial click
	click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
	click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
	//debug
	console.log('wrapper clicked at: ' + click.x + ', ' + click.y);
};

$("canvas#canvas").on('click', function( event ) {
	mainMenuHandler(event);
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
			mouseenter: function(event) {
				//console.log('yep');
			},
			mouseleave: function(event) {
				//console.log('nope');
			},
			click: function(event) {
				var clickedObject = getClickedObject($(this));
				textEditHandler(event, clickedObject);
				return false;
			},
			mousedown: function(event) {
				canDrag = true;
				var clickedObject = getClickedObject($(this));
				canvas.onmousemove = dragItem(event, clickedObject);
			},
			mouseup: function(event) {
				canDrag = false;
				canvas.onmousemove = null;
			}
		}, ".cb_placeholder");

function dragItem(event, clickedObject) {
	if ( canDrag === true ) {
		clickedObject
				.set('x', event.pageX - $("#cb_canvasWrapper").offset().left)
				.set('y', event.pageY - $("#cb_canvasWrapper").offset().top);
	}
	console.log(canDrag);
}

function getClickedObject($element) {
	console.log( 'clicked ' + $element.attr('data-cid') );
	var clickedObject;
	if ( $element.attr('data-model') === 'TextObject' ) {
		clickedObject = TextObjectCollection.get($element.attr('data-cid'));
	}
	if ( $element.attr('data-model') === 'ImageObject' ) {
		clickedObject = ImageObjectCollection.get($element.attr('data-cid'));
	}

	return clickedObject;
}