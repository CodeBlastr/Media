<div id="media-my" class="media my">
    <h2>My Media</h2>
    <p>Here is a listing of everything that you've uploaded so far.</p>

    <?php
    if(!empty($media)) {
            echo '<ul>';
            foreach($media as $medium) {
                #debug($medium);
                $thumbnailImage = !empty($medium['Media']['thumbnail']) ? '/media/media/thumbs/'.$medium['Media']['id'].'_000'.$medium['Media']['thumbnail'].'.png' : 'default.jpg';
                
                
                ?>
                    <li class="mediaIndexLi">
                        <div class="mediaIndexThumbnail">
                            <a href="/media/media/view/<?php echo $medium['Media']['id'] ?>"><img src="<?php echo $thumbnailImage ?>" alt="" height="200" width="200" /></a>
                        </div>
                        <div class="mediaIndexTitle">
                            <a href="/media/media/view/<?php echo $medium['Media']['id'] ?>"><?php echo !empty($medium['Media']['title']) ? $medium['Media']['title'] : '(untitled)' ?></a>
                        </div>
                        <div class="mediaIndexDescription">
                            <?php echo $medium['Media']['description'] ?>
                        </div>
                        <div id="media-my_actions">
                            <a href="/media/media/edit/<?php echo $medium['Media']['id'] ?>">Edit</a>
                            <?php
                            if($medium['Media']['is_visible'] == '1' && $medium['Media']['type'] == 'video') { // they need to choose a Thumbnail still
                                echo '<div class="error">Click Edit to customize the Preview Image for this video</div>';
                            }
                            ?>
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