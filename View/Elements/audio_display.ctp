<?php echo $this->Html->css('/js/plugins/jQuery.jPlayer.2.4.0/blue.monday/jplayer.blue.monday'); ?>
<?php echo $this->Html->script('/js/plugins/jQuery.jPlayer.2.4.0/jquery.jplayer.min'); ?>
<div id="jplayerList" class="<?php echo $class; ?>">
	<div id="jquery_jplayer_<?php echo $id; ?>" class="jp-jplayer"></div>
	<div id="jp_container_<?php echo $id; ?>" class="jp-audio">
	    <div class="jp-type-single">
	        <div class="jp-gui jp-interface">
	            <ul class="jp-controls">
	                
	                <!-- comment out any of the following <li>s to remove these buttons -->
	                
	                <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
	                <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
	                <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
	                <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
	                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
	                <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
	            </ul>
	            
	            <!-- you can comment out any of the following <div>s too -->
	            
	            <div class="jp-progress">
	                <div class="jp-seek-bar">
	                    <div class="jp-play-bar"></div>
	                </div>
	            </div>
	            <div class="jp-volume-bar">
	                <div class="jp-volume-bar-value"></div>
	            </div>
	            <div class="jp-current-time"></div>
	            <div class="jp-duration"></div>                   
	        </div>
	        <div class="jp-title">
	            <ul>
	                <li><?php echo $title; ?></li>
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

$(function() {
	$(document).ready(function() {
		var tracks = <?php echo $tracks ?>;
		console.log(tracks);
	    $("#jquery_jplayer_<?php echo $id; ?>").jPlayer({
	        ready: function(event) {
	            $(this).jPlayer("setMedia", tracks);
	        },
	        play: function() { // To avoid multiple jPlayers playing together.
	        	$(this).jPlayer("pauseOthers");
			},
	        wmode: "window",
			smoothPlayBar: true,
			keyEnabled: true,
			cssSelectorAncestor: "#jp_container_<?php echo $id; ?>",
			solution: "html, flash",
	        swfPath: "/js/plugins/jQuery.jPlayer.2.4.0/",
	        supplied: "mp3"
	    });
	});   
});

</script>
