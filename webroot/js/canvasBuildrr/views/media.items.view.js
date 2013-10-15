define([
  'jquery',
  'underscore',
  'backbone',
  'handlebars',
  'text!templates/media_items.html',
  'collections/media.items',
  'collections/media_selected',
  'views/media.item.view',
  'jqueryform',
  'text!templates/media_selected.html',
], function($, _, Backbone, 
		Handlebars, 
		MediaItemsTemplate, 
		MediaItems, 
		SelectedMediaCollection, 
		MediaItemView, 
		jqueryForm, 
		SelectedTemplate
){
  var MediaItemsView = Backbone.View.extend({
	  
	//Default filter params
	params: {limit: 10},
	
	events: {
		'change .filter-select'	: 'changefilterIndex',
		'click .upload'			: 'showUploadForm',
	},
	
	template: Handlebars.compile(MediaItemsTemplate),
	selectedTemplate: Handlebars.compile(SelectedTemplate),
	
	collection: new MediaItems(),
	selectedCollection: new SelectedMediaCollection(),
    
	el: $('#mediaBrowser'),
	  
	initialize: function(args) {
		if(typeof wrapperclass != 'undefined') {
			this.wrapperclass = wrapperclass;
		}else {
			this.wrapperclass = '';
		}
		
		Handlebars.registerHelper('if_gt', function(context, options) {
	        if (context > options.hash.compare)
	            return options.fn(this);
	        return options.inverse(this);
	    });
		
		this.showLoading();
		//Setup Event Listeners
		this.on('childCreation', this.render);
		this.on('renderView', this.filterIndex);
		this.on('renderEvent', this.hideLoading);
		this.listenTo(Backbone, 'updateSelected', this.selectModel);
		this.listenTo(Backbone, 'removeSelected', this.removeSelectModel);
		//this.listenTo(this.collection, 'change', this.filterIndex);
		this.listenTo(this.selectedCollection, 'remove', this.renderSelected);
		this.listenTo(this.selectedCollection, 'remove', this.filterIndex);
		this.listenTo(this.selectedCollection, 'change', this.renderSelected);
		//initialize the collection
		if(typeof selecteditems != 'undefined') {
			this.selectedCollection.add(selecteditems);
			this.renderSelected();
		}
		this.filterIndex();
	},
	
    render: function(){
      var html = this.template();
      this.$el.html( html );
      //Select Defaults
      _.each(this.params, function (value, type) {
    	  $('select[data-filter='+type+']').val(value);
      });
      
      //Upload Form
      var that = this;
      var uploadForm = this.$el.find('#MediaAddForm');
      uploadForm.ajaxForm({
		    beforeSend: function() {
		       that.showLoading();
		    },
		    uploadProgress: function(event, position, total, percentComplete) {
		        var percentVal = percentComplete + '%';
		        that.$el.find('.percent').html(percentVal);
		        console.log(percentVal);
		    },
		    success: function(data) {
		    	that.closeUploadForm();
		    	uploadForm.clearForm();
		    	console.log(data);
		    	that.collection.add(data);
		    	that.trigger('renderView');
		    },
		    error: function(data) {
		    	console.log(data);
		    },
			complete: function(xhr) {
				that.hideLoading();
			}
      }); 
      
      var that = this;
      _.each(this.childViews, function(view) {
    	  view = view.render();
    	  that.$el.find('.media-container').append(view.$el);
      });
      this.trigger('renderEvent');
      return this;
    },
    
    createChildViews: function() {
    	this.childViews = {};
		var that = this;
		this.selectedCollection.forEach(function(model){
			cmodel = that.collection.get(model.get('id'));
			if(typeof cmodel != 'undefined') {
				cmodel.set('selected', true);
			}
		});
		that.collection.forEach(function(model){
			that.childViews[model.cid] = new MediaItemView({model: model});
		});
		this.trigger('childCreation');
    },
    
    changefilterIndex: function (e) {
    	e.preventDefault();
    	var paramtype = $(e.currentTarget).data('filter');
    	var value = $(e.currentTarget).val();
    	var params = {};
    	params[paramtype] = value;
    	this.filterIndex(params);
    },
    
    filterIndex: function (args) {
    	args = typeof args !== 'undefined' ? args : {};
    	var params = this.params;
    	if(args.type) {
    		params.type = args.type;
    	}
    	if(args.limit) {
    		params.limit = args.limit;
    	}
    	this.params = params;
    	var that = this;
		this.collection.fetch({
			success: function() {
				that.createChildViews();
				return true;
			},
			reset: true,
			data: params
		});
    },
    
    showUploadForm: function(e) {
    	e.preventDefault();
    	this.$el.find('#mediaUploadForm').css('display', 'block');
    },
    
    closeUploadForm: function(e) {
    	this.$el.find('#mediaUploadForm').css('display', 'none');
    },
    
    showLoading: function(e) {
    	this.bgcover = $('<div class="bgcover"></div>');
    	this.bgcover.css({
    		position: 'absolute',
    		left: 0,
    		top: 0,
    		zIndex: 9998,
    		width: $(window).width()+'px',
    		height: $(window).height()+'px',
    		background: 'rgba(0, 0, 0, .5)'
    	});
    	this.bgcover.appendTo('body');
    	this.loader = $('<div class="loader" style="display:none;"><img src="/Media/ajax-loader.gif" /></div>');
    	this.loader.css({
    		position: 'absolute',
    		left: ($(window).width()/2) - this.loader.width()/2,
    		top:  $(window).height()/2 - this.loader.height()/2,
    		zIndex: 9999,
    		display: 'block',
    	});
    	this.loader.appendTo('body');
    },
    
    hideLoading: function(e) {
    	this.bgcover.remove();
    	this.loader.remove();
    },
    
    selectModel: function(model) {
    	this.selectedCollection.add(model);
    },
    
    removeSelectModel: function(model) {
    	this.selectedCollection.remove(model);
    },
    
    modifySelectModel: function(e) {
    	var selel = $(e.currentTarget);
    	var that = this;
    	var model = this.selectedCollection.get(selel.parent().data('cid'));
    	var order = selel.parent().data('order');
    	if(selel.hasClass('removeSelected')) {
    		that.selectedCollection.remove(model);
    		model.set('selected', false);
    	}
    	if(selel.hasClass('orderup')) {
    		console.log(order);
    		that.selectedCollection.forEach(function(model, index){
    			console.log(index);
    			console.log(model.get('order'));
    			if(index == order-1) {
    				model.set('order', order, { silent: true } );
    			}
    			if(index == order) {
    				model.set('order', order-1, { silent: true } );
    			}
    			console.log(model.get('order'));
    			console.log('');
    		});
    		that.selectedCollection.sort();
    		this.renderSelected();
    	}
    	if(selel.hasClass('orderdown')) {
    		that.selectedCollection.forEach(function(model, index){
    			if(index == order+1) {
    				model.set('order', order, { silent: true } );
    			}
    			if(index == order) {
    				model.set('order', order+1, { silent: true } );
    			}
    			console.log(model.get('order'));
    		});
    		console.log(that.selectedCollection);
    		that.selectedCollection.sort();
    		this.renderSelected();
    	}
    	
    },
    
    renderSelected: function() {
    	$('#mediaSelected').html('');
    	var that = this;
    	var html = '';
    	var i = 0;
		this.selectedCollection.forEach(function(model, index){
			if(model.get('order') == false || model.get('order') != i) { model.set('order', i, { silent: true } ); };
			i++;
			var view = new MediaItemView({model: model});
			view.render();
			view.$el.find('.actions').remove();
			var innerhtml = view.$el.html();
			var renderObj = ({cid: model.cid, model: model.toJSON(), innerhtml: innerhtml, wrapperclass: that.wrapperclass, index: index});
			html = $(that.selectedTemplate(renderObj));
			html.find('.selected-actions').find('a').bind('click', function(e) {
				e.preventDefault();
				that.modifySelectModel(e);
			});
			$('#mediaSelected').append(html);
		});
		
    }
    
  });
  
  return MediaItemsView;
});