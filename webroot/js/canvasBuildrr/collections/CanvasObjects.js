/**
 * The collection of our ImageObjects and TextObjects
 */
var CanvasObjects = Backbone.Collection.extend({
	
	url: '/media/media/canvas/collection:true',
	
	comparator: function(model) {
		return model.get("order");
	},
	
	initialize: function() {
		this.on("reset", this.afterReset);
		this.bind("remove", this.onModelRemoved, this);
	},
	
	onModelRemoved: function(model, collection, options) {
		$("div[data-cid='"+model.cid+"']").remove();
		this.refreshCanvas();
	},
	
	sync: function(){},
	
	// wipes the canvas clean
	clear: function() {
		context.save();
		context.fillStyle = (AppModel !== undefined) ? AppModel.get('backgroundColor') : '#ffffff';
		context.fillRect(0, 0, canvas.width, canvas.height);
		context.restore();
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
					if (i > 100000) {
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
