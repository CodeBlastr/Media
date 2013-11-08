<?php
/**
 * MediaFixture
 *
 */
class MediaFixture extends CakeTestFixture {

	
/**
 * Import
 *
 * @var array
 */
	public $import = array('config' => 'Media.Media');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => null,
			'title' => null,
			'description' => null,
			'views' => null,
			'filename' => 'sample',
			'extension' => null,
			'type' => null,
			'thumbnail' => null,
			'model' => null,
			'foreign_key' => null,
			'creator_id' => 1,
			'modifier_id' => 1,
			'created' => '2013-10-22 18:03:16',
			'modified' => '2013-10-22 18:03:16',
			'data' => null
		),
	);
}
