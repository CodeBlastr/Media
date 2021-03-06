<?php
App::uses('AppHelper', 'View/Helper');
class MediaHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Form',
		'Url',
		'Media.PhpThumb'
	);

	public $options = array(
		'width' => null, // was 300 but sometimes we just don't want any width or height attribute, so should only have value if specified
		'height' => null, // was 300 but sometimes we just don't want any width or height attribute, so should only have value if specified
		'url' => array(),
		'class' => 'media-item',
		'conversion' => 'resizeCrop',
	);

	public $types = array(
		'images' => array(
			'jpg',
			'jpeg',
			'gif',
			'png',
			'bmp'
		),
		'video' => array(
			'mpg',
			'mov',
			'wmv',
			'rm',
			'3g2',
			'3gp',
			'3gp2',
			'3gpp',
			'3gpp2',
			'avi',
			'divx',
			'dv',
			'dv-avi',
			'dvx',
			'f4v',
			'flv',
			'h264',
			'hdmov',
			'm4v',
			'mkv',
			'mp4',
			'mp4v',
			'mpe',
			'mpeg',
			'mpeg4',
			'mpg',
			'nsv',
			'qt',
			'swf',
			'xvid',
			'youtube'
		),
		'audio' => array(
			'aif',
			'mid',
			'midi',
			'mka',
			'mp1',
			'mp2',
			'mp3',
			'mpa',
			'wav',
			'aac',
			'flac',
			'ogg',
			'ra',
			'raw',
			'wma'
		),
		'docs' => array(
			'pdf',
			'doc',
			'docx',
			'ods',
			'odt'
		),
	);

/**
 * Constructor
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->mediaPath = DS . 'theme' . DS . 'default' . DS . 'media' . DS;
		$this->mediaUrl = '/theme/default/media/';
		$this->streamUrl = Router::url(array('plugin' => 'media', 'controller' => 'media', 'action' => 'stream'));
	}

/**
 * After render file
 */
	public function afterRenderFile($viewFile, $content) {
		// reset because there can be conflicts with more than one display() on a page
		$defaults = get_class_vars('MediaHelper');
		$this->options = $defaults['options'];
	}

/**
 * Display method
 * 
 * @param array (Media array)
 * @param array 
 */
	public function display($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		$item = isset($item['Media']) ? $item['Media'] : $item;
		if ($this->getType($item)) {
			$method = $this->type . 'Media';
			return $this->$method($item);
		} else {
			return $this->noImage();
		}
	}

/**
 * Thumb method
 * 
 * @param array (Media array)
 * @param array 
 */
	public function thumb($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		$item = isset($item['Media']) ? $item['Media'] : $item;
		if ($this->getType($item)) {
			 // call thumb method if it exists, else get the standard pre-dating Media function
			$method = method_exists($this, $this->type . 'Thumb') ? $this->type . 'Thumb' : $this->type . 'Media';
			return $this->$method($item);
		} else {
			return $this->noImage();
		}
	}
	
	public function videoThumb($item) {
		if ($item['extension'] == 'youtube') {
			parse_str(parse_url($item['filename'], PHP_URL_QUERY), $vars);
			return $this->_View->element('Media.youtube_thumb_display', array(
				'youtubeId' => $vars['v'],
				'id' => $item['id'],
				'options' => $this->options
			));
		} else {
			debug('have not written any non youtube video thumb functions');
			exit;
		}
	}

/**
 * Find method
 */
 	public function find($type = 'first', $params = array()) {
		App::uses('MediaAttachment', 'Media.Model');
		$MediaAttachment = new MediaAttachment;
		// $params['contain'][] = 'Media'; contain isn't working here, and I don't know why???? RK
		$attachments = $MediaAttachment->find($type, $params);
		if (!empty($attachments[0])) {
			// $type = 'all'
			$ids = Set::extract('/MediaAttachment/media_id');
		} else {
			// $type = 'first'
			$ids = $attachments['MediaAttachment']['media_id'];
		}
		App::uses('Media', 'Media.Model');
		$Media = new Media;
 		return $Media->find('all', array('conditions' => array('Media.id' => $ids)));
 	}
	
/**
 * Show method
 * Like display except it will look up the image for you if you give a model and foreignKey
 * 
 * @param array find params
 */
 	public function show(array $params, $options = array()) {
 		$media = $this->find('first', $params);
		return $this->display($media[0]['Media'], $options);
 	}

/**
 * Images media
 * 
 * @param array
 */
	public function imagesMedia($item) {
		$imagePath = $this->mediaUrl . $this->type . '/' . $item['filename'] . '.' . $item['extension'];
		$thumbImageOptions = array_merge(array(
			'width' => $this->options['width'],
			'height' => $this->options['height'],
			'alt' => $item['title'],
			'class' => $this->options['class'] . ' media-image-thumb',
		), $this->options);
		
		$extOptions = array('conversion' => $this->options['conversion'], 'quality' => 70, 'alt' => 'thumbnail', 'caller' => 'Media');
		$image = $this->Html->image($imagePath, $thumbImageOptions, $extOptions);
		
		return $this->_View->element('Media.image_display', array(
			'image' => $image,
			'class' => $this->options['class'],
			'url' => $this->options['url'],
			'id' => $item['id'],
		));
	}

/**
 * Audio display helper uses jplayer see
 * http://jplayer.org/
 */
	public function audioMedia($item) {
		$track = array($item['extension'] => $this->mediaUrl . $this->type . '/' . $item['filename'] . '.' . $item['extension']);
		return $this->_View->element('Media.audio_display', array(
			'tracks' => json_encode($track),
			'class' => $this->options['class'],
			'url' => $this->options['url'],
			'id' => $item['id'],
			'title' => $item['title']
		));
	}

/**
 * Audio display helper uses jplayer see
 * http://jplayer.org/
 */
	public function docsMedia($item) {
		$file = array($item['extension'] => $this->mediaUrl . $this->type . '/' . $item['filename'] . '.' . $item['extension']);
		return $this->_View->element('Media.document_display', array_merge(array(
			'class' => $this->options['class'],
			'url' => $file[$item['extension']],
			'id' => $item['id'],
			'title' => $item['title']
		), $file));
	}

/**
 * jplayer display helper uses jplayer see
 * http://jplayer.org/
 */
	public function jplayer($items, $options = array()) {
		$tracks = array();
		if (is_array($items)) {
			foreach ($items as $item) {
				$item = isset($item['Media']) ? $item['Media'] : $item;
				$this->getType($item);
				$track = array(
					'title' => $item['title'],
					 $item['extension'] => $this->streamUrl.'/'.$item['filename'].'.'.$item['extension'],
					'poster' => ''
				);
				$tracks[] = $track;
			}
		} else {
			$this->audioMedia($items);
		}
		return $this->_View->Element('Media.jplayer_list', array(
			'tracks' => json_encode($tracks),
			'class' => $this->options['class'],
			'url' => $this->options['url'],
			'id' => $item['id'],
			'title' => $item['title']
		));
	}

	public function videoMedia($item, $options = array()) {
		if ($item['extension'] === 'youtube') {
			return $this->_View->element('Media.youtube_display', array(
				'url' => $item['filename'],
				'height' => $this->options['height'],
				'width' => $this->options['width'],
				'class' => $this->options['class'],
				'id' => $this->options['id'],
			));
		}
		return $this->_View->element('Media.video_display', array(
			'url' => $this->streamUrl . '/' . $item['filename'] . '.' . $item['extension'],
			'height' => $this->options['height'],
			'width' => $this->options['width'],
			'class' => $this->options['class'],
			'id' => $this->options['id'],
		));
	}

/**
 * Get Type method
 * 
 * @param array
 * @return string
 */
	public function getType($item) {
		foreach ($this->types as $type => $extensions) {
			if (!empty($item['extension']) && in_array($item['extension'], $extensions)) {
				$this->type = $type;
				return $type;
			}
		}
		return false;
	}

/**
 * Load data method
 */
	public function loadData($options = array()) {
		$this->Model = ClassRegistry::init('Media.MediaGallery');
		$defaults = array();
		$options = Set::merge($options, $defaults);
		$data = $this->Model->find('all', $options);
		return $data;
	}

/**
 * Carousel method
 */
	public function carousel($type = 'default', $options = array()) {
		return $this->_View->element('Media.carousels/' . $type, $options);
	}

/**
 * @todo the save path (thumbsPath) should be a CDN
 * 
 * @param array $item
 * @param array $options
 * @return string|false
 */
	public function phpthumb($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		if (!empty($item)) {
			if (is_array($item)) {
				$image = $item['filename'] . '.' . $item['extension'];
			} else {
				$image = $item;
			}
			$this->getType($item);
			Configure::write('PhpThumb.thumbsPath', ROOT . DS . SITE_DIR . DS . 'Locale' . DS . 'View' . DS . 'webroot' . DS . 'media' . DS . $this->type . DS );
			Configure::write('PhpThumb.displayPath', $this->mediaUrl . $this->type . '/' . 'tmp');
			return $this->PhpThumb->thumbnail($image, $options, $options);
		} else {
			Configure::write('PhpThumb.thumbsPath', ROOT . DS . 'app' . DS . 'webroot' . DS . 'img' . DS);
			Configure::write('PhpThumb.displayPath', '/' . 'img' . '/' . 'tmp');
			return $this->PhpThumb->thumbnail('lgnoimage.gif', $options, $options);
		}
	}

/**
 * Returns an Image Tag of the default "no image found" image
 *
 * @return string
 */
 	public function noImage() {
		$locale = ROOT . DS . SITE_DIR . DS . 'Locale' . DS . 'View' . DS . 'webroot' . DS . 'img' . DS;
		$root = ROOT . DS . 'app' . DS . 'webroot' . DS . 'img' . DS;
		$path = file_exists($locale . 'lgnoimage.gif') ? $locale : $root;
		Configure::write('PhpThumb.thumbsPath', $path);
		Configure::write('PhpThumb.displayPath', '/' . 'img' . '/' . 'tmp');
 		return $this->PhpThumb->thumbnail('lgnoimage.gif', $this->options, $this->options);
 	}

}
