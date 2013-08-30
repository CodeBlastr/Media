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
		this.on("change:content", this.refresh);
		this.on("change:x", this.refresh);
		this.on("change:y", this.refresh);
		this.on("change:width", this.refresh);
		this.on("change:height", this.refresh);
		this.on("change:rotation", this.refresh);
		// create a placeholder div for this new object on the canvas
		var placeholder = $('<div class="cb_placeholder" />');
		placeholder
				.attr('data-model', 'ImageObject')
				.attr('data-cid', this.cid)
				.css('top', this.get('y'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('height'));
		$("#cb_canvasWrapper").append(placeholder);
	},
	refresh: function() {
		refreshCanvas();
		// update the placeholder div
		$("div[data-cid='"+this.cid+"']")
				.css('top', this.get('y'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('height'));
	},
	draw: function() {
		var img = new Image();
		var imageObject = this;
		img.onload = function() {
			var width = ( imageObject.get('width') === '' ) ? null : imageObject.get('width');
			var height = ( imageObject.get('height') === '' ) ? null : imageObject.get('height');
			context.drawImage(img, imageObject.get('x'), imageObject.get('y'), width, height);
			//context.drawImage(img, sx, sy, swidth, sheight, x, y, width, height); // draw w/ clipping
		};
		img.src = this.get('content');
		//debug
		console.log('drawing image at: ' + this.get('x') + ', ' + this.get('y'));
	}
});
