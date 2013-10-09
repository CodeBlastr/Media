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
	
	public $allowedActions = array('index');
	
	public $viewPath = '/MediaBrowser';
	
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
	public function media($id = null) {
		if($this->request->is('get')) {
			$conditions = array();
			if(isset($this->request->query['limit'])) {
				$conditions['limit'] = $this->request->query['limit'];
			}
			if(isset($this->request->query['type']) && $this->request->query['type'] != 'all') {
				$conditions['conditions']['type'] = $this->request->query['type'];
			}
			if(isset($this->request->query['order'])) {
				$conditions['order'] = $this->request->query['order'];
			}else {
				$conditions['order'] = array('created DESC');
			}
			if($this->Session->read('Auth.User.user_role_id') != 1) {
				$conditions['conditions']['creator_id'] = $this->userId;
			}
			
			$media = $this->Media->find('all', $conditions);
			$this->request->data = array();
			foreach($media as $m) {
				$m['Media']['type'] = $this->Media->mediaType($m['Media']['extension']);
				$m = $this->Media->save($m, array('callbacks' => false));
				$this->request->data[] = $m['Media'];
			}
			if($this->request->is('ajax')) {
				$this->autoRender = false;
				return json_encode($this->request->data);
			}
		}
		
		if($this->request->is('put')) {
			$media['Media'] = $this->request->data;
			if($this->Media->save($media, array('callbacks' => false))) {
				$this->response->statusCode(200);
			}else {
				$this->response->statusCode(500);
			}
		}
		
		if($this->request->is('delete')) {
			$media = $this->Media->findById($id);
			$filename = $this->Media->themeDirectory.DS.$media['Media']['type'].DS.$media['Media']['filename'].'.'.$media['Media']['extension'];
			$this->response->statusCode(200);
			if(unlink($filename)) {
				if(!$this->Media->delete($id)) {
				   $this->response->statusCode(500);
				}
			}else {
				$this->response->statusCode(500);
			}
		}
	}
	
	public function upload() {
		$this->layout = false;
		$this->autoRender = false;
		if (!empty($this->request->data)) {
		 try{
			if(isset($this->request->data['MediaAttachment'])) {
				$this->loadModel('Media.MediaAttachment');
			}
				
			$this->request->data['User']['id'] = $this->Auth->user('id');
			$mediaarray = array();
			foreach($this->request->data['Media']['files'] as $file) {
				$media['Media'] = array(
						'user_id' => $this->Auth->user('id'),
						'filename' => $file,
						'title' => $file['name']
				);
				$this->Media->create();
				$media = $this->Media->save($media);
				if(isset($this->request->data['MediaAttachment'])) {
					$attachedmedia = array(
							'MediaAttachment' => array(
									'media_id' => $media['Media']['id'],
									'model' => $this->request->data['MediaAttachment']['model'],
									'foreign_key' => $this->request->data['MediaAttachment']['foreign_key'],
							));
					$this->MediaAttachment->create();
					$media = $this->MediaAttachment->save($attachedmedia);
				}
		
				if($media) {
					$mediaarray[] = $media['Media'];
				}
			}
			$this->response->statusCode(200);
			if(!empty($mediaarray)) {
				$this->layout = false;
				$this->autoRender = false;
				return json_encode($mediaarray);
			}else {
				return 'No Files Uploaded';
			}
		 }catch(Exception $e) {
		 	$this->response->statusCode(500);
		 	return 'Error: '.$e->getMessage();
		 }
				
		}
	}
	
}

if (!isset($refuseInit)) {
	class MediaBrowserController extends _MediaBrowserController{}
}