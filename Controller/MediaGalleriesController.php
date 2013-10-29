<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Courses'.DS.'Controller'.DS.'MediaController.php');
 */
class _MediaGalleriesController extends MediaAppController {

	public $name = 'MediaGalleries';
	public $uses = 'Media.MediaGallery';
	
	public $helpers = array('Media.Media');
	
	public $displayElements =  array(
		'jplayer_element' => 'JPlayer',
	);
	
	public function index() {
		$galleries = $this->MediaGallery->find('all', array(
			'conditions' => array()
		));
		$this->set('tagOptions', $this->displayElements);
		$this->set('galleries', $galleries);
	}

	public function add() {
		$this->view = 'add_edit';
		if ( !empty($this->request->data) ) {
			$this->request->data['User']['id'] = $this->Auth->user('id');
			if ( $this->MediaGallery->save($this->request->data) ) {
				$this->Session->setFlash('Media Gallery created.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to save this media gallery.');
			}
		}
	}

	/**
	 *
	 * @param char $uid The UUID of the media gallery in question.
	 */
	public function edit($uid = null) {
		$this->view = 'add_edit';
		$this->MediaGallery->id = $uid;
		if ( empty($this->request->data) ) {
			$this->MediaGallery->contain(array(
				'Thumbnail',
				'Media'
			));
			$this->request->data = $this->MediaGallery->findById($uid);
		} else {
			if ( $this->MediaGallery->save($this->request->data) ) {
				$this->Session->setFlash('Your media gallery has been updated.');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	/**
	 *
	 * @param char $mediaID The UUID of the media gallery in question.
	 */
	public function view($mediaID = null) {
		if ( $mediaID ) {
			$theMedia = $this->MediaGallery->find('first', array(
				'conditions' => array(
					'MediaGallery.id' => $mediaID
				),
				'contain' => array('User', 'Media')
			));

			$this->pageTitle = $theMedia['Media']['title'];
			$this->set('theMedia', $theMedia);
		}
	}

	public function my() {
		$userID = ($this->Auth->user('id')) ? $this->Auth->user('id') : false;
		if ( $userID ) {
			$this->request->data = $this->MediaGallery->find('all', array(
				'conditions' => array(
					'MediaGallery.modifier_id' => $userID,
				)
			));
			
		} else {
			$this->redirect('/');
		}
	}
	
	/**
	 * function for ajax request to retrieve media element by gallery id
	 * 
	 * 
	 */
	public function getGalleryMedia($galleryid) {
		$limit = isset($this->request->query['limit']) ? $this->request->query['limit'] : 48;
		$this->layout = null;
		$this->autoRender = false;
		if($galleryid) {
			$this->request->data = $this->MediaGallery->find('all', array(
					'conditions' => array('id' => $galleryid),
					'fields' => array('MediaGallery.id'), 
					'contain' => array('Media' => array('fields' => array('Media.extension', 'Media.filename', 'Media.id'), 'limit' => $limit, 'order' => 'RAND()')),
			));
		}
		//debug($this->request->data);break;
		$this->request->data = array('path' => $this->MediaGallery->Media->mediaUrl.'images/', 'Media' => $this->request->data[0]['Media']);
		return json_encode($this->request->data);
	}


/**
 * action that handles the canvasBuildrr
 * @etymology Late Middle English: from Old Northern French canevas, based on Latin cannabis ‘hemp,’ from Greek kannabis.
 */
	public function canvas($galleryId = null, $mediaId = null) {
		if ($this->request->isAjax()) {
			// handle presses of the canvas' save button
			$response = $this->MediaGallery->Media->updateCanvasObjects($this->request->data, $galleryId);
			$this->__returnJsonResponse($response);
		} else {
			// This is a brand new freeform canvas gallery.
			if (!($galleryId) && !($mediaId)) {
				// create gallery
				$newGallery = $this->MediaGallery->create(array(
					'title' => 'Untitled'
				));
				$this->MediaGallery->save($newGallery);
				// create a Media row foreach page
				for ($i=0; $i < 4; $i++) {
					$this->MediaGallery->Media->create();
					$this->MediaGallery->Media->save(array(
						'Media' => array(
							'filename' => '',
							'model' => 'Media'
						)
					), array('callbacks' => false));
					if ($i === 0) {
						// store the first page's id (Media.order), so we can redirect them later
						$firstMediaId = $this->MediaGallery->Media->id;
					}
					$this->MediaGallery->Media->MediaAttachment->create();
					$this->MediaGallery->Media->MediaAttachment->save(array(
						'MediaAttachment' => array(
							'model' => 'MediaGallery',
							'foreign_key' => $this->MediaGallery->id,
							'media_id' => $this->MediaGallery->Media->id,
							'creator_id' => $this->userId,
							'modifier_id' => $this->userId,
							'order' => $i
						)
					), array('callbacks' => false));
				}
				// redirect them to this galleries first page editor
				$this->redirect(array('action' => 'canvas', $this->MediaGallery->id, $firstMediaId));
			}
			if ($mediaId) {
				$this->request->data = $this->MediaGallery->find('first', array(
						'conditions' => array('MediaGallery.id' => $galleryId),
				));
				if (!empty($this->request->data['Media'])) {
					foreach ($this->request->data['Media'] as &$media) {
						if ($media['id'] === $this->passedArgs[1]) {
							// add the `id` into the data field, as this is the data used by the JavaScript..
							$media['data'] = json_decode($media['data']);
							$media['data']->id = $mediaId;
							$media['data'] = json_encode($media['data']);
						}
					}
				}
			} else {
				// $galleryId provided & $mediaId not provided.
				$this->request->data = $this->MediaGallery->find('first', array(
						'conditions' => array('MediaGallery.id' => $galleryId),
				));
				
				// check to see if they are trying to add more than the Media per Gallery limit (4)
				$mediaPerGalleryLimit = 4;
				if (count($this->request->data['Media']) >= $mediaPerGalleryLimit) {
					// redirect them to their current page 4
					$this->Session->setFlash('You have reached the maximum of '.$mediaPerGalleryLimit.' pages');
					$this->redirect(array('action' => 'canvas', $galleryId, $this->request->data['Media'][3]['id']));
				}
			}
		}
	}
	
/**
 * Creates a duplicate Gallety for the current user.
 * Does not duplicate the Media itself. The Media remains owned by it original owner.
 */
	public function duplicateGallery($id) {
		try {
			$myGalleryId = $this->MediaGallery->duplicate($id);
			$this->redirect(array('action' => 'canvas', $myGalleryId));
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->redirect($this->referer());
		}
	}

}

if (!isset($refuseInit)) {
	class MediaGalleriesController extends _MediaGalleriesController {}
}
