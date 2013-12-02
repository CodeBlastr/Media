<?php

App::uses('HtmlHelper', 'View/Helper');

class PhpThumbHelper extends HtmlHelper {
    
    private $php_thumb;
    private $options;
    private $file_extension;
    private $cache_filename;
    private $thumb_data;
    private $error;
    private $error_detail;
    
    private function init($options = array())    {
        $this->options = $options;
        $this->set_file_extension();
        $this->thumb_data = array();
        $this->error = 0;
    }
    
    private function set_file_extension()    {
        $this->file_extension = substr($this->options['src'], strrpos($this->options['src'], '.'), strlen($this->options['src']));
    }
    
/**
 * This might also be a good spot to do a CDN
 */
    private function set_cache_filename()    {
        ksort($this->options);
        $filename_parts = array();
        $cacheable_properties = array('src', 'new', 'w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'fltr', 'far');
        
        foreach($this->options as $key => $value)    {
            if(in_array($key, $cacheable_properties))    {
                $filename_parts[$key] = $value;
            }
        }
        
        $this->cache_filename = '';
        
        foreach($filename_parts as $key => $value)    {
            $this->cache_filename .= $key . $value;
        }
        
        $last_modified = date("F d Y H:i:s.", filectime($this->options['src']));
        $this->cache_filename = $this->options['save_path'] . 'tmp' . DS . md5($this->cache_filename . $last_modified) . $this->file_extension;
    }
    
    private function image_is_cached() {
        if(is_file($this->cache_filename))    {
            return true;
        }
        return false;
    }
    
    private function create_thumb() {
        App::import('Vendor', 'Media.Vendor', array('file' => 'PhpThumb'.DS.'phpthumb.class.php'));
    	
        $this->php_thumb = new phpThumb();
        
        foreach($this->php_thumb as $var => $value) {
            if(isset($this->options[$var]))    {
                $this->php_thumb->setParameter($var, $this->options[$var]);
            }
        }
        
        if($this->php_thumb->GenerateThumbnail()) {
            $this->php_thumb->RenderToFile($this->cache_filename);
        } else {
            $this->error = 1;
            $this->error_detail = ereg_replace("[^A-Za-z0-9\/: .]", "", $this->php_thumb->fatalerror);
        }
    }
    
    private function get_thumb_data() {
    	$this->thumb_data['error'] = $this->error;
    	
        if($this->error)    {
            $this->thumb_data['error_detail'] = $this->error_detail;
            $this->thumb_data['src'] = $this->options['error_image_path'];
        } else    {
            $this->thumb_data['src'] = $this->options['display_path'] . '/' . substr($this->cache_filename, strrpos($this->cache_filename, DS) + 1, strlen($this->cache_filename));
        }
        
        if(isset($this->options['w']))    {
            $this->thumb_data['w'] = $this->options['w'];
        } else {
            $this->thumb_data['w'] = null;
        }
        
        if(isset($this->options['h']))    {
             $this->thumb_data['h'] = $this->options['h'];
        } else {
            $this->thumb_data['h'] = null;
        }
        
        return $this->thumb_data;
    }
    
    private function validate()	{
    	if(!is_file($this->options['src']))	{
    		$this->error = 1;
    		$this->error_detail = 'File ' . $this->options['src'] . ' does not exist';
    		return;
    	}
    	
    	$valid_extensions = array('.gif', '.jpg', '.jpeg', '.png');
    	
    	if(!in_array(strtolower($this->file_extension), $valid_extensions))	{
    		$this->error = 1;
    		$this->error_detail = 'File ' . $this->options['src'] . ' is not a supported image type';
    		return;
    	}
    }
    
    public function generate($options = array())    {
    	$this->init($options);
    	
    	$this->validate();
    	
    	if(!$this->error)    {
    		$this->set_cache_filename();
        	if(!$this->image_is_cached())    {
        	    $this->create_thumb();
        	}
        }
        return $this->get_thumb_data();
    }

	public function generateThumbnail($image, $options) {
        $thumbsPath = Configure::read('PhpThumb.thumbsPath');
        $displayPath = Configure::read('PhpThumb.displayPath');
        if (empty($thumbsPath)) {
            return false;
        }
        $pathOptions = array(
            'save_path' => $thumbsPath,
            'display_path' => $displayPath,
            'error_image_path' => Configure::read('PhpThumb.error_image_path')
        );
		
        if (!empty($options['model'])) {
            // model images from MeioUpload
            if (empty($options['field'])) {
                return false;
            }
            $meioupload_path = Configure::read('PhpThumb.meioupload_path');
            if (empty($meioupload_path)) {
                $meioupload_path = 'uploads';
            }
            $options['src'] = sprintf('%s%s/%s/%s/%s',
                WWW_ROOT,
                $meioupload_path,
                strtolower($options['model']),
                $options['field'],
                $image
            );
            unset($options['model']);
            unset($options['field']);
        } else {
            $options['src'] = $thumbsPath . $image;
        }
        $finalOptions = array_merge($options, $pathOptions);
        return $this->generate($finalOptions);
    }

/**
 * Url method
 */
    public function url($image = null, $options = array()) {
        $thumbnail = $this->generateThumbnail($image, $options);
        if (!empty($thumbnail['error'])) {
            return false;
        }
        return $thumbnail['src'];
    }

/**
 * Thumbnail method
 */
    public function thumbnail($image, $options, $htmlOptions = array()) {
        $thumbnail = $this->generateThumbnail($image, $options);
        if (!empty($thumbnail['error'])) {
			debug($this->error_detail);
            return false;
        }
        return $this->image($thumbnail['src'], array_merge(
            array('width' => $thumbnail['w'], 'height' => $thumbnail['h']),
            $htmlOptions
        ));
    }
}
