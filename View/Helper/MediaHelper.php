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

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->mediaPath = DS . 'theme' . DS . 'default' . DS . 'media' . DS;
		$this->mediaUrl = '/theme/default/media/';
	}

	public function display($item, $options = array()) {
		$this->options = array_merge($this->options, $options);
		$item = isset($item['Media']) ? $item['Media'] : $item;
		if ($this->_getType($item)) {
			$method = $this->type . 'Media';
			return $this->$method($item);
		} else {
			return '<img src="/img/noImage.jpg" width="'.$this->options['width'].'" height="'.$this->options['height'].'" />';
		}
	}

/**
 * Carousel method
 */
	public function carousel($type = 'default', $options = array()) {
		return $this->_View->element('Media.carousels/' . $type, $options);
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
			'class' => 'media-image-thumb',
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
					'mp3' => $this->mediaUrl . $this->type . '/' . $item['filename'] . '.' . $item['extension'],
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

	public function videoMedia($item) {
	}

	protected function _getType($item) {
		foreach ($this->types as $type => $extensions) {
			if (in_array($item['extension'], $extensions)) {
				$this->type = $type;
				return true;
			}
		}
		return false;
	}

	public function loadData($options = array()) {
		$this->Model = ClassRegistry::init('Media.MediaGallery');
		$defaults = array();
		$options = Set::merge($options, $defaults);
		$data = $this->Model->find('all', $options);
		return $data;
	}

/**
 * Watermark method
 */
	public function watermark() {
		
	}

}
