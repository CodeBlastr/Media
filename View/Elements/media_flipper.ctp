<script src="/js/handlebars/handlebars.js" type="text/javascript"></script>

<script type="text/javascript">

$(function() {
	var mediaUrl = 'http://audiojack.localhost/media/media_galleries/getGalleryMedia/523c8210-5a2c-4ff6-b6de-27d0124e0d46.json';
	var mediaPath = '';
	var media;
	var images;
	var login;
	var imghgt = 100;
	var limit = 48;
	var shown = false;

	$(document).ready(function(e) {
		login = $('#loginArea').clone();
		$('#loginArea').remove();
	});

	var doit;
	$(window).resize(function(){
	  if(shown) {
		login.remove();
		media.remove();
	  	clearTimeout(doit);
	  	doit = setTimeout(showinit, 500);
	  }
	});

	function showinit() {
		images = [];
		var hgt = $(window).width();
	 	if(hgt < 768) {
			imghgt = ($('#splashArea').width()) / 4;
			limit = 12;
	 	}else if(hgt >= 768 && hgt <= 768) {
			imghgt = ($('#splashArea').width()) / 6;
			limit = 36;
	 	}else {
	 		imghgt = ($('#splashArea').width()) / 12;
			limit = 12*4;
		}
		
		$.get(mediaUrl+'?limit='+limit).success(function(data) { mediaPath = data.Path; renderMedia(data); });
	}

	function renderMedia(data) {
		data.height = imghgt;
		var flipboxsource   = $("#media-template").html();
		var flipboxtemplate = Handlebars.compile(flipboxsource);
		var flipboxhtml = flipboxtemplate(data);
		media = $('<div id="mediaImages"></div>');
		media.width($('#splashArea').width());
		media.height($('#splashArea').height());
		media.html(flipboxhtml);
		var imagesource   = $("#images-template").html();
		var imagetemplate = Handlebars.compile(imagesource);
		images = imagetemplate(data);
		images = $(images).children('img');
		flipImages();
	}

	$(document).on('click', '#SplashLink', function(e) {
		shown = true;
		showinit();
	});

	function flipImages() {
		$('#splashArea').html(media);
		imgarray = [];
		images.each(function(index, image) {
			var selector = ".flipbox-"+index;
			$(image).hide();
			$(image).data('hidden', true);
			$(selector).html($(image));
			imgarray.push($(image));
		});
		var cont = true;
		var i = 1;
		var delay = 0;
		while (cont) {
			if(i > 1) {
				delay = Math.floor(Math.random()*2000);
			}else {
				delay = 0;
			}
			cont = false;
			imgarray[Math.floor(Math.random()*imgarray.length)].delay( delay ).show('slow').data('hidden', false);
			for(i = 0 ; i < imgarray.length ; i++) {
				if(imgarray[i].data('hidden') === true) {
					cont = true;
				}
			}
			i++;
		}
		if(!cont) {
			  login.css('opacity', 0);
			  login.appendTo('#splashArea');
			  login.delay(2000).animate({
				opacity: 1	
				  }, 1000);
			}
	}
	
});
	

</script>

<script id="media-template" type="text/x-handlebars-template">
  {{#each Media}}
  		<div class="flipbox flipbox-{{@index}} col-md-1 col-xs-3 col-sm-2 col-lg-1" style="height:{{../height}}px;">
			
		</div>
  {{/each}}
</script>

<script id="images-template" type="text/x-handlebars-template">
  <div>
  {{#each Media}}
  		<img src="{{../path}}{{filename}}.{{extension}}"></img>
  {{/each}}
  </div>
</script>

<style type="text/css">
    #mediaImages {
    	position: absolute;
    	top: 0;
    	left: 0;
    }
    
    #mediaImages img {
    	width: 100%;
    	height: 100%;
    }
    
	.flipbox {
		padding:0;
	}
</style>