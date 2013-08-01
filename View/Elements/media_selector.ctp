<?php
	//Setting the $selected variable on element call will control how many items can be selected
?>

<div id="MediaSelector">
	<a class="btn" href="/media/media/filebrowser">Select Media</a>
	
	<div id="MediaSelected" class="clearfix">
			
	</div>

</div>

<script type="text/javascript">

(function($) {
	
	var mediaNum = 0;
	var mediaSelected = [];
	var loaded = false;

	$('a[href="/media/media/filebrowser"]').click(function(e) {
		e.preventDefault();
		if(loaded) {
			$('#MediaBrowserPopUp').show('slow');
		}else {
			$.post("/media/media/filebrowser", { <?php echo isset($selected) ? 'selected: '.$selected : '' ?> })
				.done(function(html) {
					html = '<div id="MediaBrowserPopUp"><a href="#insert" class="btn">Insert</a><a href="#close" class="btn">Close</a>'+html+'</div>';
	  				$('body').append(html);
	  				$('#MediaBrowserPopUp').css('left', ($(window).width()*.5)-($('#MediaBrowserPopUp').width()/2)).css('top', '30px');
	  				$('body').append('<div class="modal-backdrop fade in"></div>');
	  				$('#MediaBrowserPopUp').show('slow');
	  				loaded = true;
				});
		}
		
		
	});
	
	
	$(document).on('click', 'a[href="#insert"]', function(e) {
		e.preventDefault();
		mediaSelected = [];
		
		$('#mediaBrowser .selected').each(function(index, el) {
			console.log($(el));
			var obj = $(el).find('.media-item').clone();
			mediaSelected.push(obj);
		});
		
		renderSelectedItems();
		
		$('#MediaBrowserPopUp').hide('slow');
		$('.modal-backdrop').remove();
	});
	
	$(document).on('click', 'a[href="#close"]', function(e) {
		e.preventDefault();
		$('#MediaBrowserPopUp').hide('slow');
		$('.modal-backdrop').remove();
	});
	
	function renderSelectedItems() {
		$('#MediaSelected').children().remove();
		console.log(mediaSelected);
		if(mediaSelected.length > 0){
			
			for(var i=0 ; i < mediaSelected.length ; i++) {
				mediaSelected[i].append('<input type="hidden" value="'+mediaSelected[i].attr('id')+'" name="data[MediaAttachment]['+i+'][media_id]">');
				console.log(mediaSelected[i]);
				mediaSelected[i].appendTo($('#MediaSelected'));
			}
		}
	}
	
	
	
})(jQuery);
</script>

<style>

	#MediaBrowserPopUp {
		display:none;
		width: 60%;
		position: absolute;
		z-index: 9999;
		background: #fff;
		padding:30px;
		border-radius: 30px;
	}
	
	#MediaSelected .media-item {
		float: left;
		padding:10px;
		background: #fff;
	}
	
</style>