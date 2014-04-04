// these are like adding "include script tags"
define([
  'jquery',
  'underscore',
  'backbone',
  'handlebars',
  'models/media',
  'text!templates/media_items.html',
  'collections/media_items',
  'collections/media_selected',
  'views/media_item_view',
  'jqueryform',
  'text!templates/media_thumbnail_select.html',
  'text!templates/media_upload.html',
], function($, _, Backbone, 
		Handlebars, // object
		Media,
		MediaItemsTemplate, 
		MediaItems, 
		SelectedMediaCollection, 
		MediaItemView, 
		jqueryForm, 
		SelectedThumbnail,
		UploadTemplate
){
  var MediaItemsView = Backbone.View.extend({
	  
	//Default filter params
	params: {limit: 500},
	
	events: {
		'click .filter-select a'	: 'changefilterIndex',
		'click .show-upload'		: 'showUploadForm',
	},
	
	template: Handlebars.compile(MediaItemsTemplate),
	selectedThumbnail: Handlebars.compile(SelectedThumbnail),
	uploadtemplate: Handlebars.compile(UploadTemplate),
	
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
		this.listenTo(Backbone, 'mediaThumbSelect', this.addThumbnail);
		//this.listenTo(this.collection, 'change', this.filterIndex);
		this.listenTo(this.collection, 'remove', this.removeSelectModel);
		this.listenTo(this.selectedCollection, 'remove', this.renderSelected);
		this.listenTo(this.selectedCollection, 'add', this.renderSelected);
		
		//initialize the collection
		this.selectedCollection.wrapperclass = this.wrapperclass;
		if(typeof selecteditems != 'undefined') {
			this.selectedCollection.add(selecteditems);
			//this.renderSelected();
		}
		if(typeof thumbnail != 'undefined') {
			if(thumbnail) {
				thumbnail = new Media(thumbnail);
				args = {model: thumbnail};
				this.addThumbnail(args);
			}
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
    	var value = $(e.currentTarget).data('type');
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
		if(this.$el.find('.upload-container').html() === '') {
			this.$el.find('.upload-container').html(this.uploadtemplate());
		}
    	this.$el.find('.upload-container').show();
    	this.$el.find('.media-container').hide();
        //Upload Form
        var that = this;
        
        // standard file upload form
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
        
        var youtubeForm = this.$el.find('#YouTubeAddForm');
        youtubeForm.ajaxForm({
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
		    	youtubeForm.clearForm();
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
    },
    
    closeUploadForm: function(e) {
    	this.$el.find('.upload-container').hide();
    	this.$el.find('.media-container').show();
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
    
    
    renderSelected: function() {
    	this.selectedCollection.renderSelected();
    },
    
    addThumbnail: function(args) {
    	var model = args.model;
    	this.thumbnail = model;
    	$('#mediaThumbnail').html('');
    	var that = this;
    	var html = '';
		var view = new MediaItemView({model: model});
		view.render();
		view.$el.find('.actions').remove();
		var innerhtml = view.$el.html();
		var renderObj = ({cid: model.cid, model: model.toJSON(), innerhtml: innerhtml, wrapperclass: that.wrapperclass, index: this.selectedCollection.length });
		html = $(this.selectedThumbnail(renderObj));
		$('#mediaThumbnail').html(html);
		$('#mediaThumbnail').on('click', '.removeThumbail', this.removeThumbnail);
    },
    
    removeThumbnail: function(e) {
    	e.preventDefault();
    	Backbone.trigger('mediaThumbRemove', {model: this.model});
    	$('#mediaThumbnail').html('No Thumbnail Selected');
    }
    
  });
  
  return MediaItemsView;
});