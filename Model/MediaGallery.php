<?php
App::uses('MediaAppModel', 'Media.Model');

/**
 * Media Gallery Model.
 * 
 * Metadata for a collection of Media
 * 
 */

class AppMediaGallery extends MediaAppModel {
		
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
	 * Duplicates an entire gallery, that will be owned by the current logged in user.
	 * 
	 * The Media itself is not duplicated with this function.  The User gets their own MediaAttachments,
	 * so basically are just reusing the Media.  (Image parts of the canvas, in my case)
	 */
	public function duplicate($galleryId) {
		$mediaGallery = $this->find('first', array(
			'conditions' => array('id' => $galleryId),
			'contain' => array(
				'MediaAttachment',
				'Media'
			)
		));
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
			$attachment['MediaAttachment']['id'] = null;
			$attachment['MediaAttachment']['foreign_key'] = $this->id;
			$attachment['MediaAttachment']['creator_id'] = $attachment['MediaAttachment']['modifier_id'] = $this->userId; 
			$attachment['MediaAttachment']['created'] = $attachment['MediaAttachment']['modified'] = null; 
			$attachment = $this->MediaAttachment->create($attachment);
			if (!$this->MediaAttachment->save($attachment)) {
				throw new Exception("Error Processing Request", 1);
			}
		}
		return $this->id;
	}

/**
 * Generates a MediaGallery with $options['Media'] number of attached media
 */
	public function generate($options) {
		// create gallery
		$newGallery = $this->create(array(
			'title' => 'Untitled'
		));
		$this->save($newGallery);
		
		// create a Media row foreach page
		$mediaToGenerate = $options['Media'];
		for ($i=0; $i < $mediaToGenerate; $i++) {
			$this->Media->create();
			$this->Media->save(array(
				'Media' => array(
					'filename' => '',
					'model' => 'Media'
				)
			), array('callbacks' => false));
			if ($i === 0) {
				// store the first page's id (Media.order), so we can redirect them later
				$firstMediaId = $this->Media->id;
			}
			$this->Media->MediaAttachment->create();
			$this->Media->MediaAttachment->save(array(
				'MediaAttachment' => array(
					'model' => 'MediaGallery',
					'foreign_key' => $this->id,
					'media_id' => $this->Media->id,
					'creator_id' => $this->userId,
					'modifier_id' => $this->userId,
					'order' => $i
				)
			), array('callbacks' => false));
		}
		
		return $firstMediaId;
	}
	
}

if (!isset($refuseInit)) {
	class MediaGallery extends AppMediaGallery {}
}
