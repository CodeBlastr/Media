<?php if ( empty($galleries) ): ?>

<div class="well">
	no galleries found
</div>

<?php else: ?>
<div class="actions pull-right">
	<?php echo $this->Html->link('Add Gallery', array('action' => 'add'), array('class' => 'btn btn-default')); ?>
</div>
<div class="clearfix"></div>
	<div class="table-responsive">
  <table class="table">
    	<thead>
    		<tr>
    			<th>Gallery Name</th>
    			<th>Gallery Description</th>
    			<th>Generate Tag</th>
    			<th></th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php foreach ($galleries as $gallery): ?>
    		<tr>
	    		<td><?php echo $this->Html->link($gallery['MediaGallery']['title'], array('action'=>'view' , $gallery['MediaGallery']['id'])); ?>&nbsp;</td>
				<td><?php echo $gallery['MediaGallery']['description']; ?>&nbsp;</td>
				<td><?php echo $this->Form->select(null, $tagOptions, array('data-id' => $gallery['MediaGallery']['id'], 'class' => 'tag-selector') ); ?>
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

<script type="text/javascript">

	$(document).ready(function() {
		$('.tag-selector').change(function(e) {
			e.preventDefault();
			if($(this).val() != '') {
			var id = $(this).data('id');
			var tagtext = '&#123;element: Media.'+$(this).val()+' galleryId='+id+'&#125;';
			var html = '<p>Copy and paste the text below anywhere in the editor</p><br>'+tagtext;
			$('#tagModal .modal-body').html(html);
			$('#tagModal').modal('show');
			$(this).val('')
			}
		});
	});
	

</script>

<div id="tagModal" class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

	
<?php endif; ?>
