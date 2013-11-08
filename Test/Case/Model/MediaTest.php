<?php
App::uses('Media', 'Media.Model');

/**
 * Media Test Case
 *
 */
class MediaTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.Media.Media');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Media = ClassRegistry::init('Course');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Course);

		parent::tearDown();
	}

}
