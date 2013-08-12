<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Courses'.DS.'Controller'.DS.'MediaController.php');
 */
class _MediaGalleriesController extends MediaAppController {

	public $name = 'MediaGalleries';
	public $uses = 'Media.MediaGallery';
	public $allowedActions = array('index', 'view', 'my', 'add', 'edit');
	public $helpers = array('Media.Media');

	public function index() {
		$galleries = $this->MediaGallery->find('all', array(
			'conditions' => array()
		));
		$this->set('galleries', $galleries);
	}

	public function add() {
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
		$this->MediaGallery->id = $uid;
		if ( empty($this->request->data) ) {
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
				'contain' => 'User'
			));

			$this->pageTitle = $theMedia['Media']['title'];
			$this->set('theMedia', $theMedia);
		}
	}

	public function my() {
		$userID = ($this->Auth->user('id')) ? $this->Auth->user('id') : false;
		if ( $userID ) {
			$allMedia = $this->MediaGallery->find('all', array(
				'conditions' => array(
					'MediaGallery.user_id' => $userID,
				)
			));
			$this->set('media', $allMedia);
		} else {
			$this->redirect('/');
		}
	}

}

if ( !isset($refuseInit) ) {

	class MediaGalleriesController extends _MediaGalleriesController {
		
	}

}
