define([
  'jquery',
  'underscore',
  'backbone',
  'handlebars',
  'models/media',
  'popover',
  'text!templates/media_item_image.html',
  'text!templates/media_item_audio.html'
], function($, 
			_, 
			Backbone, 
			Handlebars, 
			MediaItem, 
			Popover,
			ImageTemplate,
			AudioTemplate
){
  var MediaItemView = Backbone.View.extend({
  
	initialize: function(args) {
		this.on('renderEvent', this.afterRender);
		this.listenTo(this.model, 'change', this.render);
	},
	
	imageTemplate: Handlebars.compile(ImageTemplate),
	audioTemplate: Handlebars.compile(AudioTemplate),
	//videoTemplate: Handlebars.compile(MediaItemsTemplate),
	//docTemplate: Handlebars.compile(MediaItemsTemplate),
    
	tagName: 'div',
    
	events: {
		'submit .editor': 'editModel',
		'click .delete' : 'deleteModel',
		'change .selected' : 'selectModel',
	},
	
    render: function(){
      this.$el.html(this.renderItem());
      this.trigger('renderEvent');
      return this;
    },
    
    afterRender: function() {
    	this.$el.find('.edit').popover();
    	this.$el.find('.edit').on('click', function(e) {e.preventDefault(); return true;});
    	return this;
    },
    
    editModel: function(e) {
    	e.preventDefault();
    	this.model.set({
    		title: $(e.currentTarget).find('.title-input').val(),
    		description: $(e.currentTarget).find('.description-input').val()
    	});
    	this.model.save();
    	return this;
    },
    
    deleteModel: function(e) {
    	e.preventDefault();
    	if (confirm('Are you sure you want to delete '+this.model.get('title')))
        {
    		var that = this;
    		this.model.destroy({
    			success: function(data) {
    				that.$el.remove();
    			}
    		});
    		return this;
        }
    },
    
    renderItem: function() {
    	html = '';
    	var renderObj = {model: this.model.attributes, selectable: this.model.selectable()};
    	switch(this.model.get('type')) {
	      case 'images':
	    	  html = this.imageTemplate(renderObj);
	    	  break;
	      case 'audio':
	    	  html = this.audioTemplate(renderObj);
	    	  break;
    	}
    	return html;
    },
    
    selectModel: function(e) {
    	var that = this;
    	if($(e.currentTarget).is(':checked')) {
    		that.model.set('selected', true);
    		var event = new CustomEvent('mediaBrowserMediaSelected', {'detail': that.model});
    		document.dispatchEvent(event);
    	}else {
    		that.model.set('selected', false);
    		that.model.trigger('UnSelectModel');
    		var event = new CustomEvent('mediaBrowserMediaUnSelected', {'detail': that.model});
    		document.dispatchEvent(event);
    	}
    	return this;
    },
  	
  });
  
  return MediaItemView;
});