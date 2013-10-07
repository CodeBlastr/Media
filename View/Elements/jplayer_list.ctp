<link rel="stylesheet" type="text/css" href="/js/plugins/jQuery.jPlayer.2.4.0/blue.monday/jplayer.blue.monday.css">
<script type="text/javascript" src="/js/plugins/jQuery.jPlayer.2.4.0/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jQuery.jPlayer.2.4.0/add-on/jplayer.playlist.min.js"></script>
<div id="jplayerList" class="<?php echo $class; ?>">

	<div id="jquery_jplayer" class="jp-jplayer"></div>
			  <div id="jp_container_1" class="jp-audio">
			    <div class="jp-type-single">
			      <div class="jp-gui jp-interface">
			        <ul class="jp-controls">
			          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
			          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
			          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
			          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
			          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
			          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
			        </ul>
			        <div class="jp-progress">
			          <div class="jp-seek-bar">
			            <div class="jp-play-bar"></div>
			          </div>
			        </div>
			        <div class="jp-volume-bar">
			          <div class="jp-volume-bar-value"></div>
			        </div>
			        <div class="jp-time-holder">
			          <div class="jp-current-time"></div>
			          <div class="jp-duration"></div>
			          <ul class="jp-toggles">
			            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
			            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
			          </ul>
			        </div>
			      </div>
			      <div class="jp-title">
			        <ul>
			          <li>Bubble</li>
			        </ul>
			      </div>
			      
			      <div class="jp-playlist">
			         	<ul>
					      <li></li>
					    </ul>
					</div>
			      <div class="jp-no-solution">
			        <span>Update Required</span>
			        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			      </div>
			    </div>
			  </div>

</div>

<script type="text/javascript">

(function($) {
	$(document).ready(function() {
		var tracks = <?php echo $tracks ?>;
		var myPlaylist = new jPlayerPlaylist({
			  jPlayer: "#jquery_jplayer",
			  cssSelectorAncestor: "#jp_container_1",
			  cssSelector: {
				  videoPlay: '.jp-video-play',
				  play: '.jp-play',
				  pause: '.jp-pause',
				  stop: '.jp-stop',
				  seekBar: '.jp-seek-bar',
				  playBar: '.jp-play-bar',
				  mute: '.jp-mute',
				  unmute: '.jp-unmute',
				  volumeBar: '.jp-volume-bar',
				  volumeBarValue: '.jp-volume-bar-value',
				  volumeMax: '.jp-volume-max',
				  currentTime: '.jp-current-time',
				  duration: '.jp-duration',
				  fullScreen: '.jp-full-screen',
				  restoreScreen: '.jp-restore-screen',
				  repeat: '.jp-repeat',
				  repeatOff: '.jp-repeat-off',
				  gui: '.jp-gui',
				  noSolution: '.jp-no-solution'
				 },
				  
			}, tracks, {
			  playlistOptions: {
			    enableRemoveControls: false
			  },
			  swfPath: "/js/plugins/jQuery.jPlayer.2.4.0/",
			  supplied: "mp3",
			  smoothPlayBar: true,
			  keyEnabled: true,
			  audioFullScreen: false // Allows the audio poster to go full screen via keyboard
			});
		$('#jquery_jplayer').hide();
	});   
})(jQuery);

</script>

