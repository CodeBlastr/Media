<?php

/**
 * To Extend use code
 * $refuseInit = true; require_once(ROOT.DS.'app'.DS.'Plugin'.DS.'Media'.DS.'Controller'.DS.'MediaController.php');
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
		if (!empty($this->request->data)) {
			if(isset($this->request->data['MediaAttachment'])) {
				$this->loadModel('Media.MediaAttachment');
			}
			
            $this->request->data['User']['id'] = $this->Auth->user('id');
			$mediaarray = array();
			foreach($this->request->data['Media']['files'] as $file) {
				$media['Media'] = array(
					'user_id' => $this->Auth->user('id'),
					'filename' => $file
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
					$mediaarray[] = $media;
				}
			}
			if(!empty($mediaarray)) {
				$this->Session->setFlash('Media saved.');
				if ($this->request->isAjax()) {
					$this->set('media', $mediaarray);
					$this->layout = false;
					$this->view = 'ajax-upload';
				} else {
					$this->redirect(array('action' => 'my'));
				}
			}else {
				$this->Session->setFlash('Upload failed, check directory permissions');
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
            debug($this->Media->save($this->request->data));
            if ($this->Media->save($this->request->data, array('callbacks' => false))) {
                $this->Session->setFlash('Your media has been updated.');
                $this->redirect($this->referer());
            }
		}
	}//edit()


	public function delete($id) {
		try{
			if(isset($id) && $this->Media->exists($id)) {
				//Get the media info so we can delete the file
				$media = $this->Media->findById($id);
				$type = $this->Media->mediaType($media['Media']['extension']);
				$this->loadModel('Media.MediaAttachment');
				//Delete the media don't cascade because we need better control over what get delete
				if(!$this->Media->delete($id, false)) {
					throw new Exception('Could not delete Media Record');
				}
				if(!$this->MediaAttachment->deleteAll(array('media_id' => $id))) {
					throw new Exception('Could not delete attachment records');
				}
				if(!unlink($this->Media->themeDirectory.DS.$type.DS.$media['Media']['filename'].'.'.$media['Media']['extension'])) {
					throw new Exception('Could not delete file, please check permissions');
				}
				$this->Session->setFlash(__('Deleted %n', !empty($media['Media']['title']) ? $media['Media']['title'] : $id ));
			}else {
				throw new MethodNotAllowedException('Action not allowed');
			}
		}catch(Exception $e) {
			$this->Session->setFlash($e->getMessage());
		}
		
		$this->redirect($this->referer());
	}
	

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
	    $options = array(
          'conditions' => array(
		    'Media.type' => strtolower($mediaType),
		    'Media.is_visible' => '1'
          ),
          'order' => array('Media.'.$field => $sortOrder),
          'limit' => $numberOfResults
	    );

	    return $this->Media->find('all', $options);

	}


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
		if($uid == null && $this->Session->read('Auth.User.id') != 1) {
			$uid = $this->userId;
		}
		
		$galleryid = isset($this->request->query['galleryid']) ? $this->request->query['galleryid'] : array();
		if(!empty($galleryid)) {
			$this->set('galleryid', $galleryid);
		}
		$this->loadModel('Media.MediaGallery');
		$this->set('galleries', $this->MediaGallery->find('list'));
		
		if(!empty($galleryid)) {
			$media = $this->MediaGallery->find('first', array('contain' => 'Media', 'conditions' => array('id' => $galleryid)));
			$media = $media['Media'];
		}else {
			$multiple = isset($this->request->data['mulitple']) ? $this->request->data['mulitple'] : true;
			if($uid == null) {
				$media = $this->Media->find('all');
			}else{
				$media = $this->Media->find('all', array('conditions' => array('creator_id' => $uid)));
			}
		}
		
		$this->set(compact('media', 'multiple'));
		
		if($this->request->isAjax()) {
			$this->layout = null;
		}
		
	}


	public function canvas($id = null) {
// 		App::uses('Canvas', 'Media.Model');
// 		$this->Canvas = new Canvas;
		switch ($this->request->method()) {
			case ('POST'):
				$response = $this->Media->addCanvasObjects($this->request->data);
				$this->__returnJsonResponse($response);
				break;
			case ('PUT'):
				$response = $this->Media->updateCanvasObjects($this->request->data);
				$this->__returnJsonResponse($response);
				break;
			case ('DELETE'):
				$response = $this->Media->deleteCanvasObjects($this->request->data);
				$this->__returnJsonResponse($response);
				break;
			case ('GET'):
			default:
				if ($id) {
					$this->request->data = $this->Media->find('first', array(
							'conditions' => array(
									'Media.id' => $id
							)
					));
					$this->request->data['Media']['data'] = json_decode($this->request->data['Media']['data']);
					$this->request->data['Media']['data']->id = $id;
					$this->request->data['Media']['data'] = json_encode($this->request->data['Media']['data']);

// 					if ($this->request->isAjax()) {
// 							$this->__returnJsonResponse(array(
// 								'statusCode' => '200',
// 								'body' => $this->request->data
// 							));
// 					}
				}
				break;
		}
	}
		
	
	/**
	 * Lazy Loader Function derives from imgsrc link.
	 * Breaks up the link and uses the filename.
	 * 
	 * @param $imglink  The url of the image
	 * @return string rendered filename
	 */
	
	public function lazyLoad($id) {
		if(!$this->request->is('json')) {
			throw new Exception('Method Not allowed');
		}
		//Using query params to set options array
		$params = array();
		if(isset($this->request->query)) {
			foreach($this->request->query as $k => $p) {
				switch ($k) {
					case 'imgwidth':
						$param['newWidth'] = $p;
						break;
					
					case 'imgheight':
						$param['newHeight'] = $p;
						break;
						
					case 'quality':
						$param['quality'] = $p;
						break;
						
					case 'method':
						if( $p == 'resize' |  $p == 'resizeCrop' | $p == 'crop') {
							$param['cType'] = $p;
						}
						break;
					
					default:
						
						break;
				}
			}
		}
		
		$filename = $this->_resizeImage($id, $params);
		debug($filename);
		
	}
	
	/**
	 * Image Resize Handling.
	 * @TODO This probably should be a componenet
	 * (might need to be a "lib" if you want to use it globally) ^JB 
	 */
	 
	 public $resizeDefaults = array(
	 	'cType' => 'resize',
		'imgFolder' => false,
		'newName' => false,
		'newWidth' => 100,
		'newHeight' => 100,
		'quality' => 75,
		'bgcolor' => false
	);
	
	/**
	 * Resize Image function - uses GD Library
	 * @param $id = Media ID
	 * @param $options = array(
	 * 				'cType' => { resize | resizeCrop | crop },
	 * 				'location' => { Location of original media },
	 * 				'newName' => { Defaults to False },
	 * 				'newWidth' => { New Image Width },
	 * 				'newHeight' => { New Image Height },
	 * 				'quality' => { new image quality 0-100 },
	 * 				'bgcolor => { New Backround Color }
	 * 			)
	 * 	
	 * @return bool
	 */
	 
    protected function _resizeImage($id = null, $params = array()) {
    	if(empty($id)) {
    		return false;
    	}else {
    		$media = $this->Media->read(array('filename', 'extension', $id));
    	}
		
    	$params = array_merge($this->resizeDefaults, $params);
        
		if (file_exists($img)) {
	        list($oldWidth, $oldHeight, $type) = getimagesize($img);
	        $ext = $this->image_type_to_extension($type);

			# check for and create cacheFolder
			$cacheFolder = 'cache';
			$cachePath = $imgFolder . $cacheFolder;
			if (is_dir($cachePath)) {
				# do nothing the cache dir exists
			} else {
				if (mkdir($cachePath)) {
					# do nothing the cache dir exists
				} else {
					debug('Could not make images ' . $cachePath . ', and it doesn\'t exist.');
					break;
				}
			}

	        //check to make sure that the file is writeable, if so, create destination image (temp image)
	        if (is_writeable($cachePath)) {
	            if($newName){
	                $dest = $cachePath .DS . $newName. '.' . $id;
	            } else {
	                $dest = $cachePath . DS . 'tmp_' . $id;
	            }
	        } else {
	            //if not let developer know
	            $imgFolder = substr($imgFolder, 0, strlen($imgFolder) -1);
	            $imgFolder = substr($imgFolder, strrpos($imgFolder, '\\') + 1, 20);
	            debug("You must allow proper permissions for image processing. And the folder has to be writable.");
	            debug("Run \"chmod 775 on '$imgFolder' folder\"");
	            exit();
	        }

	        //check to make sure that something is requested, otherwise there is nothing to resize.
	        //although, could create option for quality only
	        if ($newWidth || $newHeight) {
	            /*
	             * check to make sure temp file doesn't exist from a mistake or system hang up.
	             * If so delete.
	             */
	            if(file_exists($dest)) {
					$size = @getimagesize($dest);
					return array(
						'path' => $cacheFolder . '/' . $newName. '.' . $id,
						'width' => $size[0],
						'height' => $size[1],
						);
	                #unlink($dest);
	            } else {
	                switch ($cType){
	                    default:
	                    case 'resize':
	                        # Maintains the aspect ration of the image and makes sure that it fits
	                        # within the maxW(newWidth) and maxH(newHeight) (thus some side will be smaller)
	                        $widthScale = 2;
	                        $heightScale = 2;

	                        if($newWidth) $widthScale = $newWidth / $oldWidth;
	                        if($newHeight) $heightScale = $newHeight / $oldHeight;
	                        //debug("W: $widthScale  H: $heightScale<br>");
	                        if($widthScale < $heightScale) {
	                            $maxWidth = $newWidth;
	                            $maxHeight = false;
	                        } elseif ($widthScale > $heightScale ) {
	                            $maxHeight = $newHeight;
    	                        $maxWidth = false;
	                        } else {
	                            $maxHeight = $newHeight;
	                            $maxWidth = $newWidth;
	                        }

	                        if($maxWidth > $maxHeight){
	                            $applyWidth = $maxWidth;
	                            $applyHeight = ($oldHeight*$applyWidth)/$oldWidth;
	                        } elseif ($maxHeight > $maxWidth) {
	                            $applyHeight = $maxHeight;
	                            $applyWidth = ($applyHeight*$oldWidth)/$oldHeight;
	                        } else {
	                            $applyWidth = $maxWidth;
	                            $applyHeight = $maxHeight;
	                        }
	                        #debug("oW: $oldWidth oH: $oldHeight mW: $maxWidth mH: $maxHeight<br>");
	                       	#debug("aW: $applyWidth aH: $applyHeight<br>");
	                        $startX = 0;
	                        $startY = 0;
	                        #exit();
	                        break;
	                    case 'resizeCrop':
	                        // -- resize to max, then crop to center
	                        $ratioX = $newWidth / $oldWidth;
	                        $ratioY = $newHeight / $oldHeight;

	                        if ($ratioX < $ratioY) {
	                            $startX = round(($oldWidth - ($newWidth / $ratioY))/2);
	                            $startY = 0;
	                            $oldWidth = round($newWidth / $ratioY);
	                            $oldHeight = $oldHeight;
	                        } else {
	                            $startX = 0;
	                            $startY = round(($oldHeight - ($newHeight / $ratioX))/2);
	                            $oldWidth = $oldWidth;
	                            $oldHeight = round($newHeight / $ratioX);
	                        }
	                        $applyWidth = $newWidth;
	                        $applyHeight = $newHeight;
	                        break;
	                    case 'crop':
	                        // -- a straight centered crop
	                        $startY = ($oldHeight - $newHeight)/2;
	                        $startX = ($oldWidth - $newWidth)/2;
	                        $oldHeight = $newHeight;
	                        $applyHeight = $newHeight;
	                        $oldWidth = $newWidth;
	                        $applyWidth = $newWidth;
	                        break;
	                }

	                switch($ext) {
	                    case 'gif' :
	                        $oldImage = imagecreatefromgif($img);
	                        break;
	                    case 'png' :
	                        $oldImage = imagecreatefrompng($img);
	                        break;
	                    case 'jpg' :
	                    case 'jpeg' :
	                        $oldImage = imagecreatefromjpeg($img);
	                        break;
	                    default :
	                        //image type is not a possible option
	                        return false;
	                        break;
	                }

	                //create new image
	                $newImage = imagecreatetruecolor($applyWidth, $applyHeight);

	                if($bgcolor) {
	                //set up background color for new image
	                    sscanf($bgcolor, "%2x%2x%2x", $red, $green, $blue);
	                    $newColor = ImageColorAllocate($newImage, $red, $green, $blue);
	                    imagefill($newImage,0,0,$newColor);
					};
	                
                    // preserve transparency
                    if($ext == 'gif' || $ext == 'png'){
                        imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                    }

	                //put old image on top of new image
	                imagecopyresampled($newImage, $oldImage, 0,0 , $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
    	                switch($ext) {
	                        case 'gif' :
	                            imagegif($newImage, $dest, $quality);
	                            break;
    	                    case 'png' :
	                            imagepng($newImage, $dest, $quality);
	                            break;
	                        case 'jpg' :
	                        case 'jpeg' :
	                            imagejpeg($newImage, $dest, $quality);
	                            break;
	                        default :
	                            return false;
	                            break;
	                    }

         	       imagedestroy($newImage);
	                imagedestroy($oldImage);

	                if(!$newName){
	                    unlink($img);
	                    rename($dest, $img);
	                }

					$size = @getimagesize($cacheFolder . '/' . $newName. '.' . $id);
					return array(
						'path' => $cacheFolder . '/' . $newName. '.' . $id,
						'width' => $applyWidth,
						'height' => $applyHeight,
						);
	            }

	        } else {
	            return false;
	        }
		} else {
			return false; // end the check for if the file to convert even exists
		}
    }

	function image_type_to_extension($imagetype) {
		if(empty($imagetype)) return false;
        switch($imagetype) {
            case IMAGETYPE_GIF    : return 'gif';
            case IMAGETYPE_JPEG    : return 'jpg';
            case IMAGETYPE_PNG    : return 'png';
            case IMAGETYPE_SWF    : return 'swf';
            case IMAGETYPE_PSD    : return 'psd';
            case IMAGETYPE_BMP    : return 'bmp';
            case IMAGETYPE_TIFF_II : return 'tiff';
            case IMAGETYPE_TIFF_MM : return 'tiff';
            case IMAGETYPE_JPC    : return 'jpc';
            case IMAGETYPE_JP2    : return 'jp2';
            case IMAGETYPE_JPX    : return 'jpf';
            case IMAGETYPE_JB2    : return 'jb2';
            case IMAGETYPE_SWC    : return 'swc';
            case IMAGETYPE_IFF    : return 'aiff';
            case IMAGETYPE_WBMP    : return 'wbmp';
            case IMAGETYPE_XBM    : return 'xbm';
            default                : return false;
        }
    }

}

if (!isset($refuseInit)) {
	class MediaController extends _MediaController{}
}
