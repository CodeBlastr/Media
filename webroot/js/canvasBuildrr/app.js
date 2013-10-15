// Filename: app.js
define([
	'jquery',
	'underscore',
	'backbone',
	'views/media.items.view',
	'views/Canvas',
	'models/CollectionContainer',
	'simplecolorpicker'
], function( $, _, Backbone, MediaItemsView, CanvasView, CollectionContainer ) {

	var initialize = function() {
		var mv = new MediaItemsView();
		Backbone.AppModel = new CollectionContainer();
		var cv = new CanvasView();
	};

	return {
		initialize: initialize
	};
});
