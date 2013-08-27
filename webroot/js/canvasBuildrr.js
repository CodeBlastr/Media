/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */
var element = $("#canvas");

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
		this.on("change:content", this.refresh);
		this.on("change:fontSize", this.refresh);
		this.on("change:fontColor", this.refresh);
		// create a placeholder div for this new object on the canvas
		var placeholder = $('<div class="cb_placeholder" />');
		placeholder
				.attr('data-model', 'TextObject')
				.attr('data-cid', this.cid)
				.css('top', this.get('y'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('fontSize'));
		$("#cb_canvasWrapper").append(placeholder);
	},
	refresh: function() {
		refreshCanvas();
		// update the placeholder div
		$("div[data-cid='"+this.cid+"']")
				.css('top', this.get('y') - this.get('fontSize'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('fontSize'));
	},
	write: function() {
		//console.log('write()');
		context.lineWidth = 1;
		context.fillStyle = this.get('fontColor');
		context.lineStyle = this.get('fontColor');
		context.font = this.get('fontSize') + 'px ' + this.get('fontFamily');
		context.fillText(this.get('content'), this.get('x'), this.get('y'));
		this.set("width", context.measureText(this.get('content')).width);
		//debug
		console.log('Writing, "'+this.get('content')+'", at: ' + this.get('x') + ', ' + this.get('y'));
	}
});

var ImageObject = Backbone.Model.extend({
	defaults: {
		type: 'image',
		content: '',
		x: '',
		y: '',
		width: '',
		height: '',
		rotation: ''
	},
	initialize: function() {
		this.on("change:content", refreshCanvas);
	},
	draw: function() {
		var img = new Image();
		var imageObject = this;
		img.onload = function() {
			context.drawImage(img, imageObject.get('x'), imageObject.get('y'));
			//context.drawImage(img, sx, sy, swidth, sheight, x, y, width, height); // draw w/ clipping
		};
		img.src = this.get('content');
		//debug
		console.log('drawing image at: ' + this.get('x') + ', ' + this.get('y'));
	}
});


var TextEditView = Backbone.View.extend({
	initialize: function( attrs ) {
		this.options = attrs;
		this.render();
		$('select[name="colorpicker"]').simplecolorpicker({picker: true});
	},
	render: function() {
		var template = _.template($("#template-textEdit").html(), this.options);
		this.$el.append(template);

		this.$el.find('option[value="'+this.model.get('fontColor')+'"]').attr("selected", "selected");
		this.$el.find('option[value="'+this.model.get('fontSize')+'"]').attr("selected", "selected");

		$("#cb_canvasWrapper").unbind(); // whoa.. need this for some reason
		
		return this;
	},
	events: {
		"keyup .textInput": "updateText",
		"click .cb_addEditText": "falseHandler",
		"click .cb_close": "close",
		'change select[name="colorpicker"]': 'updateColor',
		'change select[name="fontsizepicker"]': 'updateFontsize'
	},
	updateText: function( event ) {
		console.log('updateText()');
		this.model.set('content', event.target.value);
	},
	updateColor: function( event ) {
		console.log('updateColor()');
		this.model.set('fontColor', event.target.value);
	},
	updateFontsize: function( event ) {
		console.log('updateFontsize()');
		this.model.set('fontSize', event.target.value);
	},
	falseHandler: function( event ) {
		console.log('falseHandler()');
		return false;
	},
	close: function( event ) {
		$('select[name="colorpicker"]').simplecolorpicker('destroy');
		this.$el.find('.cb_addEditText').remove();
//		$("#cb_canvasWrapper").bind('click', function( event ) {
//			mainMenuHandler(event);
//		});
//		$("#cb_canvasWrapper").bind('mousemove', function( event ) {
//			mousemoveHandler(event);
//		});
	}
});

var ImageEditView = Backbone.View.extend({
	initialize: function( attrs ) {
		this.options = attrs;
		this.render();
	},
	render: function() {
		var template = _.template($("#template-imageEdit").html(), this.options);
		this.$el.append(template);
		
		$("#cb_canvasWrapper").unbind();

		return this;
	},
	events: {
		"change input.filePicker": 'uploadImage',
		"click .cb_close": 'close'
	},
	uploadImage: function( event ) {
		console.log('uploadImage()');
		var reader = new FileReader(), rFilter = /^image\/(?:bmp|cis\-cod|gif|ief|jpeg|pipeg|png|svg\+xml|tiff|x\-cmu\-raster|x\-cmx|x\-icon|x\-portable\-anymap|x\-portable\-bitmap|x\-portable\-graymap|x\-portable\-pixmap|x\-rgb|x\-xbitmap|x\-xpixmap|x\-xwindowdump)$/i;
		var imageModel = this.model;
		reader.onload = function( event ) {
			//uploadAndResize()
			imageModel.set('content', event.target.result);
		};
		if ( !rFilter.test(event.target.files[0].type) ) {
			alert("You must select a valid image file!");
			return;
		}
		reader.readAsDataURL(event.target.files[0]);
	},
	falseHandler: function( event ) {
		console.log('falseHandler()');
		return false;
	},
	close: function( event ) {
		this.$el.find('.cb_addEditImage').remove();
//		$("#cb_canvasWrapper").bind('click', function( event ) {
//			mainMenuHandler(event);
//		});
//		$("#cb_canvasWrapper").bind('mousemove', function( event ) {
//			mousemoveHandler(event);
//		});
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
	$('#cb_circleMenu').css({'top': event.pageY - 50, 'left': event.pageX - 50});
	$('#cb_circleMenu').show();
	// save the coords of the initial click
	click.x = event.pageX - $("#cb_canvasWrapper").offset().left;
	click.y = event.pageY - $("#cb_canvasWrapper").offset().top;
	//debug
	console.log('wrapper clicked at: ' + click.x + ', ' + click.y);
};

var textEditHandler = function( event, text ) {
	if ( text === undefined ) {
		text = new TextObject({x: click.x, y: click.y});
		TextObjectCollection.add(text);
		//debug
		console.log('text added to textCollection at: ' + click.x + ', ' + click.y);
	} else {
		console.log('editing text object:');
		console.log(text);
	}
	var textEditor = new TextEditView({
		model: text,
		el: $("#cb_canvasWrapper"),
		top: text.get('y') + 10,
		left: text.get('x'),
		content: text.get('content')
	});
};
var imageEditHandler = function( event, image ) {
	if ( image === undefined ) {
		image = new ImageObject({x: click.x, y: click.y});
		ImageObjectCollection.add(image);
		//debug
		console.log('image added to imageCollection at: ' + click.x + ', ' + click.y);
	}
	var imageEditor = new ImageEditView({
		model: image,
		el: $("#cb_canvasWrapper"),
		top: event.pageY + 10,
		left: event.pageX
	});
};

function initClickHandlers() {
	$("canvas#canvas").on('click', function( event ) {
		mainMenuHandler(event);
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
					console.log( 'clicked ' + $(this).attr('data-cid') );
					var clickedObject;
					if ( $(this).attr('data-model') === 'TextObject' ) {
						clickedObject = TextObjectCollection.get($(this).attr('data-cid'));
					}
					if ( $(this).attr('data-model') === 'ImageObject' ) {
						clickedObject = ImageObjectCollection.get($(this).attr('data-cid'));
					}
					textEditHandler(event, clickedObject);
					return false;
				}
			}, ".cb_placeholder");

	$("#cb_addText").click(function( e ) {
		console.log('cb_addText clicked');
		// hide the menu
		$('#cb_circleMenu').hide();
		//$("#cb_canvasWrapper").unbind('click');
		textEditHandler(e);
		return false;
	});

	$("#cb_addImage").click(function( e ) {
		console.log('cb_addImage clicked');
		$('#cb_circleMenu').hide();
		//$("#cb_canvasWrapper").unbind('click');
		imageEditHandler(e);
		return false;
	});

	// hide the menu when close button clicked
	$("#cb_cancel").click(function( e ) {
		$('#cb_circleMenu').hide();
		return false;
	});
}

function clear() {
	//console.log('clear()');
	context.save();
	context.clearRect(0, 0, canvas.width, canvas.height);
	context.restore();
}

function refreshCanvas() {
	console.log('refreshCanvas');
	clear();
	//console.log(TextObjectCollection);
	TextObjectCollection.each(function( textObject ) {
		//console.log(textObject);
		textObject.write();
	});
	//console.log(TextObjectCollection);
	ImageObjectCollection.each(function( imageObject ) {
		//console.log(imageObject);
		imageObject.draw();
	});
}


function rotateAndWrite() {
	context.save();
	context.translate(newx, newy);
	context.rotate(-Math.PI / 2);
	context.textAlign = "center";
	context.fillText("Your Label Here", labelXposition, 0);
	context.restore();
}
