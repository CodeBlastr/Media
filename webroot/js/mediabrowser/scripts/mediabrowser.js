//Media Browser Plugin 
//Ver .1

// Filename: mediabrowser.js

require.config({
	paths: {
	    jquery: 'libs/jquery/jquery.min',
	    underscore: 'libs/underscore/underscore.min',
	    backbone: 'libs/backbone/backbone.min',
	    handlebars: 'libs/handlebars',
	    popover: 'libs/popover',
	    tooltip: 'libs/tooltip',
	    jqueryform: 'libs/jquery/jquery.form.min',
	    text: 'libs/text',
	},
	shim: {
	    'backbone': {
	        //These script dependencies should be loaded before loading
	        //backbone.js
	        deps: ['underscore', 'jquery'],
	        //Once loaded, use the global 'Backbone' as the
	        //module value.
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

});

require([
  // Load our app module and pass it to our definition function
  'app',
], function(App){
  // The "app" dependency is passed in as "App"
  App.initialize();
});