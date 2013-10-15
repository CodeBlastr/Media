<?php

/**
 * Media Gallery Model.
 * 
 * Metadata for a collection of Media
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
	
	
	/**
	 * Duplicates an entire gallery, that will be owned by the current logged in user
	 */
	public function duplicate($galleryId) {
		$mediaGallery = $this->find('first', array(
			'conditions' => array('id' => $galleryId),
			'contain' => array(
				'MediaAttachment',
				'Media'
			)
		));
		debug($mediaGallery);break;
		$mediaAttachz = $this->MediaAttachment->find('all', array(
			'conditions' => array(
				'model' => 'MediaGallery',
				'foreign_key' => $galleryId
			)
		));
		$this->create();
		if (!$this->save(array(
			'title' => 'Copy of ' . $mediaGallery['MediaGallery']['title']
		))) {
			throw new Exception("Error Processing Request", 1);
		}
		foreach ($mediaAttachz as $attachment) {
			$attachment['id'] = null;
			$attachment['foriegn_key'] = $this->id;
			$attachment['creator_id'] = $attachment['modifier_id'] = $this->userId; 
			$this->MediaAttachment->create();
			if (!$this->MediaAttachment->save($attachment)) {
				throw new Exception("Error Processing Request", 1);
			}
		}
		return $this->id;
	}
	
}