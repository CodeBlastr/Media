function clear() {
	context.save();
	context.clearRect(0, 0, canvas.width, canvas.height);
	context.restore();
}

function refreshCanvas() {
	clear();
	
	CanvasObjectCollection.each(function( canvasObject ) {
		console.log(canvasObject);
		canvasObject.draw();
	});
	
	return true;
}

function rotateAndWrite() {
	context.save();
	context.translate(newx, newy);
	context.rotate(-Math.PI / 2);
	context.textAlign = "center";
	context.fillText("Your Label Here", labelXposition, 0);
	context.restore();
}
