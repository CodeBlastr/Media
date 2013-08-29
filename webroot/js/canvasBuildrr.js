/**
 * @name canvasBuildrr
 * @description Adds simple image and text manipulation to a canvas element.
 * @author Joel Byrnes <joel@buildrr.com>
 */
var element = $("#canvas");

/**
 * set up the necessary pointers
 */
var canvas = document.getElementById(element.attr('id'));
var context = canvas.getContext("2d");

var click = {x: '', y: ''};

var isDragging = false;

var TextObjects = Backbone.Collection.extend({
	model: TextObject
});
var TextObjectCollection = new TextObjects();

var ImageObjects = Backbone.Collection.extend({
	model: ImageObject
});
var ImageObjectCollection = new ImageObjects();
