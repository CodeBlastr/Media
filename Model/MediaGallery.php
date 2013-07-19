<?php

/**
 * Media Attachments Model.
 * 
 * Controls what media is attached to what model.
 * 
 */

class MediaGallery extends MediaAppModel {
		
	public $name = 'MediaGallery';
	
	public $hasMany = array(
		'Media' => array(
            'className'     => 'Media.Media',
            'foreignKey'    => 'foreign_key',
            'dependent'     => true
        )
	);
	
}