<li class="span2 media-item">
	<a href="#" class="thumbnail">
		<?php echo $this->Media->display($media, array('width' => 100, 'height' => 100)); ?>
		<p style="text-align: center;"><?php echo $media['Media']['title']; ?></p>
	</a>
</li>