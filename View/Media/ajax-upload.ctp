<?php foreach($media as $item): ?>
<li class="span2 media-item">
	<a href="#" class="thumbnail">
		<?php echo $this->Media->display($item, array('width' => 100, 'height' => 100)); ?>
		<p style="text-align: center;"><?php echo $item['Media']['title']; ?></p>
	</a>
</li>
<?php endforeach; ?>