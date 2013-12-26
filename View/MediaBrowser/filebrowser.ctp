<div class="container">
	<div class="row">
  		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  			<h1>The Filebrowser</h1>
  			<div id="mediaBrowser"></div>
		</div>
	</div>
</div>

<link href="/Media/css/mediaBrowswer.css" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript">
	var baseUrl = '<?php echo $this->Html->url(array('plugin' => 'media', 'contoller' => 'media_browser', 'action' => 'media')); ?>';
</script>
<script data-main="/Media/js/mediabrowser/build/media-min.js" src="/Media/js/mediabrowser/scripts/require.js"></script>
