<?php echo $this->Form->create('Media.MediaGallery'); ?>
<?php if(isset($this->request->data['MediaGallery']['id'])) { echo $this->Form->input('MediaGallery.id'); } ?>
<?php echo $this->Form->input('MediaGallery.title', array('label' => 'Gallery Title', 'class' => 'form-control')); ?>
<?php echo $this->Form->input('MediaGallery.description', array('label' => 'Gallery Description', 'class' => 'form-control')); ?>

<?php echo $this->Element('Media.media_selector', array('media' => $this->request->data['Media'], 'multiple' => true)); ?>

<?php echo $this->Form->submit('Save Gallery'); ?>
<?php echo $this->Form->end(); ?>