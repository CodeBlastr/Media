// Filename: models/media
define([
  'underscore',
  'backbone'
], function(_, Backbone){
  var MediaModel = Backbone.Model.extend({
    defaults: {
      title: "",
      
    }
  });
  // Return the model for the module
  return MediaModel;
});