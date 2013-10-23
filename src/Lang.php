<?php

class Lang {

	protected $messages = array();

	protected $lang;

	function __construct() {
		$this->loadMessages();
		$this->lang = 'en';
	}

	function langDir() {
		$langdir = __DIR__ . '/languages';
		return $langdir;
	}

	function langFiles() {
		$langfiles = scandir( $this->langDir() );
		return $langfiles;
	}

	function getLangs() {
		$langs = array();

		foreach ( $this->langFiles() as $file ) {
			if ( is_file( $this->langDir() . '/' . $file ) ) {
				$parts = explode( '.', $file );
				array_push( $langs, $parts[0] );
			}
		}

		return $langs;
	}

	function loadMessages() {
		foreach ( $this->langFiles() as $file ) {
			$fullname = $this->langDir() . '/' . $file;

			if ( is_file( $fullname ) ) {
				include ( $fullname );
			}
		}

		$this->messages = $messages;
	}

	public function setLang($req) {
		if ( isset( $req['uselang'] ) && in_array( $req['uselang'], $this->getLangs() ) ) {
			$lang = $req['uselang'];
		} else {
			$lang = 'en';
		}

		$this->lang = $lang;

		return $lang;
	}

	public function message( $key ) {
		if ( isset( $this->messages[$this->lang][$key] ) ) {
			return $this->messages[$this->lang][$key];
		} else {
			return $this->messages['en'][$key];
		}
	}

	public function formHasErrors( $key ) {
		return "<span class='fieldWithErrors'>" . $this->message( $key ). "</span>";
	}

}
