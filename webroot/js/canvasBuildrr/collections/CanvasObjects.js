define([
	'jquery',
	'underscore',
	'backbone'
], function( $, _, Backbone ) {
	/**
	 * The collection of our ImageObjects and TextObjects
	 */
	var CanvasObjects = Backbone.Collection.extend({
		
		url: '/media/media/canvas/collection:true',
		
		comparator: function( model ) {
			return model.get("order");
		},
		
		initialize: function() {
			console.log('CO init');
			this.on("reset", this.afterReset);
			this.bind("remove", this.onModelRemoved, this);
		},
		
		onModelRemoved: function( model, collection, options ) {
			$("div[data-cid='" + model.cid + "']").remove();
			this.refreshCanvas();
		},
		
		sync: function() {
		},
		
		// wipes the canvas clean
		clear: function() {
			Backbone.context.save();
			Backbone.context.fillStyle = ( Backbone.AppModel !== undefined ) ? Backbone.AppModel.get('backgroundColor') : '#ffffff';
			Backbone.context.fillRect(0, 0, Backbone.canvas.width, Backbone.canvas.height);
			Backbone.context.restore();
			return this;
		},
		
		// redraws each object in the collection
		refreshCanvas: function() {
			this.sort();
			this.clear();
			this.each(function( canvasObject ) {
				if ( canvasObject.get('type') === 'ImageObject' ) {
					var isLoaded = canvasObject.get('loaded');
					var i = 0;
					while ( isLoaded === false ) {
						console.log('waiting...');
						isLoaded = canvasObject.get('loaded');
						i++;
						if ( i > 100000 ) {
							alert('Unable to load image.  Please refresh.');
							return false;
						}
					}
				}
				canvasObject.draw();
			});
			return this;
		}
	});

	return CanvasObjects;
});
