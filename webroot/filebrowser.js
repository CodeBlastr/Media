//Requires jquery.form.js
//http://jquery.malsup.com/form/
(function($) {
		
		var links = $('#MediaFileBrowser .nav a');
		var windows = $('#MediaFileBrowser .content-panels .panel');
		var mediaItems = $('#mediaBrowser li.media-item');
		var loader = $('#MediaFileBrowser .loader');
		
		$(document).ready(function() {
			windows.hide();
			var id;
			links.each(function(i, link) {
				if($(link).hasClass('active')) {
					id = $(link).attr('href');
					return false;
				}
			});
			loader.hide();
			showWindow(id);
		});
		
		$('#MediaAddForm').ajaxForm({
			    beforeSend: function() {
			        loader.show();
			    },
			    uploadProgress: function(event, position, total, percentComplete) {
			        var percentVal = percentComplete + '%';
			        console.log(percentVal);
			    },
			    success: function(html) {
			    	console.log(html);
			        links.removeClass('active');
			        $('#mediaBrowser').addClass('active');
			        if(mediaItems.length > 0) {
			        	mediaItems.last().after(html);
			        }else {
			        	$('#mediaBrowser ul').html(html);
			        }
			        mediaItems = $('#mediaBrowser li.media-item');
			        showWindow('#mediaBrowser');
			        $('#MediaAddForm').clearForm();
			    },
				complete: function(xhr) {
					loader.hide();
				}
		}); 
				     
		
		links.click(function(e) {
			e.preventDefault();
			var id = $(this).attr('href');
			links.removeClass('active');
			$(this).addClass('active');
			showWindow(id);
			
		})
		
		$('#mediaBrowser').on('click', 'a.thumbnail', function(e){
			if($(this).hasClass('selected')) {
				$(this).removeClass('selected');
				console.log('unselected');
			}else{
				$(this).addClass('selected');
				console.log('selected');
			}
		})
		
		function showWindow (id) {
			windows.hide('fast');
			$(id).show('fast');
		}
		
})(jQuery)