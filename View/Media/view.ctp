<?PHP
#debug($theMedia);

echo '<h2>';
echo !empty($theMedia['Media']['title']) ? $theMedia['Media']['title'] : '(untitled)';
echo '</h2>';

if($theMedia['type'] == 'audio') {
    echo $this->Html->video($theMedia['Media']['filename'] . '.mp3', array('width'=>'1000', 'height'=>'500'));
}
elseif($theMedia['type'] == 'video') {
    echo $this->Html->video($theMedia['Media']['filename'] . '.mp4', array('width'=>'1000', 'height'=>'500'));
}

echo '<div class="mediaViewDescription">' . $theMedia['Media']['description'] . '</div>';

?>