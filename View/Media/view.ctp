<?PHP
#debug($theMedia);
 // what formats did we receive from the encoder?
$outputs = json_decode($theMedia['filename'], true);
foreach ($outputs['outputs'] as $output) {
    $outputArray[] = 'http://' . $_SERVER['HTTP_HOST'] . '/media/media/stream/' . $theMedia['Media']['id'] . '/' . $output['label'];
}

echo '<h2>';
echo !empty($theMedia['Media']['title']) ? $theMedia['Media']['title'] : '(untitled)';
echo '</h2>';


if($theMedia['Media']['type'] == 'audio') {
    echo $this->Html->video($outputArray, array('width'=>'450', 'height'=>'150'));
}
elseif($theMedia['Media']['type'] == 'video') {
    echo $this->Html->video($outputArray, array('width'=>'450', 'height'=>'300'));
}


echo '<div class="mediaViewDescription">' . $theMedia['Media']['description'] . '</div>';