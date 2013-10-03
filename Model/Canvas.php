<?php
/**
 * @todo Move all the canvas stuff that's in the Media model to here.
 * @author Joel Byrnes
 *
 */
App::uses('MediaAppModel', 'Media.Model');
class Canvas extends MediaAppModel {
	public $name = 'Canvas';
	
	public $useTable = 'media';
	
	public $belongsTo = array(
			'User' => array(
					'className' => 'Users.User',
					'foreignKey' => 'user_id'
			)
	);
	
	/**
	 * Convienence variable so we don't clutter our object arrays
	 * @var string
	 */
	public $screenshotId;
	
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
			);
		} else {
			return array('statusCode' => '403');
		}
	}
	
	
	/**
	 *
	 * @param array $data
	 * @return array|boolean
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
			}
			$objects[] = $canvasObject;
		}
	
		return $objects;
	}
	
	
	public function updateCanvasObjects($data) {
		$added = false;
		foreach ($data as $canvasObject) {
			if (!(isset($canvasObject['id']))) {
				if ($canvasObject['type'] == 'ImageObject') {
					$added = $this->_saveCanvasImageObject($canvasObject);
				} elseif ($canvasObject['type'] == 'TextObject') {
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
			if ( $this->beforeSave() ) {
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
		}
	
		return $added;
	
	}
	
	
	public function deleteCanvasObject($data) {
		$added = false;
		if ($added) {
			return array('statusCode' => '200');
		} else {
			return array('statusCode' => '403');
		}
	}
	
}