/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */


/**
 * set up the necessary pointers & variables
 */
var element = $("#canvas");
var canvas = document.getElementById(element.attr('id'));
var context = canvas.getContext("2d");
var click = {x: '', y: ''};


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


/**
 * A model to hold the collection, so that the collection can have attributes 
 */
var CollectionContainer = Backbone.Model.extend({
	
	url: '/media/media/canvas/collection:true',
	
    defaults: {
    	collection: new CanvasObjects(),
        backgroundColor: '#ffffff'
    },
    
    initialize: function() {
		this.on("change:backgroundColor", function(){
			this.get('collection').refreshCanvas();
		});
    },
    
    sync: function( method, model, options ) {
    	// create a clone that does not have the JS Image Objects in it
    	console.log(model);
    	model.attributes.collection.models.forEach(function(aModel, index) {
    		model.attributes.collection.models[index].attributes.image = null;
    	});
    	var modelData = JSON.stringify(model.toJSON());
    	console.log(modelData);
		$.post(this.url, modelData)
			.done(function(data) {
				console.log(data);
				return false;
			});
    },
    
	reload: function(models) {
		console.log('reload');
		
		// config the save button
		$("#saveCanvas").attr('data-saved', 'true');
		
		// wipe the overlays
        $(".cb_placeholder").remove();
        
        // import the models
        models = jQuery.parseJSON(models);
        AppModel = new CollectionContainer(models);
        AppModel.set('collection', new CanvasObjects);
        models.collection.forEach(function(model, index){
        	if (model.type === 'ImageObject' || model.type === 'screenshot') {
        		image = new ImageObject(model);
        		AppModel.get('collection').add(image);
        	}
        	if (model.type === 'TextObject') {
        		text = new TextObject(model);
        		AppModel.get('collection').add(text);
        	}
        });
        
        // render the models
        AppModel.get('collection').refreshCanvas();
        
        return this;
	}
});

var AppModel = new CollectionContainer();


/**
 * LISTEN FOR SELECTED IMAGES
 */
document.addEventListener('mediaBrowserMediaSelected', function (e) {
	var model = e.detail;
	var image = new ImageObject({
		'type': 'ImageObject',
		'content': "/theme/default/media/images/" + model.attributes.filename + "." + model.attributes.extension
	});
	var collection = AppModel.get('collection');	
	collection.add(image);
}, false);

/**
 * LISTEN FOR UN-SELECTED IMAGES
 */
document.addEventListener('mediaBrowserMediaUnSelected', function (e) {
	var model = e.detail;
	var collection = AppModel.get('collection');
	var relativeFilePath = "/theme/default/media/images/" + model.attributes.filename + "." + model.attributes.extension;
	var deselectedObject = collection.findWhere({content: relativeFilePath});
	collection.remove( deselectedObject );
}, false);


/**
 * BACKGROUND CONTROLS
 */
$("select[name='bgColorpicker']").change(function(){
	AppModel.set('backgroundColor', $(this).val());
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
	AppModel.get('collection').each(function( canvasObject, index ) {
		if ( canvasObject.get('type') === 'screenshot' ) {
			hasScreenshot = true;
			canvasObject.set('content', canvas.toDataURL());
			AppModel.get('collection')[index] = canvasObject;
		}
	});
	if ( hasScreenshot === false ) {
		image = new ImageObject({
			'type': 'screenshot',
			'content': canvas.toDataURL(),
			'isEditable': false
			});
		AppModel.get('collection').add(image);
	}

	AppModel.sync(method, AppModel, options);
	$(this).attr('data-saved', 'true');
});

