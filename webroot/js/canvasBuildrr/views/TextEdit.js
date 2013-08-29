var TextEditView = Backbone.View.extend({
	initialize: function( attrs ) {
		this.options = attrs;
		this.render();
		$('select[name="colorpicker"]').simplecolorpicker({picker: true});
	},
	render: function() {
		var template = _.template($("#template-textEdit").html(), this.options);
		this.$el.append(template);

		this.$el.find('option[value="'+this.model.get('fontColor')+'"]').attr("selected", "selected");
		this.$el.find('option[value="'+this.model.get('fontSize')+'"]').attr("selected", "selected");

		$("#cb_canvasWrapper").unbind(); // whoa.. need this for some reason

		return this;
	},
	events: {
		"keyup .textInput": "updateText",
		"click .cb_addEditText": "falseHandler",
		"click .cb_close": "close",
		'change select[name="colorpicker"]': 'updateColor',
		'change select[name="fontsizepicker"]': 'updateFontsize'
	},
	updateText: function( event ) {
		console.log('updateText()');
		this.model.set('content', event.target.value);
	},
	updateColor: function( event ) {
		console.log('updateColor()');
		this.model.set('fontColor', event.target.value);
	},
	updateFontsize: function( event ) {
		console.log('updateFontsize()');
		this.model.set('fontSize', event.target.value);
	},
	falseHandler: function( event ) {
		console.log('falseHandler()');
		return false;
	},
	close: function( event ) {
		$('select[name="colorpicker"]').simplecolorpicker('destroy');
		this.$el.find('.cb_addEditText').remove();
//		$("#cb_canvasWrapper").bind('click', function( event ) {
//			mainMenuHandler(event);
//		});
//		$("#cb_canvasWrapper").bind('mousemove', function( event ) {
//			mousemoveHandler(event);
//		});
	}
});

var textEditHandler = function( event, text ) {
	if ( text === undefined ) {
		text = new TextObject({x: click.x, y: click.y});
		TextObjectCollection.add(text);
		//debug
		console.log('text added to textCollection at: ' + click.x + ', ' + click.y);
	} else {
		console.log('editing text object:');
		console.log(text);
	}
	var textEditor = new TextEditView({
		model: text,
		el: $("#cb_canvasWrapper"),
		top: text.get('y') + 10,
		left: text.get('x'),
		content: text.get('content')
	});
};
