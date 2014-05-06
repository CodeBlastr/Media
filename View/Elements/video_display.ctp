<?php
// default options
if (empty($height)) {
	$height = '360px';
}
if (empty($width)) {
	$width = '640px';
}
if (empty($id)) {
	$id = mt_rand(100000, 999999);
}
?>

<video preload="none" poster="/media/img/video-icon.png"  src="<?php echo $url ?>" width="<?php echo $width ?>"  height="<?php echo $height ?>" class="<?php echo $class ?>" id="<?php echo $id ?>" controls>
	If at all possible, please upgrade your browser.
</video>
