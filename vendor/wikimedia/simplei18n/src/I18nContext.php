<?php
/**
 * @section LICENSE
 * This file is part of MediaWiki simple i18n.
 *
 * MediaWiki simple i18n is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * MediaWiki simple i18n is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with MediaWiki simple i18n.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 */

namespace Wikimedia\SimpleI18n;

use Psr\Log\LoggerInterface;

/**
 * Holds and optionally detects langauge for current request. Also serves as
 * a factory for creating Message objects which can be used to subsitute
 * localized message content based on the current language.
 *
 * Collaborates with a MessageCache instance to find localized content for
 * a given Message name.
 *
 * When language detection is performed, the current request paramters are
 * examined for a 'uselang' key. If the get/post data does not specifiy
 * a language the current PHP session is checked for the same key. Finally
 * detection falls back to the Accept-Language header. The best match between
 * languages specified by the header and languages available in the current
 * message cache is used. Any value chosen is stored in the current PHP
 * session for use by later requests.
 *
 * Based on code from the MediaWiki core application and the Wikimania
 * Scholarships application.
 *
 * @copyright © 2014 Bryan Davis and Wikimedia Foundation
 */
class I18nContext {

	/**
	 * @var LoggerInterface $logger
	 */
	protected $logger;

	/**
	 * @var string $defaultLanguage
	 */
	protected $defaultLanguage;

	/**
	 * @var MessageCache $messageCache
	 */
	protected $messageCache;

	/**
	 * @var string $currentLang
	 */
	protected $currentLang;

	/**
	 * @param MessageCache $cache
	 * @param string $defaultLanguage Language code
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		MessageCache $cache,
		$defaultLanguage = 'en',
		LoggerInterface $logger = null
	) {
		$this->logger = $logger ?: new \Psr\Log\NullLogger();
		$this->messageCache = $cache;
		$this->defaultLanguage = $defaultLanguage;
	}

  /**
   * @param string Language code
   */
	public function setDefaultLangauge( $lang ) {
		$this->defaultLanguage = $lang;
	}

  /**
   * @return string
   */
	public function getDefaultLanguage() {
		return $this->defaultLanguage;
	}

	/**
	 * @return MessageCache
	 */
	public function getMessageCache() {
		return $this->messageCache;
	}

	/**
	 * @return array
	 */
	public function getAvailableLanguages() {
		return $this->messageCache->getAvailableLanguages();
	}

	/**
	 * @param string $lang Language code
	 */
	public function setCurrentLanguage( $lang ) {
		if ( $lang !== 'qqx' ) {
			if ( !in_array( $lang, $this->getAvailableLanguages() ) ) {
				$lang = $this->defaultLanguage;
			}

			// Remember this language selection for future requests
			$_SESSION['uselang'] = $lang;
		}
		$this->currentLang = $lang;
	}

	/**
	 * Get current language.
	 *
	 * If no language has been set or detected, this will attempt to detect
	 * the user's preferred language by examining the current request, session
	 * and HTTP headers.
	 *
	 * The key 'uselang' is checked in the HTTP request and PHP session. If
	 * not set in either, the Accept-Language header from the request is
	 * parsed to determine potential langauges to select. In all cases, the
	 * current list of known localizations is consulted and if no match is
	 * found, the configured system wide default language is used as
	 * a fallback.
	 *
	 * @return string
	 */
	public function getCurrentLanguage() {
		if ( $this->currentLang === null ) {
			$lang = $this->defaultLanguage;
			if ( isset( $_REQUEST['uselang'] ) ) {
				$lang = $_REQUEST['uselang'];

			} elseif ( isset( $_SESSION['uselang'] ) ) {
				$lang = $_SESSION['uselang'];

			} else {
				$wants = self::parseAcceptLanguage();
				$bestMatches = array_intersect(
					$wants, $this->getAvailableLanguages()
				);
				if ( $bestMatches ) {
					$lang = current( $bestMatches );
				}
			}

			$this->setCurrentLanguage( $lang );
		}
		return $this->currentLang;
	}

	/**
	 * Get a message.
	 *
	 * @param string $key Message name
	 * @param array $params Parameters to add to the message
	 * @return Message
	 */
	public function message( $key, $params = array() ) {
		return new Message( $key, $params, $this, $this->logger );
	}

	/**
	 * Parse the Accept-Language header present in the request and return an
	 * ordered list of languages preferred by the user-agent.
	 *
	 * @return array List of preferred languages
	 */
	public static function parseAcceptLanguage() {
		if ( !isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			return array();
		}

		$weighted = array();

		// Strip any whitespace in the header value
		$hdr = preg_replace( '/\s+/', '', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		// Split on commas
		$parts = explode( ',', $hdr );
		foreach ( $parts as $idx => $part ) {
			if ( strpos( $part, ';q=' ) ) {
				// Extract relative weight from component
				list( $lang, $weight ) = explode( ';q=', $part );
				$weight = (float)$weight;

			} else {
				$lang = $part;
				$weight = 1;
			}

			if ( $lang != '*' && $weight > 0 ) {
				// Decorate the weight with the original position to make sort
				// stable
				$weighted[strtolower( $lang )] = array( $weight, -$idx );
			}
		}

		arsort( $weighted );
		return array_keys( $weighted );
	}
}
