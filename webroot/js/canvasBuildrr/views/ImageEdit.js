// Filename: views/ImageEdit
define([
  'jquery',
  'underscore',
  'backbone'
], function($, _, Backbone ){
		
	var ImageEditView = Backbone.View.extend({
		
		initialize: function( attrs ) {
			this.options = attrs;
			this.render();
		},
		
		render: function() {
			var template = _.template($("#template-imageEdit").html(), this.options);
			this.$el.append(template);
	
			$("#cb_canvasWrapper").parent().unbind();
	
			return this;
		},
		
		events: {
			"change input.filePicker": 'uploadImage',
			"click .cb_up": "orderUp",
			"click .cb_down": "orderDown",
			"click .cb_remove": "remove",
			"click .cb_lock": "toggleLock",
			"click .cb_close": 'close'
		},
		
		remove: function( event ) {
			Backbone.AppModel.get('collection').remove(this.model);
			this.close();
		},
		
		toggleLock: function( event ) {
			this.model.set('isEditable', !this.model.get('isEditable'));
			if ( this.model.get('isEditable') === false ) {
				this.$('.cb_lock i').attr('class', 'icon-ban-circle');
			} else {
				this.$('.cb_lock i').attr('class', 'icon-ok-circle');
			}
		},
		
		orderUp: function( event ) {
			this.model.set('order', this.model.get('order') + 1);
		},
		
		orderDown: function( event ) {
			this.model.set('order', this.model.get('order') - 1);
		},
		
		uploadImage: function( event ) {
			$(".filePicker").attr('disabled', 'disabled');
			var reader = new FileReader(), rFilter = /^image\/(?:bmp|cis\-cod|gif|ief|jpeg|pipeg|png|svg\+xml|tiff|x\-cmu\-raster|x\-cmx|x\-icon|x\-portable\-anymap|x\-portable\-bitmap|x\-portable\-graymap|x\-portable\-pixmap|x\-rgb|x\-xbitmap|x\-xpixmap|x\-xwindowdump)$/i;
			var imageModel = this.model;
			reader.onload = function( event ) {
				imageModel.set('content', event.target.result);
			};
			if ( !rFilter.test(event.target.files[0].type) ) {
				alert("You must select a valid image file!");
				return;
			}
			reader.readAsDataURL(event.target.files[0]);
		},
		
		falseHandler: function( event ) {
			return false;
		},
		
		close: function( event ) {
			if ( this.model.get('content') === '' ) {
				this.model.destroy();
			} else {
				var locked = !this.model.get('isEditable');
				if ( locked ) {
					if ( !window.confirm("Are you sure you want to lock this layer?  This cannot be undone.") ) {
						return;
					}
				}
			}
			this.$el.find('.cb_addEditImage').fadeOut(150).remove();
		}
	});

	// Our module now returns our view
	return ImageEditView;
});
