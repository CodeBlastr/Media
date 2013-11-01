<?php
echo $this->Html->css(array('/css/google-webfonts.css', '/media/css/canvasBuildrr.css'), null, array('inline' => false));
?>

<div class="canvasBuildrr">
	<!-- canvasBuildrr app goes here -->
</div>

<script type="text/javascript">
	var baseUrl = '<?php echo $this->Html->url(array('plugin' => 'media', 'controller' => 'media_browser', 'action' => 'media')); ?>';
	var canvasData = <?php echo json_encode($this->request->data); ?>;
	var galleryId = '<?php echo $this->passedArgs[0] ?>';
</script>
<script data-main="/js/printCanvasGut/printCanvas.js" src="/js/printCanvasGut/require.js"></script>
