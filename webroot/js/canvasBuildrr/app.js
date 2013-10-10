// Filename: app.js
define([
	'jquery',
	'underscore',
	'backbone',
	'views/media.items.view',
	'views/Canvas',
	'simplecolorpicker'
], function( $, _, Backbone, MediaItemsView, CanvasView ) {

	var initialize = function() {
		var mv = new MediaItemsView();
		var cv = new CanvasView();
	};

	return {
		initialize: initialize
	};
});
