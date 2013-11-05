<?php
echo $this->Html->css(array('/css/google-webfonts.css', '/media/css/canvasBuildrr.css'));
?>


<div class="canvasBuildrr" style="width: 4984px;">
	<!-- canvasBuildrr app goes here -->
</div>


<script type="text/html" id="template-canvas">
	<canvas id="canvas<%= canvas%>" height="2490" width="1740">
		You are using an outdated browser.
		<a href="http://browsehappy.com/">Upgrade your browser today</a>
		or
		<a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a>
		to better experience this site.
	</canvas>
</script>


<script type="text/javascript">
	var canvasData1 = <?php echo json_encode($collectionArray[0]); ?>;
	var canvasData2 = <?php echo json_encode($collectionArray[1]); ?>;
	var galleryId = '<?php echo $this->passedArgs[0] ?>';
</script>
<script data-main="/js/printCanvasGut/printCanvas.js" src="/js/printCanvasGut/require.js"></script>
