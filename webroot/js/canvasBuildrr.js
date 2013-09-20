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
	url: '/media/media/canvas/collection:true',
	comparator: function(model) {
		return model.get("order");
	},
	initialize: function() {
		this.on("reset", this.afterReset);
	},
	// wipes the canvas clean
	clear: function() {
		context.save();
		context.fillStyle = 'rgb(255,255,255)';
		context.fillRect(0, 0, canvas.width, canvas.height);
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
//		console.log(JSON.stringify(collection));
		var options = {
				success: function(models, resp, xhr) {
//					console.log(jQuery.parseJSON(models));'
					// @todo Need to recreate proper models, instead of overwriting with data-only models
					collection.reset(jQuery.parseJSON(models));
				}
		};
		if ( method == "update" ) {
			// do not send old images up everytime we want to save
			collectionClone = collection.clone();
			collectionClone.each(function( clonedObject, index ) {
				if ( clonedObject.get('type') === 'image' && clonedObject.get('id') ) {
					clonedObject.set('content', '');
					collectionClone[index] = clonedObject;
				}
			});
			return Backbone.sync( method, collectionClone, options );
		} else {
			return Backbone.sync( method, collection, options );
		}
	},
	afterReset: function(xThis, options) {
		//xThis.models;
		//options.previousModels
		console.log(xThis.models);
		console.log(options.previousModels);
		xThis.models.forEach(function(model, index){
			options.previousModels.forEach(function(pModel, pIndex){
				if (model.get('x') === pModel.get('x') && model.get('y') === pModel.get('y')) {
					// set the id of the div with pModel's cid
					$('div[data-cid="'+pModel.cid+'"]').attr('data-id', model.get('id'));
					// assign the new cid's
					$('div[data-cid="'+pModel.cid+'"]').attr('data-cid', model.cid);
				}
			});
		});
	}
});
var CanvasObjectCollection = new CanvasObjects();

$("#saveCanvas").click(function(){
	var options = {};
	var method;
	if ( $(this).attr('data-saved') === 'false' ) {
		method = 'create';
	} else {
		method = 'update';
	}

	// update screenshot
	var hasScreenshot = false;
	CanvasObjectCollection.each(function( canvasObject, index ) {
		if ( canvasObject.get('type') === 'screenshot' ) {
			hasScreenshot = true;
			canvasObject.set('content', canvas.toDataURL());
			CanvasObjectCollection[index] = canvasObject;
		}
	});
	if ( hasScreenshot === false ) {
		image = new ImageObject({
			'type': 'screenshot',
			'content': canvas.toDataURL()
			});
		CanvasObjectCollection.add(image);
	}
	
	CanvasObjectCollection.sync(method, CanvasObjectCollection, options);
	$(this).attr('data-saved', 'true');
});
