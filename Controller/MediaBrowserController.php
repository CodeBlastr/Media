<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Media'.DS.'Controller'.DS.'MediaController.php');
 */

/**
 * This is the REST API for the Media Browser
 * 
 *
 */


class _MediaBrowserController extends MediaAppController {

	public $name = 'Media';
	public $uses = array('Media.Media', 'Media.MediaGallery');
	public $helpers = array('Media.Media');
	
	/**
	 * Filebrowser Action
	 * Supports Ajax
	 * All this does is return the filebrower view
	 * 
	 */
	
	public function filebrowser($galleryid = false) {
		
		if(isset($this->request->query['selected'])) {
			$selected = $this->request->query['selected'];
		}else {
			$selected = 0;
		}
	
		$galleryid = isset($this->request->query['galleryid']) ? $this->request->query['galleryid'] : array();
		
		$this->set('galleryid', $galleryid);
		$this->set('galleries', $this->MediaGallery->find('list'));
	
		if($this->request->isAjax()) {
			$this->layout = null;
		}
	
	}
	
	/**
	 * Index of the media. Only gives back the media owned by the person viewing the
	 * media Should only be called with AJAX
	 */
	public function index() {
		$conditions = array();
		if($this->Session->read('Auth.User.user_role_id') != 1) {
			$conditions['creator_id'] = $this->userId;
		}
	}
	
}

if (!isset($refuseInit)) {
	class MediaBrowserController extends _MediaBrowserController{}
}