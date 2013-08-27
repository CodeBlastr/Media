<div class="row">
	<div class="span8">
		<canvas id="canvas" width="425" height="550">Your browser does not support HTML5 Canvas.</canvas>
	</div>
	<div class="span4">
<!--		<input type="text" id="line1" name="line1" />-->
	</div>
</div>

<style>
	canvas {
		background:	#fff;
	}
	#cb_canvasWrapper {
		cursor: crosshair;
	}
	.cb_close { cursor: pointer; }
</style>


<script type="text/html" id="template-textEdit">
<div class="cb_addEditText" style="top: <%= top %>px; left: <%= left %>px; position: absolute; display:block; cursor: default;">
	<div class="cb_textToolbar">
		<select name="colorpicker">
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
		<select name="fontsizepicker" class="input-small">
			<option value="10">10px</option>
			<option value="16">16px</option>
			<option value="24">24px</option>
			<option value="32">32px</option>
			<option value="48">48px</option>
			<option value="64">64px</option>
			<option value="72">72px</option>
		</select>
		<ul id="fontList">
			<li class="init">- choose font -</li>
			<li id="ABeeZee" style="font-family:'ABeeZee';">ABeeZee</li>
			<li id="Abel" style="font-family:'Abel';">Abel</li>
		</ul>
		<span class="cb_close" title="close">&times;</span>
	</div>
	<input type="text" class="textInput" value="<%= content %>" />
</div>
</script>
<script type="text/html" id="template-imageEdit">
<div class="cb_addEditImage" style="top: <%= top %>px; left: <%= left %>px; position: absolute; display:block; cursor: default;">
	<div class="cb_imageToolbar">
		<input type="file" class="filePicker" name="imageLoader"/>
		<span class="cb_close pull-right" title="close">&times;</span>
	</div>
</div>
</script>
<link rel="stylesheet" type="text/css" href="/css/google-webfonts.css" />
<script type="text/javascript" src="/js/simplecolorpicker/simplecolorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="/css/simplecolorpicker/simplecolorpicker.css" />


<script type="text/javascript" src="/js/underscore/underscore-1.5.1.js"></script>
<script type="text/javascript" src="/js/backbone/backbone-1.0.0.js"></script>
<script type="text/javascript" src="/media/js/canvasBuildrr.js"></script>
<link rel="stylesheet" type="text/css" href="/media/css/canvasBuildrr.css" />

<script type="text/javascript">

	$(document).ready(function() {

		//$("#canvas").canvasBuildrr();

//		var canvas;
//		var context;
//		var xs = [25, 100, 175, 250, 325, 400, 475, 550, 625, 700];
//		var ys = [360, 360, 360, 360, 360, 360, 360, 360, 360, 360];
//		var originalXs = [25, 100, 175, 250, 325, 400, 475, 550, 625, 700];
//		var originalYs = [360, 360, 360, 360, 360, 360, 360, 360, 360, 360];
//		var WIDTH = 425;
//		var HEIGHT = 550;
//		var dragok = false;
//		var currentObjectIndex;
//		var spawn = [];
//		var mouseDownWasAt;
//
//		function rect(x,y,w,h) {
//			context.beginPath();
//			context.rect(x,y,w,h);
//			context.closePath();
//			context.fill();
//		}
//
//		function clear() {
//			context.clearRect(0, 0, WIDTH, HEIGHT);
//			context.fillStyle = "#FFFFFF";
//			rect(0,0,WIDTH,HEIGHT);
//		}
//
//		function init() {
//			canvas = document.getElementById("canvas");
//			context = canvas.getContext("2d");
//			//drawAll();
//		}
//
//		function drawAll() {
//			clear();
//
//			var i = 0;
//			while ( i < xs.length ) {
//				if ( i >= 10 ) {
//					drawObject(spawn[i], false, i);
//					//console.log('drew ' + i + ' as a copy of image #' + spawn[i]);
//				} else {
//					drawObject(i, false);
//				}
//				i++;
//			}
//		}
//
//		function myMove(e) {
//			if (dragok) {
//				xs[currentObjectIndex] = e.pageX - canvas.offsetLeft;
//				ys[currentObjectIndex] = e.pageY - canvas.offsetTop;
//				drawAll();
//			}
//		}
//
//		function myDown(e){
//			/**
//			 * goes through each set of coordinates
//			 * and checks to see if you clicked in one of the areas
//			 */
//			xs.forEach( function(element, index, array){
//				if (
//					e.pageX < xs[index] + 25 + canvas.offsetLeft
//					&& e.pageX > xs[index] - 25 + canvas.offsetLeft
//					&& e.pageY < ys[index] + 25 + canvas.offsetTop
//					&& e.pageY > ys[index] - 25 + canvas.offsetTop
//				  ){
//					  x = e.pageX - canvas.offsetLeft;
//					  y = e.pageY - canvas.offsetTop;
//					  dragok = true;
//					  currentObjectIndex = index;
//					  //console.log('clicked ' + currentObjectIndex);
//					  canvas.onmousemove = myMove;
//					  mouseDownWasAt = HEIGHT - (e.pageY - canvas.offsetTop);
//				}
//			});
//		}
//
//		function myUp(e) {
//			dragok = false;
//			canvas.onmousemove = null;
//
//			var distanceFromBottom = HEIGHT - (e.pageY - canvas.offsetTop)
//			if ( currentObjectIndex !== undefined && distanceFromBottom > 75 && mouseDownWasAt < 75) {
//				drawObject(currentObjectIndex, true);
//			}
//
//			drawAll();
//		}
//
//
//		function writeText(element) {
//			context.lineWidth	= 1;
//			context.fillStyle	= "#CC00FF";
//			context.lineStyle	= "#ffff00";
//			context.font		= "18px Aclonica";
//			context.fillText(element.val(), 20, 20);
//		}
//
//
//		init();
//		//writeText();
//		canvas.onmousedown = myDown;
//		canvas.onmouseup = myUp;
//
//
//		$("#line1").on('keyup change', function(){
//			clear();
//			writeText($(this));
//		});


	});
</script>