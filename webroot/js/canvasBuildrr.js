/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */

/**
 * set up the necessary pointers
 */
var element = $("#canvas");
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
		context.fillStyle = CanvasObjectCollection.get('backgroundColor');
		context.fillRect(0, 0, canvas.width, canvas.height);
		context.restore();
	},
	// redraws each object in the collection
	refreshCanvas: function() {
		this.sort();
		this.clear();
		console.log(this);
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
	},
	sync: function( method, collection, options ) {
		console.log('syncing CanvasObjectCollection');
		var options = {
				success: function(models, resp, xhr) {
					collection.reset();
					this.reload(models);
				}
		};
		if ( method == "update" ) {
			// do not send old images up everytime we want to save
			collectionClone = collection.clone();
			collectionClone.each(function( clonedObject, index ) {
				if ( clonedObject.get('type') === 'image' && clonedObject.get('id') ) {
					clonedObject.set('content', '');
					collectionClone.models[index] = clonedObject;
				}
			});
			console.log(collectionClone);
			return Backbone.sync( method, collectionClone, options );
		} else {
			return Backbone.sync( method, collection, options );
		}
	},
	reload: function(models) {
		console.log('reload');
		// config the save button
		$("#saveCanvas").attr('data-saved', 'true');
		
		// wipe the overlays
        $(".cb_placeholder").remove();
        
        // import them to their models
        models = jQuery.parseJSON(models);
        models.forEach(function(model, index){
        	console.log(model);
        	if (model.type === 'ImageObject' || model.type === 'screenshot') {
        		image = new ImageObject(model);
        		CanvasObjectCollection.get('collection').add(image);
        	}
        	if (model.type === 'TextObject') {
        		text = new TextObject(model);
        		CanvasObjectCollection.get('collection').add(text);
        	}
        });
        
        // render the models
        this.refreshCanvas();
	}
});
//var CanvasObjectCollection = new CanvasObjects();

var CollectionContainer = Backbone.Model.extend({
    defaults: {
    	collection: new CanvasObjects(),
    	//CanvasObjectCollection: new CanvasObjects(),
        backgroundColor: '#ffffff'
    },
    initialize: function() {
    	console.log('CollectionContainer init');
		this.on("change:backgroundColor", this.get('collection').refreshCanvas);
    },
    parse: function(response, options) {
        // update the inner collection
        this.get("collection").reset(response.CanvasObjectCollection);

        // this mightn't be necessary
        delete response.CanvasObjectCollection;

        return response;
    }
});

var CanvasObjectCollection = new CollectionContainer();


/**
 * BACKGROUND CONTROLS
 */
$("select[name='bgColorpicker']").change(function(){
//	console.log($(this).val());
	console.log(CanvasObjectCollection);
	CanvasObjectCollection.set('backgroundColor', $(this).val());
});


/**
 * SAVE BUTTON
 */
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
	console.log(CanvasObjectCollection);
	CanvasObjectCollection.each(function( canvasObject, index ) {
		console.log( canvasObject );
		if ( canvasObject.get('type') === 'screenshot' ) {
			console.log(canvasObject);
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

