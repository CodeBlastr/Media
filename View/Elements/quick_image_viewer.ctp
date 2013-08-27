<?php
	/**
	 * Quick Image Gallery with Lazy loading
	 */
	 
	if(!isset($media) && isset($this->request->data['Media'])) {
		$media = $this->request->data['Media']; 
	} else {
		$media = '';
	}
?>

<div id="imageViewer">
	
	<div class="mainImage">
		
	</div>
	<div class="thumbnails">
		
	</div>
	
</div>

<div id="<?php echo $id; ?>" class="<?php echo $class; ?>">
	<?php echo $image; ?>
</div>