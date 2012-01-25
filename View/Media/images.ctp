<div class="media images index">
	<h2><?php echo $page_title_for_layout; ?></h2>
	<iframe src="/js/kcfinder/browse.php?type=images&amp;kcfinderuploadDir=<?php echo str_replace('sites'.DS, '', SITE_DIR); ?>&amp;CKEditor=WebpageContent&amp;&amp;langCode=en" width="100%" height="500px"></iframe>
</div>

<?php 
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Files',
		'items' => array(
			 $this->Html->link(__('Go to Files', true), array('action' => 'files')),
			)
		)
	))); ?>