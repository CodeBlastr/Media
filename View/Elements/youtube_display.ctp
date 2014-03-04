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

$pattern =
	'%^# Match any youtube URL
	(?:https?://)?  # Optional scheme. Either http or https
	(?:www\.)?      # Optional www subdomain
	(?:             # Group host alternatives
	  youtu\.be/    # Either youtu.be,
	| youtube\.com  # or youtube.com
	  (?:           # Group path alternatives
		/embed/     # Either /embed/
	  | /v/         # or /v/
	  | /watch\?v=  # or /watch\?v=
	  )             # End path alternatives.
	)               # End host alternatives.
	([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
	$%x'
	;
$result = preg_match($pattern, $url, $matches);
$url = $matches[1];
?>

<iframe width="<?php echo $width ?>" height="<?php echo $height ?>" src="//www.youtube.com/embed/<?php echo $url ?>" frameborder="0" allowfullscreen class="<?php echo $class ?>" id="<?php echo $id ?>"></iframe>