<div id="media-my" class="media my">
    <h2>My Media</h2>

    <?php
    if(!empty($media)) {
            echo '<ul>';
            foreach($media as $medium) {
                #debug($medium);
                ?>
                    <li class="mediaIndexLi">
                        <div class="mediaIndexThumbnail">
                            <a href="/media/media/view/<?php echo $medium['Media']['id'] ?>"><img src="<?php echo !empty($medium['Media']['thumbnail']) ? $medium['Media']['thumbnail'] : 'default.gif' ?>" alt="" /></a>
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