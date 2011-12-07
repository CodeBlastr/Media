<div id="media-index" class="media view">
    <h2>Media</h2>

    <?php
    if(!empty($media)) {
            echo '<ul>';
            foreach($media as $medium) {
                #debug($medium);
                $thumbnailImage = !empty($medium['Media']['thumbnail']) ? '/theme/default/media/thumbs/'.$medium['Media']['id'].'_000'.$medium['Media']['thumbnail'].'.png' : '/theme/default/media/default.jpg';
                ?>
                    <li class="mediaIndexLi">
                        <div class="mediaIndexThumbnail">
                            <a href="/media/media/view/<?php echo $medium['Media']['id'] ?>"><img src="<?php echo $thumbnailImage ?>" alt="<?php echo $medium['Media']['title'] ?>" height="200" width="200"/></a>
                        </div>
                        <div class="mediaIndexTitle">
                            <a href="/media/media/view/<?php echo $medium['Media']['id'] ?>"><?php echo !empty($medium['Media']['title']) ? $medium['Media']['title'] : '(untitled)' ?></a>
                        </div>
                        <div class="mediaIndexDescription">
                            <?php echo $medium['Media']['description'] ?>
                        </div>
                    </li>
                <?php
            }//foreach()
            echo '</ul>';
    }//if(videos)
    else {
            echo '<div class="error">No media found.</div>';
    }
    ?>
</div>