<?php
class MediaAppController extends AppController {
	protected function __returnJsonResponse($response) {
		$this->autoRender = false;
		$this->response->statusCode($response['statusCode']);
		$this->response->body(json_encode($response['body']));
	}
}
