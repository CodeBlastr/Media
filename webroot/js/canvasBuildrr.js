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

var CanvasObjects = Backbone.Collection.extend({
	url: '/media/media/canvas',
	comparator: function(model) {
		return model.get("order");
	},
	// wipes the canvas clean
	clear: function() {
		context.save();
		context.clearRect(0, 0, canvas.width, canvas.height);
		context.restore();
	},
	// redraws each object in the collection
	refreshCanvas: function() {
		console.log(this);
		this.clear();
		this.each(function( canvasObject ) {
			console.log(canvasObject);
			canvasObject.draw();
		});
	},
	sync: function( method, collection, options ) {
		console.log('syncing CanvasObjectCollection');
		if ( method == "update" ) {
			// do not send the images up everytime we want to save
			collectionClone = collection.clone();
			collectionClone.each(function( clonedObject, index ) {
				if ( clonedObject.get('type') === 'image' ) {
					clonedObject.set('content', '');
					collectionClone[index] = clonedObject;
				}
			});
			return Backbone.sync( method, collectionClone, options );
		} else {
			return Backbone.sync( method, collection, options );
		}
	}
});
var CanvasObjectCollection = new CanvasObjects();

$("#saveCanvas").click(function(){
	var options = {};
	var method;
	if ( $(this).attr('data-saved') === 'false') {
		method = 'create';
	} else {
		method = 'update';
	}

	CanvasObjectCollection.sync(method, CanvasObjectCollection, options);
	$(this).attr('data-saved', 'true');
});
