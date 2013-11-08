// Filename: app.js
define([
  'jquery',
  'underscore',
  'backbone',
  'views/media.items.view',
], function($, _, Backbone, MediaItemsView){
  $.noConflict( true );
  var initialize = function(){
     var mv = new MediaItemsView();
  };

  return {
    initialize: initialize
  };
});