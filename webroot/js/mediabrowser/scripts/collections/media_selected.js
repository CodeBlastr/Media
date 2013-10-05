define([
  'underscore',
  'backbone',
  'models/media'
], function(_, Backbone, MediaItem){
  var SelectedMediaCollection = Backbone.Collection.extend({
	 
	  model: MediaItem,
  
	  comparator: 'order'
  });
  
  return SelectedMediaCollection;
});