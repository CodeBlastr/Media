({
	name: "mediabrowser",
    include: ["mediabrowser"],
    out: "../build/media-min-simple.js",
    
	paths: {
	    jquery: 'libs/jquery/jquery-min',
	    underscore: 'libs/underscore/underscore-min',
	    backbone: 'libs/backbone/backbone-min',
	    handlebars: 'libs/handlebars',
	    popover: 'libs/popover',
	    tooltip: 'libs/tooltip',
	    jqueryform: 'libs/jquery/jquery-form-min',
	    text: 'libs/text'
	},
	shim: {
	    'backbone': {
	        deps: ['underscore', 'jquery'],
	        exports: 'Backbone'
	    },
	    'underscore': {
	        exports: '_'
	    },
	    'handlebars': {
	        exports: 'Handlebars'
	    },
	    'popover': {
	    	deps: ['tooltip'],
	    	exports: 'Popover'
	    },
	    'jqueryform': {
	    	deps: ['jquery'],
	    	exports: 'jqueryForm'
	    }
	}

})