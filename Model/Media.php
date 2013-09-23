<?php
class Media extends MediaAppModel {

/**
 * An array of file types we accept to the media plugin.
 */
	public $supportedFileExtensions = array('pdf', 'doc', 'docx', 'ods', 'odt');

/**
 * An array of video types we accept to the media plugin.
 */
	public $supportedVideoExtensions = array('mpg', 'mov', 'wmv', 'rm', '3g2', '3gp', '3gp2', '3gpp', '3gpp2', 'avi', 'divx', 'dv', 'dv-avi', 'dvx', 'f4v', 'flv', 'h264', 'hdmov', 'm4v', 'mkv', 'mp4', 'mp4v', 'mpe', 'mpeg', 'mpeg4', 'mpg', 'nsv', 'qt', 'swf', 'xvid');


/**
 * An array of audio types we accept to the media plugin.
 */
	public $supportedAudioExtensions = array('aif', 'mid', 'midi', 'mka', 'mp1', 'mp2', 'mp3', 'mpa', 'wav', 'aac', 'flac', 'ogg', 'ra', 'raw', 'wma');

	public $supportedImageExtensions = array('jpg', 'png', 'gif', 'bmp', 'jpeg');
	
	public $name = 'Media';

	public $belongsTo = array(
	    'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id'
			)
		);

	public $screenshotId;

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->themeDirectory = ROOT.DS.SITE_DIR.DS.'Locale'.DS.'View'.DS.WEBROOT_DIR.DS.'media';
		$this->uploadFileDirectory = 'docs';
		$this->uploadVideoDirectory =  'videos';
		$this->uploadAudioDirectory = 'audio';
		$this->uploadImageDirectory = 'images';
		$this->mediaUrl = '/theme/default/media/';
		$this->order = array("{$this->alias}.created");
	}


	public function beforeSave($options) {
		parent::beforeSave($options);
		$this->data['Media']['model'] = !empty($this->data['Media']['model']) ? $this->data['Media']['model'] : 'Media';
		$this->plugin = strtolower(ZuhaInflector::pluginize($this->data['Media']['model']));
		$this->_createDirectories();
		$this->data = $this->_handleRecordings($this->data);
		$this->data = $this->_handleCanvasImages($this->data);
		$this->fileExtension = $this->getFileExtension($this->data['Media']['filename']['name']);
		return $this->processFile();
	}//beforeSave()

	
	public function processFile() {
		$this->data['Media']['type'] = $this->mediaType($this->fileExtension);
		if($this->data['Media']['type']) {
			$this->data = $this->uploadFile($this->data);
			return true;
		}
		return false;
	}
	
	public function mediaType($ext) {
		if(in_array($ext, $this->supportedFileExtensions)) {
			return 'docs';
			$this->data = $this->uploadFile($this->data);
		} elseif(in_array($ext, $this->supportedImageExtensions)) {
			return 'images';
		} elseif (in_array($ext, $this->supportedVideoExtensions)) {
			return 'videos';
		} elseif (in_array($ext, $this->supportedAudioExtensions)) {
			return 'audio';
		} else {
			// an unsupported file type
			return false;
		}
		
	}

    /**
     *
     * @param type $results
     * @param type $primary
     * @return array 
     */
    public function afterFind($results, $primary = false) {

		foreach($results as $key => $val) {
			if(isset($val['Media']['filename'])) {

				# what formats did we receive from the encoder?
				$outputs = json_decode($val['Media']['filename'], true);
				
				# audio files have 1 output currently.. arrays are not the same.. make them so.
				/** @todo this part is kinda hacky.. **/
				if($val['Media']['type'] == 'audio') {
					$temp['outputs'] = $outputs['outputs'];
					$outputs = null;
					$outputs['outputs'][0] = $temp['outputs'];
				}
			
				if($val['Media']['type'] == 'videos') {
					$outputArray = $extensionArray = null;
					if (!empty($outputs)) {
						foreach ($outputs['outputs'] as $output) {
							$outputArray[] = 'http://' . $_SERVER['HTTP_HOST'] . '/media/media/stream/' . $val['Media']['filename'] . '/' . $output['label'];
							$extensionArray[] = $output['label'];
						}
					}
					# set the modified ['filename']
					$results[$key]['Media']['filename'] = $outputArray;
					$results[$key]['Media']['ext'] = $extensionArray;
				}
			}
		}
		return $results;
    }


/**
 * This is a valid callback that comes with the Rateable plugin
 * It is being kept here for future reference/use
 * @param array $data
 */
	public function afterRate($data) {
		#debug($data);
	}



/**
 * Get the extension of a given file path
 *
 * @param {string} 		A file name/path string
 */
    function getFileExtension($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
		
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
		
        # check if there is any extension
        if(count($pattern) == 1) {
            return FALSE;
        }

        if(count($pattern) > 1) {
            $filenamepart = $pattern[count($pattern)-1][0];
            preg_match('/[^?]*/', $filenamepart, $matches);
            return strtolower($matches[0]);
        }
    }


/**
 * Handles an uploaded file (ie. doc, pdf, etc)
 */
	public function uploadFile($data) {
		$uuid = $this->__uuid().uniqid();
		$newFile =  $this->themeDirectory . DS . $this->data['Media']['type'] . DS . $uuid . '.' . $this->fileExtension;
		if (rename($data['Media']['filename']['tmp_name'], $newFile)) :
			$data['Media']['filename'] = $uuid; // change the filename to just the filename
			$data['Media']['extension'] = $this->fileExtension; // change the extension to just the extension
			return $data;
		else :
			throw new Exception(__d('media', 'File Upload of ' . $data['Media']['filename']['name'] . ' to ' . $newFile . '  Failed'));
		endif;
	}

	
	/**
	 * This function needs to do the following things:
	 *  - save the metadata of each image
	 *  - save the entire models of TextObjects
	 *  - (maybe) either return the entire collection so we can .reset() or just the collection's id
	 *  
	 * @param array $data
	 * @return array
	 */
	public function addCanvasCollection($data) {

		// save all image objects as rows in `media`
		$addedObjects = $this->addCanvasObjects($data);
		
		// save a "parent" row that has all data
		$this->id = $this->screenshotId;
		$this->saveField('data', json_encode($addedObjects), array('callbacks' => false));
		
		if ($addedObjects) {
			return array(
					'statusCode' => '200',
					'body' => json_encode($addedObjects)
					//'body' => array('id' => $this->screenshotId)
			);
		} else {
			return array('statusCode' => '403');
		}
	}
	

	/**
	 * 
	 * @param array $data
	 * @return string
	 */
	public function addCanvasObjects($data) {
		$objects = false;
		foreach ($data as $canvasObject) {
			if ($canvasObject['type'] == 'image' || $canvasObject['type'] == 'screenshot') {
				$savedImage = $this->_saveCanvasImageObject($canvasObject);
				if ($canvasObject['type'] == 'screenshot') {
					$this->screenshotId = $this->id;
				}
				$canvasObject['id'] = $savedImage['Media']['id'];
				$canvasObject['content'] = '/theme/Default/media/' . $savedImage['Media']['type'] . '/' .  $savedImage['Media']['filename'] . '.' . $savedImage['Media']['extension'];
				$objects[] = $canvasObject;
			}
			$objects[] = $canvasObject;
		}

		return $objects;

	}

	/**
	 * saves image file from image object to the file server
	 * 
	 * @param array $data
	 * @return array|boolean
	 */
	private function _saveCanvasImageObject($data) {
		$added = false;
		// make sure that this is (probably) safe to pass to fopen()
		if (strpos($data['content'], 'data:') !== 0) {
			return false;
		}
		
		$image = fopen($data['content'], 'r');
		$metadata = stream_get_meta_data($image);
		
		switch ($metadata['mediatype']) {
			case ('image/png'):
				$extension = 'png';
				break;
			case ('image/jpeg'):
				$extension = 'jpg';
				break;
			case ('image/gif'):
				$extension = 'gif';
				break;
			case ('image/bmp'):
				$extension = 'bmp';
				break;
			default:
				return false;
				break;
		}
		
		// set temp filename
		$uuid = $this->__uuid().uniqid();
		
		// write image to disk
		$imageString = str_replace('data:'.$metadata['mediatype'].';base64,', '', $data['content']);
		$imageString = base64_decode($imageString);
		$fopen = fopen(sys_get_temp_dir() . $uuid, 'wb');
		$written = fwrite($fopen, $imageString);
		fclose($fopen);
		
		if ($written) {
			// clean up
// 			unset($data['cid']);
// 			unset($data['content']);
			
			// save record to database server
			$this->create();
			$added = $this->save(array(
					'Media' => array(
						'filename' => array(
								'name' => $uuid . '.' . $extension,
								'tmp_name' => sys_get_temp_dir() . $uuid
								),
// 						'data' => json_encode($data)
					)
			));
		}

		return $added;
		
	}
	
	public function updateCanvasObjects($data) {
		$added = false;
		foreach ($data as $canvasObject) {
			if (!(isset($canvasObject['id']))) {
				if ($canvasObject['type'] == 'image') {
					$added = $this->_saveCanvasImageObject($canvasObject);
				} elseif ($canvasObject['type'] == 'text') {
					$added = $this->_saveCanvasTextObject($canvasObject);
				}
			}
		}
		
		if ($added) {
			return array('statusCode' => '200');
		} else {
			return array('statusCode' => '403');
		}
	}
	
	public function deleteCanvasObject($data) {
		$added = false;
		if ($added) {
			return array('statusCode' => '200');
		} else {
			return array('statusCode' => '403');
		}
	}
	

/**
 * Recordings were saved to the recording server, and now we need to move them to the local server.
 *
 */
	private function _handleRecordings($data) {
		if (!empty($data['Media']['type']) && $data['Media']['type'] == 'record') {
			$fileName = $data['Media']['uuid'];
			$serverFile = '/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/'.$fileName.'.flv';
			$localFile = $this->themeDirectory . $this->plugin . DS . 'videos' . DS . $fileName.'.flv';
			#$url = '/theme/default/media/'.$this->pluginFolder.'/videos/'.$fileName.'.flv';

			if (file_exists($serverFile)) {
				if(rename($serverFile, $localFile)) {
					#echo $url = '/theme/default/media/'.$this->pluginFolder.'/videos/'.$fileName.'.flv';
				} else {
					return false;
				}
			} else {
				return false;
			}

			$data['Media']['filename']['name'] = $fileName.'.flv';
			$data['Media']['filename']['type'] = 'video/x-flv';
			$data['Media']['filename']['tmp_name'] = $localFile;
			$data['Media']['filename']['error'] = 0;
			$data['Media']['filename']['size'] = 99999; //
		}
		return $data;
	}

	private function _handleCanvasImages($data) {
		if ( !empty($data['Media']['canvasImageData']) ) {
			
			$canvasImageData = str_replace('data:image/png;base64,', '', $data['Media']['canvasImageData']);
			$decodedImage = base64_decode($canvasImageData);
			
			$filename = preg_replace("/[^\w\s\d\-_~,;:\[\]\(\]]|[\.]{2,}/", '', $data['Media']['title'].'_'.uniqid());
			$saveName = $this->themeDirectory . $this->plugin . DS . 'images' . DS . $filename.'.png';
			
			$fopen = fopen($saveName, 'wb');
			fwrite($fopen, $decodedImage);
			fclose($fopen);
			
			$data['Media']['filename']['name'] = $filename.'.png';
			$data['Media']['filename']['type'] = 'image/png';
			$data['Media']['filename']['tmp_name'] = $saveName;
			$data['Media']['filename']['error'] = 0;
		}
		return $data;
	}

/**
 * Create the directories for this plugin if they aren't there already.
 */
	private function _createDirectories() {
		if (!file_exists($this->themeDirectory)) {
			if (
				mkdir($this->themeDirectory, 0775, true) &&
				mkdir($this->themeDirectory . DS . 'videos', 0775, true) &&
				mkdir($this->themeDirectory . DS . 'docs', 0775, true) &&
				mkdir($this->themeDirectory . DS . 'audio', 0775, true) &&
				mkdir($this->themeDirectory . DS . 'images', 0775, true) &&
				mkdir($this->themeDirectory . DS . 'images' . DS . 'thumbs', 0775, true)
				) {
				return true;
			} else {
				return false;
			}
		} else {return true;
		}
	}


}
