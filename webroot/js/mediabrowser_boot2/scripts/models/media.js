// Filename: models/media
define([
  'underscore',
  'backbone'
], function(_, Backbone){

  var MediaModel = Backbone.Model.extend({
    defaults: {
      title: '',
      description: '',
      filename: '',
      extension: '',
      data: '',
      order: false,
      selected: false
    },
    
    selectable: function(){
    	if(typeof selectable != 'undefined') {
    		return selectable;
    	}
    	return false;
    },
    
    updateSelected: function() {
    	var model = this;
    	if(this.get('selected')) {
     		Backbone.trigger('updateSelected', model);
    	}else {
    		Backbone.trigger('removeSelected', model);
    	}
    },
  
	initialize: function(){
		this.on('change:selected', this.updateSelected);
	},
    
    isAudio: function() {
    	if(this.get('type') == 'audio') {
    		return true;
    	}
    	return false;
    },
	isImage: function() {
    	if(this.get('type') == 'images') {
    		return true;
    	}
    	return false;
	}
  
  });
  // Return the model for the module
  return MediaModel;
});