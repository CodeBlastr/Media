<?php

/**
 * Media Attachments Model.
 * 
 * Controls what media is attached to what model.
 * 
 */

class MediaGallery extends MediaAppModel {
		
	public $name = 'MediaGallery';
	public $actsAs = array('Media.MediaAttachable');
	
	 public $hasOne = array(
        'Thumbnail' => array(
            'className' => 'Media.Media',
        	'foreignKey' => false,
            'conditions' => array('Thumbnail.id' => 'MediaGallery.thumbail'),
            'dependent' => true
        )
    );
	 
	 public $belongsTo = array(
 		'Creator' => array(
 				'className' => 'Users.User',
 				'foreignKey' => 'creator_id',
 		),
 		'Modifier' => array(
 				'className' => 'Users.User',
 				'foreignKey' => 'modifier_id',
 		)
	 );
	
}