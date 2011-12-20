<?php
class Media extends MediaAppModel {

/**
 * An array of file types we accept to the media plugin.
 */
	public $supportedFileExtensions = array('pdf', 'doc', 'docx', 'ods');

/**
 * An array of video types we accept to the media plugin.
 * @todo These appear in the encodable behavior too.  They should only appear here.
 */
	public $supportedVideoExtensions = array('mpg', 'mov', 'wmv', 'rm', '3g2', '3gp', '3gp2', '3gpp', '3gpp2', 'avi', 'divx', 'dv', 'dv-avi', 'dvx', 'f4v', 'flv', 'h264', 'hdmov', 'm4v', 'mkv', 'mp4', 'mp4v', 'mpe', 'mpeg', 'mpeg4', 'mpg', 'nsv', 'qt', 'swf', 'xvid');


/**
 * An array of audio types we accept to the media plugin.
 * @todo These appear in the encodable behavior too.  They should only appear here.
 */
	public $supportedAudioExtensions = array('aif', 'mid', 'midi', 'mka', 'mp1', 'mp2', 'mp3', 'mpa', 'wav', 'aac', 'flac', 'ogg', 'ra', 'raw', 'wma');

	public $name = 'Media';

	public $actsAs = array(
		'Ratings.Ratable' => array(
			'saveToField' => true,
			//'field' => 'rating'
			//'modelClass' => 'Media.Media'
			),
		'Encoders.Encodable',
		);

	public $belongsTo = array(
	    'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id'
			)
		);


	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->themeDirectory = ROOT.DS.SITE_DIR.DS.'View'.DS.'Themed'.DS.'Default'.DS.WEBROOT_DIR.DS.'media'.DS;
		$this->uploadFileDirectory = 'docs';
		$this->uploadVideoDirectory =  'videos';
		$this->uploadAudioDirectory = 'audio';
		$this->uploadImageDirectory = 'images';
		$this->order = array("{$this->alias}.created");
	}



	public function beforeSave($options) {
		$this->data['Media']['model'] = !empty($this->data['Media']['model']) ? $this->data['Media']['model'] : 'Media';
		$this->plugin = strtolower(pluginize($this->data['Media']['model']));
		$this->_createDirectories();
		$this->data = $this->_handleRecordings($this->data);
		$this->fileExtension = $this->getFileExtension($this->data['Media']['filename']['name']);
		
		
		if(in_array($this->fileExtension, $this->supportedFileExtensions)) { 
			$this->data['Media']['type'] = 'docs';
			$this->data = $this->uploadFile($data);
		} elseif (in_array($this->fileExtension, $this->supportedVideoExtensions)) {
			 $this->data['Media']['type'] = 'videos';
			 $this->data = $this->encode($this);
		} elseif (in_array($this->fileExtension, $this->supportedAudioExtensions)) {
			 $this->data['Media']['type'] = 'audio';
			 $this->data = $this->encode($this);
		} else {
			# an unsupported file type
			return false;
		}
		return true;
	}
  

	public function afterRate($data) {
		#debug($data);
	}



/**
 * Get the extension of a given file path
 *
 * @param {string} 		A file name/path string
 * @todo				This appears in both the encodable behavior and here.  Both could and should make use of this function, there should be a single place that both could access it (AppModel maybe), but where?
 */
    function getFileExtension($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];

        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);

        # check if there is any extension
        if(count($pattern) == 1)
        {
            return FALSE;
        }

        if(count($pattern) > 1)
        {
            $filenamepart = $pattern[count($pattern)-1][0];
            preg_match('/[^?]*/', $filenamepart, $matches);
            return strtolower($matches[0]);
        }
    }


/**
 * Handles an uloaded file (ie. doc, pdf, etc)
 */
	public function uploadFile($data) {
		$uuid = $this->_generateUUID();
		$newFile =  $this->uploadFileDirectory . DS . $uuid .'.'. $this->fileExtension;
		if (rename($data['Media']['filename']['tmp_name'], $newFile)) :
			$data['Media']['id'] = $uuid; // change the filename to just the filename
			$data['Media']['filename'] = $uuid; // change the filename to just the filename
			$data['Media']['extension'] = $this->fileExtension; // change the extension to just the extension
			$data['Media']['type'] = 'docs';
			return $data;
		else :
			throw new Exception(__d('media', 'File Upload of ' . $data['Media']['filename']['name'] . ' to ' . $newFile . '  Failed'));
		endif;
	}
	

/**
 * Recordings were saved to the recording server, and now we need to move them to the local server. 
 * 
 */
	private function _handleRecordings($data) {
		if ($data['Media']['type'] == 'record') {
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
	
	
/**
 * Create the directories for this plugin if they aren't there already.
 */
	private function _createDirectories() {
		if (!file_exists($this->themeDirectory . $this->plugin)) {
			if (
				mkdir($this->themeDirectory . $this->plugin) && 
				mkdir($this->themeDirectory . $this->plugin . DS . 'videos') && 
				mkdir($this->themeDirectory . $this->plugin . DS . 'docs') &&
				mkdir($this->themeDirectory . $this->plugin . DS . 'audio') &&
				mkdir($this->themeDirectory . $this->plugin . DS . 'images') &&
				mkdir($this->themeDirectory . $this->plugin . DS . 'images' . DS . 'thumbs')
				) {
				return true;
			} else {
				return false;
			}
		} else {return true;
		}
	}


}//class{}