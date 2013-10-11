<!-- CSS -->
<style>
	.modal-footer {
		border: none;
	}
</style>
<link rel="stylesheet" type="text/css" href="/media/css/canvasBuildrr.css" />
<link rel="stylesheet" type="text/css" href="/css/google-webfonts.css" />
<link rel="stylesheet" type="text/css" href="/css/simplecolorpicker/simplecolorpicker.css" />


<div class="span8 canvasBuildrr">
	<!-- canvasBuildrr app goes here -->
</div>
<div class="span4">
	<div>
		<div id="backgroundControls" style="background: #fff; padding: 15px;">
			<label>
				Background color:
				<select name="bgColorpicker">
					<option value="#000000">Black</option>
					<option value="#e1e1e1">Gray</option>
					<option value="#ffffff">White</option>
					<option value="#5484ed">Bold blue</option>
					<option value="#a4bdfc">Blue</option>
					<option value="#46d6db">Turquoise</option>
					<option value="#7ae7bf">Seafoam</option>
					<option value="#7bd148">Green</option>
					<option value="#51b749">Bold green</option>
					<option value="#fbd75b">Yellow</option>
					<option value="#ffb878">Orange</option>
					<option value="#ff887c">Red</option>
					<option value="#dc2127">Bold red</option>
					<option value="#663399">Royal Purple</option>
					<option value="#dbadff">Light Purple</option>
					<option value="#ff0080">Hot Pink</option>
				</select>
			</label>

			<?php
			//Setting the $selected variable on element call will control how many items can be selected
			$multiple = isset($multiple) && is_bool($multiple) ? $multiple : false;
			$wrapperclass = isset($class) ? $class : 'col-md-3';
			//Format the media regardless of how it sent
			$selecteditems = array();
			if ( isset($media) && !empty($media) ) {
				foreach ( $media as $m ) {
					if ( isset($m['Media']) ) {
						$m['Media']['selected'] = true;
						$selecteditems[] = $m['Media'];
					} else {
						$m['selected'] = true;
						$selecteditems[] = $m;
					}
				}
			}
			$selecteditems = json_encode($selecteditems);
			?>

			<div id="MediaSelector">
				Upload Foreground Image
				<a data-toggle="modal" href="#mediaBrowserModal" class="btn btn-primary btn-lg">Browse</a>
				<p>&nbsp;</p>
				<div id="mediaSelected" class="clearfix"></div>
			</div>
		</div>

		<div class="btn" id="saveCanvas" data-saved="false">Save progress</div>

	</div>
</div>


<script type="template/javascript" id="mediaModalTemplate">
	<div class="modal fade" id="mediaBrowserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none">
		<div class="modal-dialog">
		    <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Browse Files</h4>
				</div>
				<div class="modal-body">
					<div id="mediaBrowser"></div>
				</div>
				<div class="modal-footer"></div>
		    </div>
		</div>
	</div>
</script>

<script type="text/html" id="template-canvas">
	<div id="cb_canvasWrapper" style="width: 420px;">
		<canvas id="canvas" width="420" height="594">
			You are using an outdated browser.
			<a href="http://browsehappy.com/">Upgrade your browser today</a>
			or
			<a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a>
			to better experience this site.
		</canvas>
	</div>
</script>

<script type="text/html" id="template-textEdit">
<div class="cb_addEditText" style="top: <%= top %>px; left: <%= left %>px;">
	<div class="pull-left">
		<div class="cb_textToolbar">
			<select name="colorpicker">
				<option value="#000000">Black</option>
				<option value="#e1e1e1">Gray</option>
				<option value="#ffffff">White</option>
				<option value="#5484ed">Bold blue</option>
				<option value="#a4bdfc">Blue</option>
				<option value="#46d6db">Turquoise</option>
				<option value="#7ae7bf">Seafoam</option>
				<option value="#7bd148">Green</option>
				<option value="#51b749">Bold green</option>
				<option value="#fbd75b">Yellow</option>
				<option value="#ffb878">Orange</option>
				<option value="#ff887c">Red</option>
				<option value="#dc2127">Bold red</option>
				<option value="#663399">Royal Purple</option>
				<option value="#dbadff">Light Purple</option>
				<option value="#ff0080">Hot Pink</option>
			</select>
			<select name="fontsizepicker" class="input-small">
				<option value="10">10px</option>
				<option value="16">16px</option>
				<option value="24">24px</option>
				<option value="32">32px</option>
				<option value="48">48px</option>
				<option value="64">64px</option>
				<option value="72">72px</option>
			</select>
			<div class="fontInputBox">
				<input type="text" name="font" id="font" placeholder="- choose font -" readonly>
				<ul id="fontList">
					<li style="font-family:'Arial';">Arial</li>
					<li style="font-family:'Open Sans';">Open Sans</li>
					<li style="font-family:'Oswald';">Oswald</li>
					<li style="font-family:'Lobster';">Lobster</li>
					<li style="font-family:'Shadows Into Light';">Shadows Into Light</li>
					<li style="font-family:'Crafty Girls';">Crafty Girls</li>
					<li style="font-family:'Changa One';">Changa One</li>
					<li style="font-family:'Happy Monkey';">Happy Monkey</li>
					<li style="font-family:'Special Elite';">Special Elite</li>
					<li style="font-family:'Coming Soon';">Coming Soon</li>
					<li style="font-family:'Pacifico';">Pacifico</li>
				</ul>
			</div>
		</div>
		<input type="text" class="textInput" autofocus value="<%= content %>" />
	</div>
	<div class="pull-right editorActions">
		<div class="cb_close" title="Close">&times;</div>
		<div class="cb_up" title="Move up"><i class="icon-arrow-up"></i></div>
		<div class="cb_down" title="Move down"><i class="icon-arrow-down"></i></div>
		<div class="cb_remove" title="Remove this text"><i class="icon-trash"></i></div>
		<div class="cb_lock" title="Lock layer"><i class="icon-ok-circle"></i></div>
	</div>
</div>
</script>
<script type="text/html" id="template-imageEdit">
<div class="cb_addEditImage" style="top: <%= top %>px; left: <%= left %>px;">
	<div class="cb_imageToolbar">
		<div class="pull-left">

		</div>
		<div class="pull-right editorActions">
			<div class="cb_close" title="Close">&times;</div>
			<div class="cb_up" title="Move up"><i class="icon-arrow-up"></i></div>
			<div class="cb_down" title="Move down"><i class="icon-arrow-down"></i></div>
			<div class="cb_remove" title="Remove this image"><i class="icon-trash"></i></div>
		</div>
	</div>
</div>
</script>


<script type="text/javascript">
	$($('#mediaModalTemplate').html()).appendTo('body');
	var selectable = true;
	var wrapperclass = '<?php echo $wrapperclass; ?>';
	var selecteditems = <?php echo $selecteditems; ?>;
	var baseUrl = '<?php echo $this->Html->url(array('plugin' => 'media', 'controller' => 'media_browser', 'action' => 'media')); ?>';
	var canvasData = <?php echo json_encode($this->request->data['Media']['data']); ?>;
</script>
<script data-main="/Media/js/canvasBuildrr/canvasBuildrr.js" src="/Media/js/canvasBuildrr/require.js"></script>

