<?php
	//Setting the $selected variable on element call will control how many items can be selected
?>

<div id="MediaSelector">
	<a class="btn" href="/media/media/filebrowser">Select Media</a>
	
	<div id="MediaSelected">
					
	</div>

</div>

<script type="text/javascript">

(function($) {	
	$('a[href="/media/media/filebrowser"]').click(function(e) {
		e.preventDefault();
		$.post("/media/media/filebrowser", { <?php echo isset($selected) ? 'selected: '.$selected : '' ?> })
			.done(function(html) {
  				$('body').append(html);
  				$('#MediaFileBrowser').show('fast');
			});
	});
})(jQuery);
</script>