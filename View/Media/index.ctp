<h2>Media</h2>

<?php
if(!empty($media)) {
	echo '<ul>';
	foreach($media as $medium) {
		echo '<li>'
			.	'<div><a href="' . $medium['filename'] . '">' . $medium['title'] . '</a></div>'
			.	'<div>' . $medium['description'] . '</div>'
			.'</li>';
	}//foreach()
	echo '</ul>';
}//if(videos)
else {
	echo '<div>No media found.</div>';
}
?>