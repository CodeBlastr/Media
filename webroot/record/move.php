<?php

if (file_exists('/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/myvideo.flv')) {
	$name = rand(5, 15);
	if(rename("/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/myvideo.flv", "/home/razorit/razorit.com/projects/video/test/abc".$name.".flv")) {
		echo '<a href="http://www.razorit.com/projects/video/text/abc'.$name.'.flv"> Right click to download </a>';
	} else {
		echo 'File could not be moved';
	}
} else {
	echo 'File does not exist.';
}

?>