<?php
/**
 * @section LICENSE
 * This file is part of Wikimania Scholarship Application.
 *
 * Wikimania Scholarship Application is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Wikimania Scholarship Application is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with Wikimania Scholarship Application.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @file
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 * @copyright © 2013 Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
 * @copyright © 2012-2013 Katie Filbert, Wikimania 2012 Washington DC organizing team
 * @copyright © 2011 Harel Cain, Wikimania 2011 Haifa organizing team
 * @copyright © 2010 Wikimania 2010 Gdansk organizing team
 * @copyright © 2009 Wikimania 2009 Buenos Aires organizing team
 */

namespace Wikimania\Scholarship;

class Lang {

	/**
	 * @param array $messages
	 */
	protected $messages = array();

	/**
	 * Current language
	 * @param string $lang
	 */
	protected $lang;

	/**
	 * @param string $langDir
	 */
	protected $langDir;

	/**
	 * @param string $defaultLang
	 */
	protected $defaultLang;

	/**
	 * @param string $dir Directory containing language files
	 * @param string $default Default language
	 */
	public function __construct( $dir, $default = 'en' ) {
		// TODO: assert that the dir exists
		$this->langDir = $dir;
		$this->defaultLang = $default;

		$messages = array();
		foreach ( glob( "{$this->langDir}/*.php" ) as $file ) {
			if ( is_file( $file ) && is_readable( $file ) ) {
				include $file;
			}
		}
		$this->messages = $messages;
	}

	/**
	 * Get a list of available languages.
	 * @return array List of languages
	 */
	public function getLangs() {
		return array_keys( $this->messages );
	}

	/**
	 * Get the active language.
	 * @return string Selected language
	 */
	public function getLang() {
		if ( isset( $_REQUEST['uselang'] ) ) {
			$lang = $_REQUEST['uselang'];

		} elseif ( isset( $_SESSION['uselang'] ) ) {
			$lang = $_SESSION['uselang'];

		} else {
			$lang = $this->defaultLang;
		}

		if ( !in_array( $lang, $this->getLangs() ) ) {
			$lang = $this->defaultLang;
		}

		// remember this language selection for future requests
		$_SESSION['uselang'] = $lang;

		$this->lang = $lang;
		return $lang;
	}

	/**
	 * Get a message string.
	 *
	 * @param string $key Message name
	 * @param array $params Parameters to add to the message
	 * @return string Message
	 */
	public function message( $key, $params = array() ) {
		if ( $this->lang === null ) {
			$this->getLang();
		}

		// Try the language, then english, then fail
		if ( isset( $this->messages[$this->lang][$key] ) ) {
			$msg = $this->messages[$this->lang][$key];

		} elseif ( isset( $this->messages['en'][$key] ) ) {
			$msg = $this->messages['en'][$key];

		} else {
			// FIXME: log missing translation
			$msg = $key;
		}

		// Replace any $1, $2 style parameters
		$replace = array();
		foreach( $params as $n => $p ) {
			$replace['$' . ( $n + 1 )] = $p;
		}

		if ( is_array( $msg ) ) {
			foreach ( $msg as $key => $value ) {
				$msg[$key] = strtr( $msg[$key], $replace );
			}

		} else {
			$msg = strtr( $msg, $replace );
		}

		return $msg;
	}

}
