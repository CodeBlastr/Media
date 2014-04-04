// Filename: app.js
define([
  'jquery',
  'underscore',
  'backbone',
  'views/media_items_view',
], function($, _, Backbone, MediaItemsView){
   
  var initialize = function(){
     var mv = new MediaItemsView();
  };

  return {
    initialize: initialize
  };
});