<?PHP
#debug($theMedia);

echo '<h2>';
echo !empty($theMedia['Media']['title']) ? $theMedia['Media']['title'] : '(untitled)';
echo '</h2>';

if($theMedia['Media']['type'] == 'audio') {
    echo $this->Html->video('/media/media/stream/'.$theMedia['Media']['id'], array('width'=>'450', 'height'=>'150'));
}
elseif($theMedia['Media']['type'] == 'video') {
    echo $this->Html->video('/media/media/stream/'.$theMedia['Media']['id'], array('width'=>'450', 'height'=>'300'));
}

echo '<div class="mediaViewDescription">' . $theMedia['Media']['description'] . '</div>';

?>