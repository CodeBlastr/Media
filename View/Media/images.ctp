  <iframe src="/js/kcfinder/browse.php?type=images&amp;kcfinderuploadDir=<?php echo str_replace('sites'.DS, '', SITE_DIR); ?>&amp;CKEditor=WebpageContent&amp;&amp;langCode=en" width="100%" height="500px"></iframe>
<?php 
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Files',
		'items' => array(
			 $this->Html->link(__('Files', true), array('action' => 'files')),
			)
		)
	))); ?>