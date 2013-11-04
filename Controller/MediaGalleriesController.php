<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Media'.DS.'Controller'.DS.'MediaGalleriesController.php');
 */
class AppMediaGalleriesController extends MediaAppController {

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
				),
				'order' => array('MediaGallery.created' => 'DESC')
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

		$this->request->data = array('path' => $this->MediaGallery->Media->mediaUrl.'images/', 'Media' => $this->request->data[0]['Media']);
		return json_encode($this->request->data);
	}


/**
 * action that handles the canvasBuildrr
 * 
 * @etymology Late Middle English: from Old Northern French canevas, based on Latin cannabis ‘hemp,’ from Greek kannabis.
 * @param char $galleryId
 * @param char $mediaId
 */
	public function canvas($galleryId = null, $mediaId = null) {
		if ($this->request->isAjax()) {
			// handle presses of the canvas' save button
			$response = $this->MediaGallery->Media->updateCanvasObjects($this->request->data, $galleryId);
			$this->__returnJsonResponse($response);
		} else {
			// No parameters passed. This is a brand new freeform canvas gallery.
			if (!($galleryId) && !($mediaId)) {
				// generate a gallery w/ 4 attached Media
				$firstMediaId = $this->MediaGallery->generate(array('Media' => 4));
				// redirect them to this gallery's first page editor
				$this->redirect(array('action' => 'canvas', $this->MediaGallery->id, $firstMediaId));
			}
			
			// A mediaId was specified.  Find it and return it's data.
			if ($mediaId) {
				$this->request->data = $this->MediaGallery->find('first', array(
					'conditions' => array('MediaGallery.id' => $galleryId)
				));
				#dirty
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
				// $galleryId provided & $mediaId not provided.  Redirect them to their page 1 editor.
				$this->request->data = $this->MediaGallery->find('first', array(
					'conditions' => array('MediaGallery.id' => $galleryId)
				));
				$this->redirect(array('action' => 'canvas', $galleryId, $this->request->data['Media'][0]['id']));
			}
		}
	}


	public function printCanvas($id, $autoDownload = true) {
		$this->request->data = $this->MediaGallery->find('first', array(
			'conditions' => array('MediaGallery.id' => $id)
		));
		$this->layout = false;
		
		$this->WkHtmlToPdf = $this->Components->load('WkHtmlToPdf');
		$this->WkHtmlToPdf->initialize($this);
		$pdfLocation = $this->WkHtmlToPdf->createPdf($autoDownload);
		
		if (!$autoDownload) {
			return $pdfLocation;
		}
	}

	
/**
 * Creates a duplicate Gallery for the current user.
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
	class MediaGalleriesController extends AppMediaGalleriesController {}
}
