<?PHP
/* @var $this View */


#debug($theMedia);


// what formats did we receive from the encoder?
$outputs = json_decode($theMedia['Media']['filename'], true);
foreach ($outputs['outputs'] as $output) {
    $outputArray[] = 'http://' . $_SERVER['HTTP_HOST'] . '/media/media/stream/' . $theMedia['Media']['id'] . '/' . $output['label'];
}
?>

<?php
$this->Html->script('/ratings/js/jquery.ui.stars.min', null, array('inline'=>false));
$this->Html->css('/ratings/css/jquery.ui.stars.min', null, array('inline' => false));
?>

<div id="mediaViewBox">

    <?php
    if($theMedia['Media']['type'] == 'audio') {
        echo $this->Html->video($outputArray, array('width'=>'709', 'height'=>'404'));
    }
    elseif($theMedia['Media']['type'] == 'video') {
        echo $this->Html->video($outputArray, array('width'=>'709', 'height'=>'404', 'poster'=>'/media/media/thumbs/'.$medium['Media']['id'].'_000'.$medium['Media']['thumbnail'].'.png'));
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