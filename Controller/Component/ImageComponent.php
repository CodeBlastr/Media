<?php
App::uses('Component', 'Controller');
class ImageComponent extends Component {

	public $resizeDefaults = array(
			'cType' => 'resize',
			'imgFolder' => false,
			'newName' => false,
			'newWidth' => 100,
			'newHeight' => 100,
			'quality' => 75,
			'bgcolor' => false
	);

	public function image_type_to_extension($imagetype) {
		if (empty($imagetype)) {
			return false;
		}
		switch ($imagetype) {
			case IMAGETYPE_GIF :
				return 'gif';
			case IMAGETYPE_JPEG :
				return 'jpg';
			case IMAGETYPE_PNG :
				return 'png';
			case IMAGETYPE_SWF :
				return 'swf';
			case IMAGETYPE_PSD :
				return 'psd';
			case IMAGETYPE_BMP :
				return 'bmp';
			case IMAGETYPE_TIFF_II :
			case IMAGETYPE_TIFF_MM :
				return 'tiff';
			case IMAGETYPE_JPC :
				return 'jpc';
			case IMAGETYPE_JP2 :
				return 'jp2';
			case IMAGETYPE_JPX :
				return 'jpf';
			case IMAGETYPE_JB2 :
				return 'jb2';
			case IMAGETYPE_SWC :
				return 'swc';
			case IMAGETYPE_IFF :
				return 'aiff';
			case IMAGETYPE_WBMP :
				return 'wbmp';
			case IMAGETYPE_XBM :
				return 'xbm';
			default :
				return false;
		}
	}

	/**
	 * Resize Image function - uses GD Library
	 *
	 * @param $id =
	 *        	Media ID
	 * @param $options =
	 *        	array(
	 *        	'cType' => { resize | resizeCrop | crop },
	 *        	'location' => { Location of original media },
	 *        	'newName' => { Defaults to False },
	 *        	'newWidth' => { New Image Width },
	 *        	'newHeight' => { New Image Height },
	 *        	'quality' => { new image quality 0-100 },
	 *        	'bgcolor => { New Backround Color }
	 *        	)
	 *        	
	 * @return bool
	 */
	public function _resizeImage($id = null, $params = array()) {
		if (empty($id)) {
			return false;
		} else {
			$media = $this->Media->read(array(
					'filename',
					'extension',
					$id
			));
		}
		
		$params = array_merge($this->resizeDefaults, $params);
		
		if (file_exists($img)) {
			list ( $oldWidth, $oldHeight, $type ) = getimagesize($img);
			$ext = $this->image_type_to_extension($type);
			
			// check for and create cacheFolder
			$cacheFolder = 'cache';
			$cachePath = $imgFolder . $cacheFolder;
			if (is_dir($cachePath)) {
				// do nothing the cache dir exists
			} else {
				if (mkdir($cachePath)) {
					// do nothing the cache dir exists
				} else {
					debug('Could not make images ' . $cachePath . ', and it doesn\'t exist.');
					break;
				}
			}
			
			// check to make sure that the file is writeable, if so, create destination image (temp image)
			if (is_writeable($cachePath)) {
				if ($newName) {
					$dest = $cachePath . DS . $newName . '.' . $id;
				} else {
					$dest = $cachePath . DS . 'tmp_' . $id;
				}
			} else {
				// if not let developer know
				$imgFolder = substr($imgFolder, 0, strlen($imgFolder) - 1);
				$imgFolder = substr($imgFolder, strrpos($imgFolder, '\\') + 1, 20);
				debug("You must allow proper permissions for image processing. And the folder has to be writable.");
				debug("Run \"chmod 775 on '$imgFolder' folder\"");
				exit();
			}
			
			// check to make sure that something is requested, otherwise there is nothing to resize.
			// although, could create option for quality only
			if ($newWidth || $newHeight) {
				/*
				 * check to make sure temp file doesn't exist from a mistake or system hang up. If so delete.
				 */
				if (file_exists($dest)) {
					$size = @getimagesize($dest);
					return array(
							'path' => $cacheFolder . '/' . $newName . '.' . $id,
							'width' => $size [0],
							'height' => $size [1]
					);
					// nlink($dest);
				} else {
					switch ($cType) {
						default :
						case 'resize' :
							// Maintains the aspect ration of the image and makes sure that it fits
							// within the maxW(newWidth) and maxH(newHeight) (thus some side will be smaller)
							$widthScale = 2;
							$heightScale = 2;
							
							if ($newWidth) {
								$widthScale = $newWidth / $oldWidth;
							}
							if ($newHeight) {
								$heightScale = $newHeight / $oldHeight;
								// debug("W: $widthScale H: $heightScale<br>");
							}
							if ($widthScale < $heightScale) {
								$maxWidth = $newWidth;
								$maxHeight = false;
							} elseif ($widthScale > $heightScale) {
								$maxHeight = $newHeight;
								$maxWidth = false;
							} else {
								$maxHeight = $newHeight;
								$maxWidth = $newWidth;
							}
							
							if ($maxWidth > $maxHeight) {
								$applyWidth = $maxWidth;
								$applyHeight = ($oldHeight * $applyWidth) / $oldWidth;
							} elseif ($maxHeight > $maxWidth) {
								$applyHeight = $maxHeight;
								$applyWidth = ($applyHeight * $oldWidth) / $oldHeight;
							} else {
								$applyWidth = $maxWidth;
								$applyHeight = $maxHeight;
							}
							// ebug("oW: $oldWidth oH: $oldHeight mW: $maxWidth mH: $maxHeight<br>");
							// ebug("aW: $applyWidth aH: $applyHeight<br>");
							$startX = 0;
							$startY = 0;
							// xit();
							break;
						case 'resizeCrop' :
							// -- resize to max, then crop to center
							$ratioX = $newWidth / $oldWidth;
							$ratioY = $newHeight / $oldHeight;
							
							if ($ratioX < $ratioY) {
								$startX = round(($oldWidth - ($newWidth / $ratioY)) / 2);
								$startY = 0;
								$oldWidth = round($newWidth / $ratioY);
								$oldHeight = $oldHeight;
							} else {
								$startX = 0;
								$startY = round(($oldHeight - ($newHeight / $ratioX)) / 2);
								$oldWidth = $oldWidth;
								$oldHeight = round($newHeight / $ratioX);
							}
							$applyWidth = $newWidth;
							$applyHeight = $newHeight;
							break;
						case 'crop' :
							// -- a straight centered crop
							$startY = ($oldHeight - $newHeight) / 2;
							$startX = ($oldWidth - $newWidth) / 2;
							$oldHeight = $newHeight;
							$applyHeight = $newHeight;
							$oldWidth = $newWidth;
							$applyWidth = $newWidth;
							break;
					}
					
					switch ($ext) {
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
							// image type is not a possible option
							return false;
							break;
					}
					
					// create new image
					$newImage = imagecreatetruecolor($applyWidth, $applyHeight);
					
					if ($bgcolor) {
						// set up background color for new image
						sscanf($bgcolor, "%2x%2x%2x", $red, $green, $blue);
						$newColor = ImageColorAllocate($newImage, $red, $green, $blue);
						imagefill($newImage, 0, 0, $newColor);
					}
					
					// preserve transparency
					if ($ext == 'gif' || $ext == 'png') {
						imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
						imagealphablending($newImage, false);
						imagesavealpha($newImage, true);
					}
					
					// put old image on top of new image
					imagecopyresampled($newImage, $oldImage, 0, 0, $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
					switch ($ext) {
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
					
					if (!($newName)) {
						unlink($img);
						rename($dest, $img);
					}
					
					$size = @getimagesize($cacheFolder . '/' . $newName . '.' . $id);
					return array(
							'path' => $cacheFolder . '/' . $newName . '.' . $id,
							'width' => $applyWidth,
							'height' => $applyHeight
					);
				}
			} else {
				return false;
			}
		} else {
			return false; // end the check for if the file to convert even exists
		}
	}
}
