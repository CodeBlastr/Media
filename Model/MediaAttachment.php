<?php
App::uses('MediaAppModel', 'Media.Model');

/**
 * Media Attachments Model.
 * 
 * Controls what media is attached to what model.
 * We are using a seperate Model for this so Multiple Media Items can
 * be attached to a Model
 * 
 */

class MediaAttachment extends MediaAppModel {
		
	public $name = 'MediaAttachment';

	public $hasMany = array(
		'Media' => array(
			'className' => 'Media.Media',
			'foreignKey' => 'media_id'
		)
	);
}