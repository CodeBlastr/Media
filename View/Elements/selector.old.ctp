<?php
//Setting the $selected variable on element call will control how many items can be selected
$multiple = isset($multiple) && is_bool($multiple) ? $multiple : false;
$wrapperclass = isset($class) ? $class : 'thumbnail pull-left';
//Format the media regardless of how it sent
$bootstrap = isset($bootstrap) ? $bootstrap :  3;
$selecteditems = array();
if(isset($media) && !empty($media)) {
	foreach ($media as $m) {
		if(isset($m['Media'])) {
			$m['Media']['selected'] = true;
			$selecteditems[] = $m['Media'];
		} else {
			$m['selected'] = true;
			$selecteditems[] = $m;
		}
	}
}
$thumbnail = isset($this->request->data['MediaThumbnail'][0]) ? json_encode($this->request->data['MediaThumbnail'][0]) : false;
$selecteditems = json_encode($selecteditems); ?>

<div id="MediaSelector">
	<a data-toggle="modal" href="#mediaBrowserModal" class="btn btn-primary btn-xs">Select Media</a>
	<div id="mediaSelected" class="clearfix"></div>
</div>
<script type="template/javascript" id="mediaModalTemplate">
	<div class="modal fade" id="mediaBrowserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
      			<div class="modal-body">
        			<div id="mediaBrowser"> <br>&nbsp;&nbsp;Loading Browser...<br><br> </div>
      			</div>
      		</div>
	 	</div>
	</div>
</script>

<script type="text/javascript">
	$($('#mediaModalTemplate').html()).appendTo('body');
	var thumbnail = <?php echo $thumbnail ? $thumbnail : 'false' ?>;
	var selectable = true;
	var wrapperclass = '<?php echo $wrapperclass; ?>';
	var selecteditems = <?php echo $selecteditems; ?>;
	var baseUrl = '<?php echo $this->Html->url(array('plugin' => 'media', 'controller' => 'media_browser', 'action' => 'media')); ?>';
</script>

<?php if($bootstrap == 2): ?>
	<script data-main="/Media/js/mediabrowser_boot2/build/media-min.js" src="/Media/js/mediabrowser/scripts/require.js"></script>
<?php else: ?>
	<!--script data-main="/Media/js/mediabrowser/build/media-min.js" src="/Media/js/mediabrowser/scripts/require.js"></script-->
	<script data-main="/Media/js/mediabrowser_boot2/scripts/mediabrowser.js" src="/Media/js/mediabrowser/scripts/require.js"></script>
	<?php // this is used instead of the line above to make edits... <script data-main="/Media/js/mediabrowser_boot2/scripts/mediabrowser.js" src="/Media/js/mediabrowser/scripts/require.js"></script> ?>
<?php endif; ?>


<style>
	.media-selected.thumbnail.pull-left {
		margin: 4px 4px 0 0;
	}
	.media-selected .content {
		text-align: center;
	}
	.media-selected img {
		width: 48px;
		height: 48px;
	}
	.media-selected p {
		font-size: 10px;
		color: #777;
	}
</style>