<?php if ( empty($galleries) ): ?>

<div class="well">
	no galleries found
</div>

<?php else: ?>

	<div class="table-responsive">
  <table class="table">
    	<thead>
    		<tr>
    			<th>Gallery Name</th>
    			<th>Gallery Description</th>
    			<th></th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php foreach ($galleries as $gallery): ?>
    		<tr>
	    		<td><?php echo $this->Html->link($gallery['MediaGallery']['title'], array('plugin'=>'users','controller'=>'user_groups' , 'action'=>'view' , $gallery['MediaGallery']['id'])); ?>&nbsp;</td>
				<td><?php echo $gallery['MediaGallery']['description']; ?>&nbsp;</td>
				<td class="actions" style="padding: 5px;">
					<?php echo $this->Html->link('<span class="glyphicon glyphicon-eye-open"></span>', array('action' => 'view', $gallery['MediaGallery']['id']),  array('class' => 'btn btn-default btn-xs', 'escape' => false)); ?>
					<?php echo $this->Html->link('<span class="glyphicon glyphicon-edit"></span>', array('action' => 'edit', $gallery['MediaGallery']['id']), array('class' => 'btn btn-default btn-xs', 'escape' => false)); ?>
					<?php //echo $this->Html->link(__('Delete'), array('action' => 'delete', $gallery['MediaGallery']['id']),  array('class' => 'btn btn-default btn-xs', 'escape' => false), sprintf(__('Are you sure you want to delete # %s?', true), $gallery['MediaGallery']['id'])); ?>
				</td>
    		</tr>
    		<?php endforeach; ?>
    	</tbody>
  </table>
</div>

	
<?php endif; ?>
