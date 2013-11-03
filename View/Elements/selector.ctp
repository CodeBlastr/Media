<?php
//Setting the $selected variable on element call will control how many items can be selected
$multiple = isset($multiple) && is_bool($multiple) ? $multiple : false;
$wrapperclass = isset($class) ? $class : 'col-md-3';
//Format the media regardless of how it sent
$selecteditems = array();
if(isset($media) && !empty($media)) {
	foreach ($media as $m) {
		if(isset($m['Media'])) {
			$m['Media']['selected'] = true;
			$selecteditems[] = $m['Media'];
		}else {
			$m['selected'] = true;
			$selecteditems[] = $m;
		}
	}
}
$thumbnail = isset($this->request->data['MediaThumbnail'][0]) ? json_encode($this->request->data['MediaThumbnail'][0]) : false;
$selecteditems = json_encode($selecteditems);
?>

	<div class="well well-sm clearfix">
		<div class="col-md-8 pull-left">
			<div id="mediaThumbnail">No Media Select</div>
			<div id="mediaSelected" class="clearfix"></div>
		</div>
		<div class="col-md-3 pull-right">
			<a data-toggle="modal" href="#mediaBrowserModal" class="btn btn-primary btn-lg">Select Media</a>
		</div>
	</div>
	
<script type="template/javascript" id="mediaModalTemplate">
	<div class="modal fade" id="mediaBrowserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Browse Files</h4>
      </div>
      <div class="modal-body">
        <div id="mediaBrowser"></div>
      </div>
      <div class="modal-footer">
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

<script data-main="/Media/js/mediabrowser/build/media-min.js" src="/Media/js/mediabrowser/scripts/require.js"></script>

<style>
	.modal-footer {
		border: none;
	}
</style>