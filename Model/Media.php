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
			)
		);

	public $belongsTo = array(
	    'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id'
			)
		);


	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$themeDirectory = ROOT.DS.SITE_DIR.DS.'View'.DS.'Themed'.DS.'Default'.DS.WEBROOT_DIR . DS . 'media' . DS;
		$this->uploadFileDirectory = $themeDirectory . 'uploads' . DS . 'files';
		$this->uploadVideoDirectory =  $themeDirectory . 'uploads';
		$this->uploadAudioDirectory = $themeDirectory . 'uploads';
		$this->order = array("{$this->alias}.created");
	}



	public function beforeSave() {
		debug($this->data);
		break;
		if ($this->data['Media.type'] == 'record') {
            $this->data['Media']['user_id'] = CakeSession::read('Auth.User.id');
			$fileName = $this->data['Media']['uuid'];
			$url = '/theme/default/media/recordings/'.$fileName.'.flv';
			if (file_exists('/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/'.$fileName.'.flv')) {
				if(rename('/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/'.$fileName.'.flv', ROOT.DS.SITE_DIR.DS.'View'.DS.'Themed'.DS.'Default'.DS.WEBROOT_DIR . DS . 'media' . DS . 'uploads' . DS . $fileName.'.flv')) {
					echo $url = '/theme/default/media/uploads/'.$fileName.'.flv';
				} else {
					echo 'File could not be moved';
					return false;
				}
			} else {
				echo 'File does not exist.';
				return false;
			}

			#echo '<a href="http://'.$_SERVER['HTTP_HOST'].$url.'">right click this one</a>';
			$this->data['Media']['filename']['name'] = $fileName.'.flv';
			$this->data['Media']['filename']['type'] = 'video/x-flv';
			$this->data['Media']['filename']['tmp_name'] = '/home/razorit/source/red5-read-only/dist/webapps/oflaDemo/streams/'.$fileName.'.flv';
			$this->data['Media']['filename']['error'] = 0;
			$this->data['Media']['filename']['size'] = 99999;
		}
		
		
		
		
		
		if (!empty($this->data['Media']['filename']['size'])) :
			$this->fileExtension = $this->getFileExtension($this->data['Media']['filename']['name']);
			if(in_array($this->fileExtension, $this->supportedFileExtensions)) :
				# this means its a file (we don't need to specify a type on the input form)
				$this->Behaviors->detach('Encoders.Encodable');
				$this->data = $this->uploadFile($this->data);
			elseif (in_array($this->fileExtension, $this->supportedVideoExtensions)) :
				# this means its a video file (we don't need to specify a type on the input form)
				# @todo 	this won't be any good until there is a standardized field name for the filename.  (as in, this arbitrary, "submittedurl, or  submittedfile" thing probably won't work good.
				# @todo		put variables like, filePath, and urls, into the options part of this array.
                $this->data['Media']['type'] = 'video';
				$this->Behaviors->attach('Encoders.Encodable', array('type' => 'Zencoder'));
			elseif (in_array($this->fileExtension, $this->supportedAudioExtensions)) :
				# this means its a audio file (we don't need to specify a type on the input form)
				# @todo 	this won't be any good until there is a standardized field name for the filename.  (as in, this arbitrary, "submittedurl, or  submittedfile" thing probably won't work good.
				# @todo		put variables like, filePath, and urls, into the options part of this array.
              $this->data['Media']['type'] = 'audio';
				$this->Behaviors->attach('Encoders.Encodable', array('type' => 'Zencoder'));
			else :
				# it must an invalid file type
				# @todo throw an exception here
				return false;
			endif;
		else :
			# attach the econdable behavior by default
			# @todo		put variables like, filePath, and urls, into the options part of this array.
			$this->Behaviors->attach('Encoders.Encodable', array('type' => 'Zencoder'));
		endif;

		return true;
	}


	public function afterRate($data) {
		#debug($data);
	}//afterRate()



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
			$data['Media']['type'] = 'file';
			return $data;
		else :
			throw new Exception(__d('media', 'File Upload of ' . $data['Media']['filename']['name'] . ' to ' . $newFile . '  Failed'));
		endif;
	}


}//class{}