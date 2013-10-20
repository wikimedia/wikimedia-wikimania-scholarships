<?php

class Request {

	protected $query;

	protected $request;

	protected $attribs;

	protected $cookies;

	protected $files;

	protected $server;

	protected $content;

	public function __construct(
		array $query = array(),
		array $request = array(),
		array $attribs = array(),
		array $cookies = array(),
		array $files = array(),
		array $server = array(),
		$content = null
	) {
		$this->query = $query;
		$this->request = $request;
		$this->attribs = $attribs;
		$this->cookies = $cookies;
		$this->files = $files;
		$this->server = $server;
		$this->content = $content;
	}

	public static function newFromGlobals() {
		return new self( $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER );
	}

	public function getQuery() {
		return $this->query;
	}

	public function getRequest() {
		return $this->request;
	}

	public function getAttribs() {
		return $this->attribs;
	}

	public function getCookies() {
		return $this->cookies;
	}

	public function getFiles() {
		return $this->files;
	}

	public function getServer() {
		return $this->server;
	}

	public function getContent() {
		return $this->content;
	}

}
