<?php
echo $this->Html->css(array('/css/google-webfonts.css', '/media/css/canvasBuildrr.css'), null, array('inline' => false));

// format data
foreach ($this->request->data['Media'] as $media) {
	$collectionArray[] = json_decode($media['data']);
}
?>

<div class="canvasBuildrr">
	<!-- canvasBuildrr app goes here -->
</div>


<script type="text/html" id="template-canvas">
		<canvas id="canvas1" width="2481" height="1753">
			You are using an outdated browser.
			<a href="http://browsehappy.com/">Upgrade your browser today</a>
			or
			<a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a>
			to better experience this site.
		</canvas>
</script>


<script type="text/javascript">
	var baseUrl = '<?php echo $this->Html->url(array('plugin' => 'media', 'controller' => 'media_browser', 'action' => 'media')); ?>';
	var canvasData1 = <?php echo json_encode($collectionArray[0]); ?>;
	var canvasData2 = <?php echo json_encode($collectionArray[1]); ?>;
	var galleryId = '<?php echo $this->passedArgs[0] ?>';
</script>
<script data-main="/js/printCanvasGut/printCanvas.js" src="/js/printCanvasGut/require.js"></script>
