// Filename: models/CollectionContainer
define([
	'jquery',
	'underscore',
	'backbone',
	'collections/CanvasObjects',
	'models/TextObject',
	'models/ImageObject'
], function( $, _, Backbone, CanvasObjects, TextObject, ImageObject ) {
	/**
	 * A model to hold the collection, so that the collection can have attributes 
	 */
	var CollectionContainer = Backbone.Model.extend({
		
		url: '/media/media/canvas/',
		
		defaults: {
			collection: new CanvasObjects(),
			backgroundColor: '#ffffff'
		},
		
		initialize: function() {
			this.on("change:backgroundColor", function() {
				this.get('collection').refreshCanvas();
			});

			console.log('CC init');
		},
		
		sync: function( method, model, options ) {
			// remove the Image DOM Objects
			model.attributes.collection.models.forEach(function( aModel, index ) {
				model.attributes.collection.models[index].attributes.image = null;
			});
			var modelData = JSON.stringify( model.toJSON() );
			$.post(model.url, {data: modelData})
				.done(function( data ) {
					return false;
				});
		},
		
		reload: function( models ) {
			console.log('reload');

			// config the save button
			$("#saveCanvas").attr('data-saved', 'true');

			// wipe the overlays
			$(".cb_placeholder").remove();

			// import the models
			models = jQuery.parseJSON(models);
			Backbone.AppModel = new CollectionContainer(models);
			Backbone.AppModel.set('collection', new CanvasObjects);
			models.collection.forEach(function( model, index ) {
				if ( model.type === 'ImageObject' || model.type === 'screenshot' ) {
					image = new ImageObject(model);
					Backbone.AppModel.get('collection').add(image);
				}
				if ( model.type === 'TextObject' ) {
					text = new TextObject(model);
					Backbone.AppModel.get('collection').add(text);
				}
			});

		}
	});

	return CollectionContainer;
});
