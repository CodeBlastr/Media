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
		'width' => 300,
		'height' => 300,
		'url' => array(),
		'class' => 'media-item',
		'conversion' => 'resizeCrop',
	);

	public $types = array(
		'images' => array(
			'jpg',
			'jpeg',
			'gif',
			'png'
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
			'xvid'
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
		'document' => array(
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
 * Display method
 * 
 * @param array (Media array)
 * @param array 
 */
	public function display($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		$item = isset($item['Media']) ? $item['Media'] : $item;
		if ($this->_getType($item)) {
			$method = $this->type . 'Media';
			return $this->$method($item);
		} else {
			return $this->noImage();
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
		$thumbImageOptions = array(
			'width' => $this->options['width'],
			'height' => $this->options['height'],
			'alt' => $item['title'],
			'class' => $this->options['class'] . ' media-image-thumb',
		);
		$image = $this->Html->image($imagePath, $thumbImageOptions, array(
			'conversion' => $this->options['conversion'],
			'quality' => 75,
			'alt' => 'thumbnail',
			'caller' => 'Media'
		));
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
	public function documentMedia($item) {
		$track = array($item['extension'] => $this->mediaUrl . $this->type . '/' . $item['filename'] . '.' . $item['extension']);
		return $this->_View->element('Media.document_display', array(
			'class' => $this->options['class'],
			'url' => $this->options['url'],
			'id' => $item['id'],
			'title' => $item['title']
		));
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
				$this->_getType($item);
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
 */
	protected function _getType($item) {
		foreach ($this->types as $type => $extensions) {
			if (in_array($item['extension'], $extensions)) {
				$this->type = $type;
				return true;
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
 * PhpThumb method
 * 
 * @todo the save path (thumbsPath) should be a CDN
 */
	public function phpthumb($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		if (!empty($item)) {
			$this->_getType($item);
			$image = $item['filename'] . '.' . $item['extension'];
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
 * No Image method
 */
 	public function noImage() {
 		return '<img src="/img/noImage.jpg" width="'.$this->options['width'].'" height="'.$this->options['height'].'" />';
 	}

}
