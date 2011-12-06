<?PHP
/* @var $this View */
?>
<div id="media-edit" class="media edit">
    <h2>Edit Your Media</h2>

    <?php
    echo $this->Form->create('Media', array('type' => 'file'));
    echo $this->Form->input('Media.id');


    $options = array('A'=>'Audio','V'=>'Video');
    $attributes = array('legend'=>'Type of Media');
    echo $this->Form->radio('Media.type', $options, $attributes);


    echo '<br />';


    if($this->request->data['Media']['type'] == 'video') {
        // thumbnail selector
        if($this->request->data['Media']['is_visible'] == '1') {  // they need to choose a Thumbnail still
            
            $options = array(
                '0'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0000.png" height="200" width="200" />',
                '1'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0001.png" height="200" width="200" />',
                '2'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0002.png" height="200" width="200" />',
                '3'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0003.png" height="200" width="200" />',
                '4'=>'<img src="/theme/default/media/thumbs/'.$this->data['Media']['id'].'_0004.png" height="200" width="200" />'
                );
            $attributes = array('legend'=>'Video Preview Image');
            echo $this->Form->radio('Media.thumbnail', $options, $attributes);
            
        }
    }

    echo $this->Form->input('Media.title', array('between'=>'<br />','type'=>'text', 'label' => 'Title:'));

    echo $this->Form->input('Media.description', array('between'=>'<br />','type'=>'textarea', 'label' => 'Description:'));


    echo $this->Form->end('Save Changes');
    ?>
</div>