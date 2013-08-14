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
</style>

<link rel="stylesheet" type="text/css" href="/css/google-webfonts.css" />

<script type="text/javascript" src="/media/js/canvasBuildrr.js"></script>
<link rel="stylesheet" type="text/css" href="/media/css/canvasBuildrr.css" />


<script type="text/javascript">




	$(document).ready(function() {

		$("#canvas").canvasBuildrr();

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