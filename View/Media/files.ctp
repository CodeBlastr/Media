<div class="media files index">
	<iframe src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/js/kcfinder/browse.php?type=files&amp;kcfinderuploadDir=<?php echo str_replace('sites'.DS, '', SITE_DIR); ?>&amp;CKEditor=WebpageContent&amp;&amp;langCode=en" width="100%" height="500px"></iframe>
</div>

<?php 
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Files',
		'items' => array(
			 $this->Html->link(__('Go to Images', true), array('action' => 'images')),
			)
		)
	))); ?>