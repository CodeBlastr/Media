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
