<?PHP
#debug($theMedia);

echo '<h2>';
echo !empty($theMedia['Media']['title']) ? $theMedia['Media']['title'] : '(untitled)';
echo '</h2>';

if($theMedia['Media']['type'] == 'audio') {
    echo $this->Html->video('/Themed/Default/'.WEBROOT_DIR.'/media/streams/audio/'.$theMedia['Media']['id'].'.mp3', array('width'=>'1000', 'height'=>'500'));
}
elseif($theMedia['Media']['type'] == 'video') {
    echo $this->Html->video('/Themed/Default/'.WEBROOT_DIR.'/media/streams/video/'.$theMedia['Media']['id'].'.mp4', array('width'=>'1000', 'height'=>'500'));
}

echo '<div class="mediaViewDescription">' . $theMedia['Media']['description'] . '</div>';

?>