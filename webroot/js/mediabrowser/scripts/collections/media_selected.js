define([
  'underscore',
  'backbone',
  'models/media',
  'text!templates/media_selected.html',
  'handlebars',
  'views/media.item.view',
], function(_, Backbone, MediaItem, SelectedTemplate, Handlebars, MediaItemView){
  var SelectedMediaCollection = Backbone.Collection.extend({
	  model: MediaItem,
	  comparator: 'order',
	  
	  selectedTemplate: Handlebars.compile(SelectedTemplate),
		  
	  renderSelected: function() {
		  	$(document).find('#mediaSelected').html('');
		  	var that = this;
	    	var html = '';
	    	var i = 0;
			this.forEach(function(model, index){
				if(model.get('order') == false || model.get('order') != i) { model.set('order', i, { silent: true } ); };
				i++;
				model.set('audio', model.isAudio());
				model.set('image', model.isImage());
				model.set('doc', model.isDoc());
				model.set('video', model.isVideo());
				var renderObj = ({cid: model.cid, model: model.toJSON(), wrapperclass: that.wrapperclass, index: index});
				html = $(that.selectedTemplate(renderObj));
				html.find('.selected-actions').find('a').bind('click', function(e) {
					e.preventDefault();
					that.modifySelectModel(e);
				});
				$(document).find('#mediaSelected').append(html);
			});
	  },
  
	  modifySelectModel: function(e) {
		  	var selel = $(e.currentTarget);
		  	var that = this;
		  	var model = this.get(selel.parent().data('cid'));
		  	var order = selel.parent().data('order');
		  	if(selel.hasClass('removeSelected')) {
		  		that.remove(model);
		  		model.set('selected', false);
		  	}
		  	if(selel.hasClass('orderup')) {
		  		that.forEach(function(model, index){
		  			if(index == order-1) {
		  				model.set('order', order, { silent: true } );
		  			}
		  			if(index == order) {
		  				model.set('order', order-1, { silent: true } );
		  			}
		  		});
		  		that.sort();
		  		this.renderSelected();
		  	}
		  	if(selel.hasClass('orderdown')) {
		  		that.forEach(function(model, index){
		  			if(index == order+1) {
		  				model.set('order', order, { silent: true } );
		  			}
		  			if(index == order) {
		  				model.set('order', order+1, { silent: true } );
		  			}
		  		});
		  		that.sort();
		  		this.renderSelected();
		  	}
		  	if(selel.hasClass('makethumbnail')) {
		  		Backbone.trigger('mediaThumbSelect', {model: model});
		  	}
		  	
	  },
  });
  
  return SelectedMediaCollection;
});