<?php
// default options
if (empty($height)) {
	$height = '';
}
if (empty($width)) {
	$width = '';
}
if (empty($id)) {
	$id = mt_rand(100000, 999999);
}
preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches);
$url = $matches[1]; ?>

<iframe width="<?php echo $width ?>" height="<?php echo $height ?>" src="//www.youtube.com/embed/<?php echo $url ?>" frameborder="0" allowfullscreen class="<?php echo $class ?>" id="<?php echo $id ?>"></iframe>