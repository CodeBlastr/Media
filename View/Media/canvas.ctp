<div class="row">
	<div class="span8">
		<canvas id="canvas" width="425" height="550">Your browser does not support HTML5 Canvas.</canvas>
	</div>
	<div class="span4">
		<div>
		
			<div id="backgroundControls" style="background: #fff; padding: 15px;">
				<label>
					Background color: 
					<select name="bgColorpicker">
						<option value="#ffffff">White</option>
						<option value="#000000">Black</option>
						<option value="#7bd148">Green</option>
						<option value="#5484ed">Bold blue</option>
						<option value="#a4bdfc">Blue</option>
						<option value="#46d6db">Turquoise</option>
						<option value="#7ae7bf">Light green</option>
						<option value="#51b749">Bold green</option>
						<option value="#fbd75b">Yellow</option>
						<option value="#ffb878">Orange</option>
						<option value="#ff887c">Red</option>
						<option value="#dc2127">Bold red</option>
						<option value="#dbadff">Purple</option>
						<option value="#e1e1e1">Gray</option>
					</select>
				</label>
			</div>

			<div class="btn" id="saveCanvas" data-saved="false">Save progress</div>
			
		</div>
	</div>
</div>

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
		<div class="cb_remove" title="Remove image"><i class="icon-trash"></i></div>
		<div class="cb_lock" title="Lock layer"><i class="icon-ok-circle"></i></div>
	</div>
</div>
</script>
<script type="text/html" id="template-imageEdit">
<div class="cb_addEditImage" style="top: <%= top %>px; left: <%= left %>px;">
	<div class="cb_imageToolbar">
		<div class="pull-left">
			<input type="file" class="filePicker" name="imageLoader"/>
		</div>
		<div class="pull-right editorActions">
			<div class="cb_close" title="Close">&times;</div>
			<div class="cb_up" title="Move up"><i class="icon-arrow-up"></i></div>
			<div class="cb_down" title="Move down"><i class="icon-arrow-down"></i></div>
			<div class="cb_remove" title="Remove image"><i class="icon-trash"></i></div>
		</div>
	</div>
</div>
</script>

<link rel="stylesheet" type="text/css" href="/css/google-webfonts.css" />
<script type="text/javascript" src="/js/simplecolorpicker/simplecolorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="/css/simplecolorpicker/simplecolorpicker.css" />


<script type="text/javascript" src="/js/underscore/underscore-1.5.1.js"></script>
<script type="text/javascript" src="/js/backbone/backbone-1.0.0.js"></script>

<script type="text/javascript" src="/media/js/canvasBuildrr/models/TextObject.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr/models/ImageObject.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr/views/MainOverlay.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr/views/TextEdit.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr/views/ImageEdit.js"></script>

<link rel="stylesheet" type="text/css" href="/media/css/canvasBuildrr.css" />

<script type="text/javascript">
	$(document).ready(function() {
		//$("#canvas").canvasBuildrr();
	});

	$(function() {
		var toggleList = $("#subject"),
			list = $(".subject ul"),
			item = list.find("li"),
			subject = $("#subject");

		toggleList.on("click", function(){
			$(".arrows").toggleClass("is-active");
			list.fadeToggle(); // hide list
		});

		item.on("click", function(){
			var itemVal = $(this).find(".val").text();
			subject.val(itemVal);
			list.fadeOut();
			item.removeClass("is-active");
			$(this).addClass("is-active");
		});
	});

	$('select[name="bgColorpicker"]').simplecolorpicker({picker: true});
	
</script>

<?php if (!(empty($this->request->data))) : ?>
<script>
//console.log(AppModel);
//console.log(AppModel.get('collection'));

AppModel.get('collection').reset();
AppModel.reload(<?php echo json_encode($this->request->data['Media']['data']); ?>);
</script>
<?php endif; ?>
