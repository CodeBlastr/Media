<?PHP
/* @var $this View */
?>
<div id="media-edit" class="media edit">
    <h2>Edit Your Media</h2>

    <?php
    echo $this->Form->create('Media', array('type' => 'file'));
    echo $this->Form->input('Media.id');

    if($this->request->data['Media']['type'] == 'video') {
        // thumbnail selector
            $options = array(
                '0'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0000.jpg" height="200" width="200" />',
                '1'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0001.jpg" height="200" width="200" />',
                '2'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0002.jpg" height="200" width="200" />',
                '3'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0003.jpg" height="200" width="200" />',
                '4'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0004.jpg" height="200" width="200" />'
                );
            $attributes = array('legend'=>'Video Preview Image');
            echo $this->Form->radio('Media.thumbnail', $options, $attributes);
    }

    echo $this->Form->input('Media.title', array('between'=>'<br />','type'=>'text', 'label' => 'Title:'));

    echo $this->Form->input('Media.description', array('between'=>'<br />','type'=>'textarea', 'label' => 'Description:'));


    echo $this->Form->end('Save Changes');
    ?>
</div>