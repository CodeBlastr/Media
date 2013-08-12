<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Courses'.DS.'Controller'.DS.'MediaController.php');
 */


class _MediaController extends MediaAppController {

	public $name = 'Media';
	public $uses = 'Media.Media';
	public $allowedActions = array('index', 'view', 'notification', 'stream', 'my', 'add', 'edit', 'sorted', 'record');
	public $helpers = array('Media.Media');
/**
 * kinda expects URL to be: /media/media/index/(audio|video)
 * shows media of the type passed in the request
 */
	public function index() {
		#debug($this->request->pass);
		if(isset($this->request->pass[0])) {
			$mediaType = $this->request->pass[0];
		}
		$allMedia = $this->Media->find('all', array(
			'conditions' => array(
				'Media.filename !=' => '',
				'Media.is_visible' => '1', // 0 = not on our server; 1 = good to go
				'Media.type' => $mediaType
				)
			));
		$this->set('media', $allMedia);
	}//index()


	public function add() {
		if(!empty($this->request->data)) {
            $this->request->data['User']['id'] = $this->Auth->user('id');
			#debug($this->request->data);break;
			if ($this->Media->save($this->request->data)) {
				$this->Session->setFlash('Media saved.');
				#$this->redirect('/media/media/edit/'.$this->Media->id);
				if($this->request->isAjax()) {
					$this->set('media', $this->Media->findById($this->Media->id));
					$this->layout = false;
					$this->view = 'ajax-upload';
				}else {
					$this->redirect(array('action' => 'my'));
				}
				
			} else {
				
				if($this->request->isAjax()) {
					throw new InternalErrorException('Upload Failed');
				}else {
					$this->Session->setFlash('Invalid Upload.');
				}
				
			}
		}

	}//upload()


/**
 *
 * @param char $uid The UUID of the media in question.
 */
	public function edit($uid = null) {
    	$this->Media->id = $uid;
		if (empty($this->request->data)) {
			$this->request->data = $this->Media->findById($uid);
		} else {
            // save the new media metadata
            if ($this->Media->save($this->request->data)) {
                $this->Session->setFlash('Your media has been updated.');
                $this->redirect(array('action' => 'my'));
            }
		}
	}//edit()



/**
 *
 * @param char $mediaID The UUID of the media in question.
 */
	public function view($mediaID = null) {
		if($mediaID) {
            // Increase the Views by 1
            $this->Media->updateAll(array('Media.views'=>'Media.views+1'), array('Media.id'=>$mediaID));

			// Use this to save the Overall Rating to Media.rating
			$this->Media->calculateRating($mediaID, 'rating');

			$theMedia = $this->Media->find('first', array(
				'conditions' => array(
                    'Media.id' => $mediaID
                    ),
				'contain' => 'User'
				));

			$this->pageTitle = $theMedia['Media']['title'];
			$this->set('theMedia', $theMedia);
		}
	}//view()


	public function my() {
		$userID = ($this->Auth->user('id')) ? $this->Auth->user('id') : false;
		if($userID) {
			$allMedia = $this->Media->find('all', array(
				'conditions' => array(
					'Media.user_id' => $userID,
					#'Media.type' => $mediaType
					)
				));
			$this->set('media', $allMedia);
		} else {
			$this->redirect('/');
		}
	}//my()


/**
 * This action can stream or download a media file.
 * Expected Use: /media/media/stream/{UUID}/{FORMAT}
 * @param char $mediaID The UUID of the media in question.
 * @param string $requestedFormat The filetype of the media expected.
 */
	function stream($mediaID = null, $requestedFormat = FALSE) {
		#debug($this->request->params);break;
		if($mediaID && $requestedFormat) {

			// find the filetype
			$theMedia = $this->Media->findById($mediaID);

			foreach($theMedia['Media']['ext'] as $outputExtension) {
				if($outputExtension == $requestedFormat) $outputTypeFound = true;
			}

			if($outputTypeFound) {
				// yes, we should have this media in the requested format

				if(!empty($theMedia['Media']['type'])) {
					// determine what data to send to the browser

					if($theMedia['Media']['type'] == 'audio') {

						switch($requestedFormat) {
							case ('mp3'):
								$filetype = array('extension' => 'mp3', 'mimeType' => array('mp3' => 'audio/mp3'));
								break;
							case ('ogg'):
								$filetype = array('extension' => 'ogg', 'mimeType' => array('ogg' => 'audio/ogg'));
								break;
						}//switch()
					} elseif($theMedia['Media']['type'] == 'video') {

						switch($requestedFormat) {
							case ('mp4'):
								$filetype = array('extension' => 'mp4', 'mimeType' => array('mp4' => 'video/mp4'));
								break;
							case ('webm'):
								$filetype = array('extension' => 'webm', 'mimeType' => array('mp4' => 'video/webm'));
								break;
						}//switch()

					}// audio/video

					if(isset($filetype)) { /** @todo break up to stream & to download & do download data updating **/
						// send the file to the browser
						$this->viewClass = 'Media'; // <-- magic!
						$params = array(
							'id' => $mediaID . '.' . $filetype['extension'], // this is the full filename.. perhaps the one shown to the user if they download
							'name' => $mediaID, // this is the filename minus extension
							'download' => false, // if true, then a download box pops up
							'extension' => $filetype['extension'],
							'mimeType' => $filetype['mimeType'],
							'path' => ROOT.DS.SITE_DIR.DS.'Locale'.DS.'View'.DS.WEBROOT_DIR . DS . 'media' . DS . 'streams' . DS . $theMedia['Media']['type'] . DS
                           );

						$this->set($params);

					}
				}//if(Media.type)

			} else {
                /** 404 **/
			}

		}//if($mediaID && $requestedFormat)

	}//stream()


	/**
	 * ACTION FOR ELEMENTS
	 */

	/**
	 *
	 * @param string $mediaType
	 * @param string $sortOrder
	 * @param integer $numberOfResults
	 * @return array|boolean
	 */
	function sorted($mediaType, $field, $sortOrder, $numberOfResults) {
#debug('Media.'.$field.' '.strtoupper($sortOrder));
	    $options = array(
          'conditions' => array(
		    'Media.type' => strtolower($mediaType),
		    'Media.is_visible' => '1'
          ),
          'order' => array('Media.'.$field => $sortOrder),
          //'order' => array('Media.id' => 'desc'),

          'limit' => $numberOfResults
	    );

	    return $this->Media->find('all', $options);

	}//sorted()



/**
 * record video
 */
	function record($model = 'Media', $foreignKey = null) {
		$this->set('uuid', $this->Media->_generateUUID());
		$this->set('model', $model);
		$this->set('foreignKey', $foreignKey);

		if(!empty($this->request->data)) {
			if ($this->Media->save($this->request->data)) {
				$this->Session->setFlash('Media saved.');
				#$this->redirect('/media/media/edit/'.$this->Media->id);
				$this->redirect(array('action' => 'my'));
			} else {
				$this->Session->setFlash('Invalid Upload.');
			}
        }

	}

	function images() {
		$this->set('page_title_for_layout', __('Media Images'));
	}
	function files() {
		$this->set('page_title_for_layout', __('Media Files'));
	}

	
	/**
	 * Filebrowser Action
	 * Supports Ajax
	 * 
	 * @param $uid - The user to show the images for
	 * @param $multiple - Allow the user to select more that one Item
	 */
	public function filebrowser($multiple = true, $uid = null) {
		debug($this->request->data);
		if($uid == null && $this->Session->read('Auth.User.id') != 1) {
			$uid = $this->Session->read('Auth.User.id');
		}
		
		$multiple = isset($this->request->data['mulitple']) ? $this->request->data['mulitple'] : true;
		
		$media = $this->Media->find('all', array('conditions' => array('creator_id' => $uid)));
		
		$this->set(compact('media', 'multiple'));
		
		if($this->request->isAjax()) {
			$this->layout = null;
		}
		
	}


	public function canvas($id = null) {
		if ( $id ) {
			$canvas = $this->Media->find('first', array(
				'conditions' => array(
					'Media.id' => $id
				)
			));
			$this->set('media', $media);
		}
	}

}

if (!isset($refuseInit)) {
	class MediaController extends _MediaController{}
}
