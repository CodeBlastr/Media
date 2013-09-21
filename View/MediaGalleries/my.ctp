<?php 
	$mediaGalleries = $this->request->data;
?>
<div id="galleriesMyIndex">
<div class="table-responsive">
  <table class="table">
    <thead>
    	<tr>
    		<th>Gallery Thumbnail</th>
    		<th>Gallery Name</th>
    		<th>Gallery Descritpion</th>
    		<th>Created</th>
    		<th></th>
    	</tr>
    </thead>
    <tbody>
    	<?php foreach ($mediaGalleries as $gallery): ?>
    	<tr>
    		<td><?php echo isset($gallery['Thumbnail']) ? $gallery['Thumbnail'] : '' ?></td>
			<td><?php echo $gallery['MediaGallery']['title']; ?></td>
			<td><?php echo $this->Text->truncate($gallery['MediaGallery']['description'], 150); ?></td>
			<td><?php echo $this->Time->format('m/d/Y h:i A', $gallery['MediaGallery']['created']); ?></td>
			<td>
				<ul class="nav nav-pills">
					<li><?php echo $this->Html->link('view', array('action' => 'view', $gallery['MediaGallery']['id']), array('class' => 'btn btn-default btn-xs')); ?></li>
  					<li><?php echo $this->Html->link('edit', array('action' => 'edit', $gallery['MediaGallery']['id']), array('class' => 'btn btn-default btn-xs')); ?></li>
  					<li><?php echo $this->Html->link('delete', array('action' => 'delete', $gallery['MediaGallery']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete %s', $gallery['MediaGallery']['title'])); ?></li>
				</ul>
			</td>
		</tr>
		<?php endforeach; ?>
    </tbody>
  </table>
</div>	
</div>
