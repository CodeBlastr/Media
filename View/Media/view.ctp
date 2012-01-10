<?PHP
/* @var $this View */


#debug($theMedia);


//default image
$thumbnailImage = !empty($theMedia['Media']['thumbnail']) ? '/theme/default/media/'.strtolower(ZuhaInflector::pluginize($theMedia['Media']['model'])).'/images/thumbs/'.$theMedia['Media']['id'].'_000'.$theMedia['Media']['thumbnail'].'.jpg' : '/img/noImage.jpg';


// load the star ratings files
echo $this->Html->script('/ratings/js/jquery.ui.stars.min');
echo $this->Html->css('/ratings/css/jquery.ui.stars.min');
?>

<div id="mediaViewBox">

    <?php
    if($theMedia['Media']['type'] == 'audio') {
        echo $this->Html->video($theMedia['Media']['filename'], array('width'=>'709', 'height'=>'404', 'title'=>$theMedia['Media']['title']));
    }
    elseif($theMedia['Media']['type'] == 'videos') {
        echo $this->Html->video($theMedia['Media']['filename'], array('width'=>'709', 'height'=>'404', 'poster'=>$thumbnailImage, 'title'=>$theMedia['Media']['title']));
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
            #debug($theMedia['Media']['rating']);
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