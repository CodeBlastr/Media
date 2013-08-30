function clear() {
	//console.log('clear()');
	context.save();
	context.clearRect(0, 0, canvas.width, canvas.height);
	context.restore();
}

function refreshCanvas() {
	//console.log('refreshCanvas');
	clear();
	//console.log(TextObjectCollection);
	TextObjectCollection.each(function( textObject ) {
		//console.log(textObject);
		textObject.write();
	});
	//console.log(TextObjectCollection);
	ImageObjectCollection.each(function( imageObject ) {
		console.log(imageObject);
		imageObject.draw();
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
