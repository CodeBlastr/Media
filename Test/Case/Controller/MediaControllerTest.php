<?php
App::uses('MediaController', 'Media.Controller');

/**
 * TestCoursesController *
 */
class TestMediaController extends MediaController {
/**
 * Auto render
 *
 * @var boolean
 */
	public $autoRender = false;

/**
 * Redirect action
 *
 * @param mixed $url
 * @param mixed $status
 * @param boolean $exit
 * @return void
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

/**
 * CoursesController Test Case
 *
 */
class MediaControllerTestCase extends ControllerTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.Media.Media', 'plugin.Media.Media');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Media = new TestMediaController();
		$this->Media->constructClasses();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Media);

		parent::tearDown();
	}

}
