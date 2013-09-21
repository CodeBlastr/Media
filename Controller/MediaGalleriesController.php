<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Courses'.DS.'Controller'.DS.'MediaController.php');
 */
class _MediaGalleriesController extends MediaAppController {

	public $name = 'MediaGalleries';
	public $uses = 'Media.MediaGallery';
	
	public $helpers = array('Media.Media');

	public function index() {
		$galleries = $this->MediaGallery->find('all', array(
			'conditions' => array()
		));
		$this->set('galleries', $galleries);
	}

	public function add() {
		$this->view = 'add_edit';
		if ( !empty($this->request->data) ) {
			$this->request->data['User']['id'] = $this->Auth->user('id');
			if ( $this->MediaGallery->save($this->request->data) ) {
				$this->Session->setFlash('Media Gallery created.');
				$this->redirect(array('action' => 'my'));
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
				$this->redirect(array('action' => 'my'));
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

}

if ( !isset($refuseInit) ) {

	class MediaGalleriesController extends _MediaGalleriesController {
		
	}

}
