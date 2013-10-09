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
