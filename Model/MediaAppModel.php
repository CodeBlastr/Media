<?php
class MediaAppModel extends AppModel {

/**
 * Create the directories for this plugin if they aren't there already.
 */
	protected function __createDirectories() {
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
		} else {
			return true;
		}
	}
	
/**
 * Get the extension of a given file path
 *
 * @param
 *        	{string} A file name/path string
 */
	public function getFileExtension($filepath) {
		preg_match('/[^?]*/', $filepath, $matches);
		$string = $matches [0];
		
		$pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
		
		// check if there is any extension
		if (count($pattern) == 1) {
			return FALSE;
		}
		
		if (count($pattern) > 1) {
			$filenamepart = $pattern [count($pattern) - 1] [0];
			preg_match('/[^?]*/', $filenamepart, $matches);
			return strtolower($matches [0]);
		}
	}

	
/**
 * Menu Init method
 * Used by WebpageMenuItem to initialize when someone creates a new menu item for the users plugin.
 * 
 */
 	public function menuInit($data = null) {
 		App::uses('Media', 'Media.Model');
		$Media = new Media;
		// link to properties index and first property
		$data['WebpageMenuItem']['item_url'] = '/media/media_browser/filebrowser';
		$data['WebpageMenuItem']['item_text'] = 'File Manager';
		$data['WebpageMenuItem']['name'] = 'File Manager';
 		return $data;
 	}
}
