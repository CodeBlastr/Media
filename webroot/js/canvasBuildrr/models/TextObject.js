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
		this.on("change:y", this.refresh);
		this.on("change:x", this.refresh);
		// create a placeholder div for this new object on the canvas
		var placeholder = $('<div class="cb_placeholder" />');
		placeholder
				.attr('data-model', 'TextObject')
				.attr('data-cid', this.cid)
				.css('top', this.get('y'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('fontSize'))
				.append( $('<div class="cb_ph_corner cb_ph_bottomLeft" />') )
				.append( $('<div class="cb_ph_corner cb_ph_bottomRight" />') )
				.append( $('<div class="cb_ph_corner cb_ph_topLeft" />') )
				.append( $('<div class="cb_ph_corner cb_ph_topRight" />') );
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
		//console.log('Writing, "'+this.get('content')+'", at: ' + this.get('x') + ', ' + this.get('y'));
	}
});
