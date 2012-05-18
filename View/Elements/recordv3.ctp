<?php
# default settings
$width = !empty($width) ? $width : 320;
$height = !empty($height) ? $height : 240;
$quality = !empty($quality) ? $quality : 'high';
$showVolume = !empty($showVolume) ? $showVolume : 1;
$uuid = !empty($uuid) ? $uuid : substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789',20)),0,40);$offset = !empty($offset) ? $offset : 0; ?>


        <style type="text/css" media="screen"> 
            html, body  { height:100%; }
            body { margin:0; padding:0; overflow:auto; text-align:center; 
                   background-color: #869ca7; }   
            object:focus { outline:none; }
            #flashContent { display:none; }
        </style>
        
        <!-- Enable Browser History by replacing useBrowserHistory tokens with two hyphens -->
		<!--  BEGIN Browser History required section -->
		<?php echo $this->Html->css('/media/record.v3/history/history'); ?>
		<?php echo $this->Html->script('/media/record.v3/history/history'); ?>
        <!-- END Browser History required section -->  
            
		<?php echo $this->Html->script('/media/record.v3/swfobject'); ?>
        <script type="text/javascript">
            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection. 
            var swfVersionStr = "10.2.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
            var xiSwfUrlStr = "/media/record.v3/playerProductInstall.swf";
            var flashvars = {};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#869ca7";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "red5recorder";
            attributes.name = "red5recorder";
            attributes.align = "middle";
            swfobject.embedSWF(
                "/media/record.v3/red5recorder.swf", "flashContent", 
                "100%", "100%", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
            swfobject.createCSS("#flashContent", "display:block;text-align:left;");
        </script>
    </head>
    <body>
        <!-- SWFObject's dynamic embed method replaces this alternative HTML content with Flash content when enough 
             JavaScript and Flash plug-in support is available. The div is initially hidden so that it doesn't show
             when JavaScript is disabled.
        -->
        <div id="flashContent">
            <p>
                To view this page ensure that Adobe Flash Player version 
                10.2.0 or greater is installed. 
            </p>
            <script type="text/javascript"> 
                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
                document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
            </script> 
        </div>
        
        <noscript>
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="red5recorder">
                <param name="movie" value="/media/record.v3/red5recorder.swf" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#869ca7" />
                <param name="allowScriptAccess" value="sameDomain" />
                <param name="allowFullScreen" value="true" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="/media/record.v3/red5recorder.swf" width="100%" height="100%">
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#869ca7" />
                    <param name="allowScriptAccess" value="sameDomain" />
                    <param name="allowFullScreen" value="true" />
                <!--<![endif]-->
                <!--[if gte IE 6]>-->
                    <p> 
                        Either scripts and active content are not permitted to run or Adobe Flash Player version 10.2.0 or greater is not installed.
                    </p>
                <!--<![endif]-->
                    <a href="http://www.adobe.com/go/getflashplayer">
                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                    </a>
                <!--[if !IE]>-->
                </object>
                <!--<![endif]-->
            </object>
        </noscript>

<?php 
if ($hasMany === true) {
  if ($form !== false) { echo $this->Form->create('Media', array('type' => 'file')); }
    echo $this->Form->hidden('Media.'.$offset.'.title', array('value' => ''));
    echo $this->Form->hidden('Media.'.$offset.'.description', array('value' => ''));
    echo $this->Form->hidden('Media.'.$offset.'.uuid', array('value' => $uuid));
    echo $this->Form->hidden('Media.'.$offset.'.user_id', array('value'=> $this->Session->read('Auth.User.id')));
    echo $this->Form->hidden('Media.'.$offset.'.type', array('value' => 'record'));
    echo $this->Form->hidden('Media.'.$offset.'.model', array('value' => $model));
    #echo $this->Form->hidden('Media.foreign_key', array('value' => $foreignKey));
  if ($form !== false) { echo $this->Form->end('Submit Recording'); }
} else {
   if ($form !== false) { echo $this->Form->create('Media', array('type' => 'file')); }
    echo $this->Form->hidden('Media.title', array('value' => ''));
    echo $this->Form->hidden('Media.description', array('value' => ''));
    echo $this->Form->hidden('Media.uuid', array('value' => $uuid));
    echo $this->Form->hidden('Media.user_id', array('value'=> $this->Session->read('Auth.User.id')));
    echo $this->Form->hidden('Media.type', array('value' => 'record'));
    echo $this->Form->hidden('Media.model', array('value' => $model));
    #echo $this->Form->hidden('Media.foreign_key', array('value' => $foreignKey));
  if ($form !== false) { echo $this->Form->end('Submit Recording'); }
}
