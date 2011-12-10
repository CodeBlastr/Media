<?PHP
/* @var $this View */


#debug($theMedia);


// what formats did we receive from the encoder?
$outputs = json_decode($theMedia['Media']['filename'], true);

// audio files have 1 output currently.. arrays are not the same.. make them so.
/** @todo this is kinda hacky.. also exists in Media::Stream **/
if(!is_array($outputs[0])) {
	$temp['outputs'] = $outputs['outputs'];
	$outputs = null;
	$outputs['outputs'][0] = $temp['outputs'];
}

foreach ($outputs['outputs'] as $output) {
    $outputArray[] = 'http://' . $_SERVER['HTTP_HOST'] . '/media/media/stream/' . $theMedia['Media']['id'] . '/' . $output['label'];
}
#debug($outputArray);

// load the star ratings files
$this->Html->script('/ratings/js/jquery.ui.stars.min', null, array('inline'=>false));
$this->Html->css('/ratings/css/jquery.ui.stars.min', null, array('inline' => false));
?>

<div id="mediaViewBox">

    <?php
    if($theMedia['Media']['type'] == 'audio') {
        echo $this->Html->video($outputArray, array('width'=>'709', 'height'=>'404'));
    }
    elseif($theMedia['Media']['type'] == 'video') {
        echo $this->Html->video($outputArray, array('width'=>'709', 'height'=>'404', 'poster'=>'/theme/default/media/thumbs/'.$theMedia['Media']['id'].'_000'.$theMedia['Media']['thumbnail'].'.png'));
    }
    ?>

    <div id="mediaView_titleBox">
        <div id="mediaView_titleInfo">
            <?php
            echo '<h2>';
            echo !empty($theMedia['Media']['title']) ? $theMedia['Media']['title'] : '(untitled)';
            echo '</h2>';
            ?>
        </div><!-- #mediaView_titleInfo -->
        <div id="mediaView_ratingBox">
            <?php
            #debug($this->passedArgs);
            echo $this->Rating->display(array(
                'item' => $theMedia['Media']['id'],
                'type' => 'radio',
                'stars' => 10,
                'value' =>  $theMedia['Media']['rating'],
                'createForm' => array('url' => array($theMedia['Media']['id'], 'rate' =>  $theMedia['Media']['id'], 'redirect' => false), 'label'=>false)
                ));
            ?>
        </div>
    </div>

    <?php echo '<div class="mediaViewDescription">' . $theMedia['Media']['description'] . '</div>'; ?>

</div><!-- #MediaMediaBox -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#MediaViewForm').stars({
            split:2,
            cancelShow:false,
            callback: function(ui, type, value) {
                ui.$form.submit();
            }
        });
    });
</script>