var ImageEditView = Backbone.View.extend({
	initialize: function( attrs ) {
		this.options = attrs;
		this.render();
	},
	render: function() {
		var template = _.template($("#template-imageEdit").html(), this.options);
		this.$el.append(template);

		$("#cb_canvasWrapper").parent().unbind();

		return this;
	},
	events: {
		"change input.filePicker": 'uploadImage',
		"click .cb_up": "orderUp",
		"click .cb_down": "orderDown",
		"click .cb_close": 'close'
	},
	orderUp: function( event ) {
		this.model.set('order', this.model.get('order') + 1);
	},
	orderDown: function( event ) {
		this.model.set('order', this.model.get('order') - 1);
	},
	uploadImage: function( event ) {
		console.log('uploadImage()');
		$(".filePicker").attr('disabled', 'disabled');
		$(".cb_addEditImage").fadeOut(5000);
		var reader = new FileReader(), rFilter = /^image\/(?:bmp|cis\-cod|gif|ief|jpeg|pipeg|png|svg\+xml|tiff|x\-cmu\-raster|x\-cmx|x\-icon|x\-portable\-anymap|x\-portable\-bitmap|x\-portable\-graymap|x\-portable\-pixmap|x\-rgb|x\-xbitmap|x\-xpixmap|x\-xwindowdump)$/i;
		var imageModel = this.model;
		reader.onload = function( event ) {
			// var image = new Image();
			// image.src = event.target.result;
			// image.onload = function() {
				// imageModel.set('image', image);
				// imageModel.set('loaded', true);
				// imageModel.set('height', this.height);
				// imageModel.set('width', this.width);
				// imageModel.set('aspectRatio', this.width / this.height);
			// };
			imageModel.set('content', event.target.result);
		};
		if ( !rFilter.test(event.target.files[0].type) ) {
			alert("You must select a valid image file!");
			return;
		}
		reader.readAsDataURL(event.target.files[0]);
		this.close();
	},
	falseHandler: function( event ) {
		return false;
	},
	close: function( event ) {
		this.$el.find('.cb_addEditImage').remove();
	}
});

var imageEditHandler = function( event, image ) {
	if ( image === undefined ) {
		image = new ImageObject({x: click.x, y: click.y});
		AppModel.get('collection').add(image);
		//debug
		console.log('image added to AppModel.collection at: ' + click.x + ', ' + click.y);
	}
	var imageEditor = new ImageEditView({
		model: image,
		el: $("#cb_canvasWrapper").parent(),
		top: image.get('y') + $("#cb_canvasWrapper").offset().top + 10,
		left: image.get('x') + $("#cb_canvasWrapper").offset().left,
		content: image.get('content')
	});
};
