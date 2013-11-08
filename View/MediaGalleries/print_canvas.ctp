<?php
//echo $this->Html->css(array('/css/google-webfonts.ttf.css', '/media/css/canvasBuildrr.css'));
foreach ($collectionArray as $page) {
	if ($page) {
		foreach ($page->collection as $collection) {
			if (!empty($collection->fontFamily)) {
				$fonts[] = $collection->fontFamily;
			}
		}
	}
}
?>
<html>
	<head>
		<style>
		<?php foreach ($fonts as $font) : ?>
			@font-face {
				font-family: "<?php echo $font ?>";
				src: url('/fonts/google/<?php echo str_replace(' ', '', $font) ?>.ttf');
				font-style: normal;
				font-weight: 400;
			}
		<?php endforeach; ?>
		</style>
	</head>
	<body>
		
		<?php foreach ($fonts as $font) : ?>
		<span style="font-family: '<?php echo $font ?>'; font-size: 0px;">.</span>
		<?php endforeach; ?>
		
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
		
	</body>
</html>
