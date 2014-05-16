<?php // note : twitter bootstrap is needed to run this ?>
<?php $options = $dataForView; ?>
<?php unset($options['defaultTemplate']); // its just too much to read in a debug :) ?>
<?php if (count($options['data']['Media']) > 1) : ?>
	<div id="myCarousel" class="carousel slide" data-pause="hover" data-interval="5000">
		<!-- Carousel items -->
		<div class="carousel-inner">
	        <?php for($i = 0; $i < count($options['data']['Media']); $i++) : ?>
			<div class="<?php echo $i == 0 ? 'active' : null; ?> item">
				<?php echo $this->Media->display($options['data']['Media'][$i], $options); ?>
			</div>
			<?php endfor; ?>
		</div>
		<!-- Carousel nav -->
		<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		<div class="carousel-indicators row-fluid">
	        <?php for($i = 0; $i < count($options['data']['Media']); $i++) : ?>
			<div data-target="#myCarousel" data-slide-to="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : null; ?> pull-left">
				<?php echo $this->Media->display($options['data']['Media'][$i], array('width' => '50', 'height' => 50)); ?>
			</div>
			<?php endfor; ?>
		</div>
	</div>
<?php else : ?>
	<?php // would like to pass parames directly from element('carousel', $options) to here ?>
	<?php echo $this->Media->display($options['data']['Media'][0], $options); ?>
<?php endif; ?>