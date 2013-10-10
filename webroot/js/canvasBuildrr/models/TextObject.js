var TextObject = Backbone.Model.extend({
	
	defaults: {
		type: 'TextObject',
		content: '',
		fontFamily: 'Arial',
		fontColor: '#333333',
		fontSize: 32,
		width: '',
		x: '',
		y: '',
		rotation: 0,
		scale: 0,
		order: 0,
		isEditable: true
	},
	
	url: '/media/media/canvas',
	
	initialize: function() {
		// init event listeners
		this.on("change:content", this.refresh)
			.on("change:order", this.refresh)
			.on("change:fontSize", this.refresh)
			.on("change:fontColor", this.refresh)
			.on("change:fontFamily", this.refresh)
			.on("change:y", this.refresh)
			.on("change:x", this.refresh)
			.on("change:rotation", this.refresh);
		// create a placeholder div for this new object on the canvas
		var placeholder = $('<div class="cb_placeholder" />');
		placeholder
				.attr('data-model', 'TextObject')
				.attr('data-cid', this.cid)
				.css('top', this.get('y') - this.get('fontSize'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('fontSize'))
				.append( '<div class="cb_ph_corner cb_ph_topRight btn btn-mini" title="Drag to Rotate; Double-Click to Reset."><i class="icon icon-refresh"></i></div>' );
		$("#cb_canvasWrapper").append(placeholder);
	},
	
	refresh: function() {
		AppModel.get('collection').refreshCanvas();
		// update the placeholder div
		var phTitle = ( this.get('isEditable') === true ) ? 'Click to Edit Text; Drag to Move Text.' : '';
		$("div[data-cid='"+this.cid+"']")
				.css('top', this.get('y') - this.get('fontSize'))
				.css('left', this.get('x'))
				.css('width', this.get('width'))
				.css('height', this.get('fontSize'))
				.attr('title', phTitle);
	},
	
	draw: function() {
		context.save();

		// set options
		context.lineWidth = 1;
		context.fillStyle = this.get('fontColor');
		context.lineStyle = this.get('fontColor');
		context.font = this.get('fontSize') + 'px ' + this.get('fontFamily');

		// measure width
		this.set({width: context.measureText(this.get('content')).width}, {silent:true});

		context.translate(
			this.get('x') + (this.get('width') / 2),
			this.get('y') - (this.get('fontSize') / 2)
		);
		context.rotate(this.get('rotation') * Math.PI / 180);

		// rotate the overlay container
		$("div[data-cid='"+this.cid+"']").css('transform', 'rotate('+this.get('rotation')+'deg)');

		// draw out
		context.fillText(
			this.get('content'),
			0 - this.get('width') / 2,
			this.get('fontSize') / 2
		);
		console.log('Writing, "'+this.get('content')+'", at: (' + (0 - this.get('width') / 2) + ', ' + (this.get('fontSize') / 2) + '), rotated ' + this.get('rotation') + 'deg');

		context.restore();	
		//return true;	
	}
});
