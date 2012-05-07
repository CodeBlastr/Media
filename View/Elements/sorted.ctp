<?php
/**
 * Media Sorted Element
 *
 * PHP versions 5
 *
 * Zuha(tm) : Business Management Applications (http://zuha.com)
 * Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.org)
 *
 * Licensed under GPL v3 License
 * Must retain the above copyright notice and release modifications publicly.
 *
 * @copyright     Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.com)
 * @link          http://zuha.com Zuha™ Project
 * @package       zuha
 * @subpackage    zuha.app.plugins.media.views.elements
 * @since         Zuha(tm) v 0.0.1
 * @license       GPL v3 License (http://www.gnu.org/licenses/gpl.html) and Future Versions
 * @author	  <joel@razorit.com>
 */

# this should be at the top of every element created with format __ELEMENT_PLUGIN_ELEMENTNAME_instanceNumber.
# it allows a database driven way of configuring elements, and having multiple instances of that configuration.
if(!empty($instance) && defined('__ELEMENT_MEDIA_SORTED_'.$instance)) {
	extract(unserialize(constant('__ELEMENT_MEDIA_SORTED_'.$instance)));
} else if (defined('__ELEMENT_MEDIA_SORTED')) {
	extract(unserialize(__ELEMENT_MEDIA_SORTED));
}

// Set the Options
$mediaType = !empty($mediaType) ? $mediaType : 'video';
$field = !empty($field) ? $field : 'views';
$sortOrder = !empty($sortOrder) ? $sortOrder : 'desc';
$numberOfResults = !empty($numberOfResults) ? $numberOfResults : 5;

// Get the Data
$media = $this->requestAction('/media/media/sorted/'.$mediaType.'/'.$field.'/'.$sortOrder.'/'.$numberOfResults);
#debug($media);

// Generate the Output
$output = '';
foreach ($media as $medium) {

   $output .= <<<HTML
   <li>
       <div class="media_sorted">
	    <div>
		<a href="/media/media/view/{$medium['Media']['id']}">
		    {$medium['Media']['title']}
		</a>
	    </div>
	    <div>
		<a href="/media/media/view/{$medium['Media']['id']}">
		    <img src="/theme/default/media/thumbs/{$medium['Media']['id']}_000{$medium['Media']['thumbnail']}.jpg" width="144" height="94" alt="{$medium['Media']['title']}" />
		</a>
	    </div>
	    <div>
		Rating = {$medium['Media']['rating']}
	    </div>
       </div>
    </li>
HTML;

}
?>

<div id="ELEMENT_MEDIA_SORTED_<?php echo $instance ?>">
    <ul>
	<?php echo $output ?>
    </ul>
</div>
