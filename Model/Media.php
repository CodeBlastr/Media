<?php
App::uses('MediaAppModel', 'Media.Model');
class AppMedia extends MediaAppModel {

/**
 * An array of file types we accept to the media plugin.
 */
	public $supportedFileExtensions = array('pdf', 'doc', 'docx', 'ods', 'odt');

/**
 * An array of video types we accept to the media plugin.
 */
	public $supportedVideoExtensions = array('mpg', 'mov', 'wmv', 'rm', '3g2', '3gp', '3gp2', '3gpp', '3gpp2', 'avi', 'divx', 'dv', 'dv-avi', 'dvx', 'f4v', 'flv', 'h264', 'hdmov', 'm4v', 'mkv', 'mp4', 'mp4v', 'wav', 'mpe', 'mpeg', 'mpeg4', 'mpg', 'nsv', 'qt', 'swf', 'xvid');

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
		
	public $hasMany = array(
		'MediaAttachment' => array(
			'className' => 'Media.MediaAttachment',
			'foreignKey' => 'media_id'
		)
	);

	public $fileExtension;

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


/**
 * This function was made to replace the takeover of save() that we had, processing uploaded files.
 * If trying to upload & save a Media object, pass it to this function.
 * 
 * @param array $media
 * @return boolean
 */
	public function upload($media = null) {
		if ($media !== null) {
			$this->data = $media;
		}
		if ($this->beforeUpload()) {
			$savedData = $this->save($this->data);
			if ($savedData) {
				return $this->afterUpload($savedData);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

/**
 * 
 * @return boolean
 */
	public function beforeUpload() {
		$this->data['Media']['model'] = !empty($this->data['Media']['model']) ? $this->data['Media']['model'] : 'Media';
		$this->plugin = strtolower(ZuhaInflector::pluginize($this->data['Media']['model']));
		$this->__createDirectories();
		$this->data = $this->_handleRecordings($this->data);
		$this->data = $this->_handleCanvasImages($this->data);
		$this->fileExtension = $this->getFileExtension($this->data['Media']['filename']['name']);

		return $this->processFile();
	}

/**
 * 
 * @param array $data
 * @return array|boolean
 */
	public function afterUpload($data) {
		$mediaType = $this->mediaType($this->fileExtension);
		// automatically generate and save video thumbnails
		if ($mediaType === 'videos') {
			return $this->setVideoThumbnail($data);
		}
	}

/**
 * Given a Media object, this will take a snapshot of the video, save it in /media/images/, and record it's relative URL in Media.thumbnail
 * 
 * @param array $data Media object
 * @param array $options Defaults are: array('thumbnailSize' => '150x150', 'fromTime' => '00:00:5')
 * @return array|boolean Result from Media->save()
 * @throws Exception
 */
	public function setVideoThumbnail($data, $options = array()) {
		
		$defaults['thumbnailSize'] = '150x150';
		$defaults['fromTime'] = '00:00:5';
		$options = Set::merge($options, $defaults);
		
		$uploadFile = $this->getMediaFilePath($data);
		$randomFilename = $this->__uuid().uniqid();
		$thumbnailFilePath = $this->themeDirectory . DS . 'images' . DS . $randomFilename . '.jpg';
		
		if (PHP_OS === 'Darwin') {
			$command = VENDORS . 'ffmpeg/mac64/ffmpeg -i ' . $uploadFile . " -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$options['thumbnailSize']} -ss {$options['fromTime']} ".$thumbnailFilePath;
		} elseif (PHP_OS === 'WINNT') {
			throw new Exception('Windows ffmpeg not setup yet', 1);
			//$command = VENDORS . 'ffmpeg\windows\ffmpeg -i ' . $uploadFile . " -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$options['thumbnailSize']} -ss {$options['fromTime']} ".$thumbnailFilePath;
		} else {
			switch (PHP_INT_SIZE) {
				case 4 :
					throw new Exception('*nix 32bit not setup yet', 1);
				case 8 :
					$command = VENDORS . 'ffmpeg/nix64/ffmpeg -i ' . $uploadFile . " -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$options['thumbnailSize']} -ss {$options['fromTime']} ".$thumbnailFilePath;
					break;
				default :
					throw new Exception('I was unable to detect which ffmpeg binary to use on this system.', 1);
			}
		}
		
		exec($command);
		
		$data['Media']['thumbnail'] = DS . 'media' . DS . 'images' . DS . $randomFilename . '.jpg';
		
		return $this->save($data);
	}

/**
 * Give this a Media array and it will give you the full path of the actual file
 * 
 * @param array Standard $data['Media'] array
 * @return string Full path of media file
 */
	public function getMediaFilePath($data) {
		return $this->themeDirectory . DS . $data['Media']['type'] . DS . $data['Media']['filename'] . '.' . $data['Media']['extension'];
	}

/**
 * 
 * @return boolean
 */
	public function processFile() {
		$this->data['Media']['type'] = $this->mediaType($this->fileExtension);
		if ($this->data['Media']['type']) {
			$this->data = $this->uploadFile($this->data);
			return true;
		}
		return false;
	}

	public function mediaType($ext) {
		if (in_array($ext, $this->supportedFileExtensions)) {
			return 'docs';
			$this->data = $this->uploadFile($this->data);
		} elseif (in_array($ext, $this->supportedImageExtensions)) {
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
/**
 * This code was only ever used for the Zencoder service.
 * It seemed to have been putting arrays into 'filename' and 'ext', 
 * so that we could echo out the different available filetypes for this audio/video file.
 */

		// foreach ($results as $key => $val) {
			// if (isset($val['Media']['filename'])) {
// 
				// // what formats did we receive from the encoder?
				// $outputs = json_decode($val['Media']['filename'], true);
// 
				// // audio files have 1 output currently.. arrays are not the same.. make them so.
				// /** @todo this part is kinda hacky.. **/
				// if ($val['Media']['type'] == 'audio') {
					// $temp['outputs'] = $outputs['outputs'];
					// $outputs = null;
					// $outputs['outputs'][0] = $temp['outputs'];
				// }
// 
				// if ($val['Media']['type'] == 'videos') {
					// $outputArray = $extensionArray = null;
					// if (!empty($outputs)) {
						// foreach ($outputs['outputs'] as $output) {
							// $outputArray[] = 'http://' . $_SERVER['HTTP_HOST'] . '/media/media/stream/' . $val['Media']['filename'] . '/' . $output['label'];
							// $extensionArray[] = $output['label'];
						// }
					// }
					// // set the modified ['filename']
					// $results[$key]['Media']['filename'] = $outputArray;
					// $results[$key]['Media']['ext'] = $extensionArray;
				// }
			// }
		// }
		
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
 * Handles an uploaded file (ie. doc, pdf, etc)
 */
	public function uploadFile($data) {
		$uuid = $this->__uuid().uniqid();
		$newFile =  $this->themeDirectory . DS . $this->data['Media']['type'] . DS . $uuid . '.' . $this->fileExtension;
		if (rename($data['Media']['filename']['tmp_name'], $newFile)) {
			$data['Media']['filename'] = $uuid; // change the filename to just the filename
			$data['Media']['extension'] = $this->fileExtension; // change the extension to just the extension
			return $data;
		} else {
			throw new Exception(__d('media', 'File Upload of ' . $data['Media']['filename']['name'] . ' to ' . $newFile . '  Failed'));
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
				if (rename($serverFile, $localFile)) {
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

	/**
	 * I beleive that this one was used to save images created with the LiterallyCanvas script
	 * @param type $data
	 * @return int
	 */
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
	 *
	 * @param array $data An entire model from the canvasBuildrr {model:collection:{models}}
	 * @return array
	 */
	public function updateCanvasObjects($data, $galleryId = false) {
		$data = json_decode($data, true);

		$this->id = ($data['id']) ? $data['id'] : null;

		// save the screenshot file.
		foreach ($data['collection'] as &$canvasObject) {
			if ($canvasObject['type'] == 'screenshot') {
				$savedImage = $this->_saveCanvasImageObject($canvasObject, $galleryId);
				$canvasObject['id'] = $savedImage['Media']['id'];
				$canvasObject['content'] = $this->mediaUrl . $savedImage['Media']['type'] . '/' .  $savedImage['Media']['filename'] . '.' . $savedImage['Media']['extension'];
			}
		}

		// save all data to our screenshot/parent row
		$addedObjects = $this->saveField('data', json_encode($data), array('callbacks' => false));

		if ($addedObjects) {
			return array(
					'statusCode' => '200',
					'body' => json_encode($addedObjects)
			);
		} else {
			return array('statusCode' => '403');
		}
	}


	/**
	 * saves image file from image object to the file server
	 * 
	 * @todo Would like to delete the old "screenshot" when a new one is created
	 * 
	 * @param array $data
	 * @return array|boolean
	 */
	private function _saveCanvasImageObject($data, $galleryId = false) {

		// make sure that this is (probably) safe to pass to fopen()
		if (strpos($data['content'], 'data:') !== 0) {
			return false;
		}

		$added = false;

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
		}

		// set temp filename
		$uuid = $this->__uuid().uniqid();

		// write image to disk
		$imageString = str_replace('data:'.$metadata['mediatype'].';base64,', '', $data['content']);
		$decodedImageString = base64_decode($imageString);
		$fopen = fopen(sys_get_temp_dir() . DS . $uuid, 'wb');
		$written = fwrite($fopen, $decodedImageString);
		fclose($fopen);

		if ($written) {
			// save record to database server
			if (!$this->id) {
				$this->create();
			}
			if ($galleryId === false) {
				$this->Behaviors->disable('MediaAttachment');
			}
			$this->data = array(
				'Media' => array(
					'filename' => array(
						'name' => $uuid . '.' . $extension,
						'tmp_name' => sys_get_temp_dir() . DS . $uuid
					)
				));
			$added = $this->upload();
			
			/** this was commented out since the canvasBuildrr is currently running off of 4 pre-attached media **/
			// if ($added) {
				// $mediaAttachment = array(
					// 'media_id' => $this->id,
					// 'model' => 'MediaGallery',
					// 'foreign_key' => $galleryId
				// );
				// $this->MediaAttachment->save($mediaAttachment);
			// }
		}

		return $added;

	}

}

if (!isset($refuseInit)) {
	class Media extends AppMedia {}
}
