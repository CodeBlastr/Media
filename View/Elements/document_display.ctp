<?php
	/**
	 * Document Display Element
	 * 
	 * You MUST ECHO THE ID IN THE TOP DIV for the media selector to work!
	 */
?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>">
	<?php echo $this->Html->link($url); ?>
</div>