<?PHP
/* @var $this View */
?>
<h2>Submit Your Media</h2>

<?php
echo $this->Form->create('Media', array('type' => 'file'));
echo $this->Form->hidden('Media.user_id', array('value'=> $this->Session->read('Auth.User.id')));


$options = array('A'=>'Audio','V'=>'Video');
$attributes = array('legend'=>'Type of Media');
echo $this->Form->radio('Media.type', $options, $attributes);


echo $this->Form->input('Media.submittedfile', array('type'=>'file', 'label' => 'Upload a file from your computer:')); // , 'accept' => 'audio/* video/*'

echo $this->Form->input('Media.submittedurl', array('type'=>'text', 'label' => 'Alternatively enter the URL of a file that is already online:'));

echo $this->Form->input('Media.title', array('type'=>'text', 'label' => 'Title:'));

echo $this->Form->input('Media.description', array('type'=>'textarea', 'label' => 'Description:'));

echo $this->Form->end('Submit');
?>