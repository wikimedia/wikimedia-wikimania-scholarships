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

use Psr\Log\LoggerInterface;

class Lang {

	/**
	 * @var LoggerInterface $logger
	 */
	protected $logger;

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
	 * @param LoggerInterface $logger Log channel
	 */
	public function __construct( $dir, $default = 'en', $logger = null ) {
		$this->logger = $logger ?: new \Psr\Log\NullLogger();

		$this->langDir = $dir;
		if ( ! is_dir( $dir ) ) {
			$this->logger->error( 'Directory not found.', array(
				'method' => __METHOD__,
				'dir' => $dir,
			) );
		}

		$this->defaultLang = $default;

		$messages = array();
		foreach ( glob( "{$this->langDir}/*.json" ) as $file ) {
			if ( is_readable( $file ) ) {
				$json = file_get_contents( $file );
				if ( $json === false ) {
					$this->logger->error( 'Error reading file', array(
						'method' => __METHOD__,
						'file' => $file,
					) );
					continue;
				}

				$data = json_decode( $json, true );
				if ( $data === null ) {
					$this->logger->error( 'Error parsing json', array(
						'method' => __METHOD__,
						'file' => $file,
						'json_error' => json_last_error(),
					) );
					continue;
				}

				// Discard metadata
				unset( $data['@metadata'] );

				if ( empty( $data ) ) {
					// Ignore empty languages
					continue;
				}

				$lang = substr( basename( $file ), 0, -5 );
				if ( $lang === 'qqq' ) {
					// Ignore message documentation
					continue;
				}

				$messages[$lang] = $data;
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

		if ( $lang !== 'qqx' ) {
			if ( !in_array( $lang, $this->getLangs() ) ) {
				$lang = $this->defaultLang;
			}

			// remember this language selection for future requests
			$_SESSION['uselang'] = $lang;
		}

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

		if ( $this->lang === 'qqx' ) {
			return $key;
		}

		// Try the language, then english, then fail
		if ( isset( $this->messages[$this->lang][$key] ) ) {
			$msg = $this->messages[$this->lang][$key];

		} elseif ( isset( $this->messages['en'][$key] ) ) {
			$msg = $this->messages['en'][$key];

		} else {
			// FIXME: log missing translation
			$this->logger->warning( 'No translation for key "{key}" in {lang} or en',
				array(
					'method' => __METHOD__,
					'key' => $key,
					'lang' => $this->lang,
			) );
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
