<?php
if ( empty($galleries) ) {
?>

<div class="well">
	no galleries found
</div>

<?php
} else {

	foreach ( $galleries as $gallery ) {
		debug( $gallery );
	}

}
