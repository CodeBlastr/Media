<?PHP
/* @var $this View */
?>
<h2>Submit Your Media</h2>

<?php
echo $this->Form->create('Media', array('type' => 'file'));


$options = array('A'=>'Audio','V'=>'Video');
$attributes = array('legend'=>'Type of Media');
echo $this->Form->radio('Media.type', $options, $attributes);

echo '<br />';

echo $this->Form->input('Media.submittedfile', array('between'=>'<br />','type'=>'file', 'label' => 'Upload a file from your computer:')); // , 'accept' => 'audio/* video/*'

echo $this->Form->input('Media.submittedurl', array('between'=>'<br />','type'=>'text', 'label' => 'Enter the URL of a file that is already online:'));

echo $this->Form->input('Media.title', array('between'=>'<br />','type'=>'text', 'label' => 'Title:'));

echo $this->Form->input('Media.description', array('between'=>'<br />','type'=>'textarea', 'label' => 'Description:'));


echo $this->Form->end('Submit');
?>