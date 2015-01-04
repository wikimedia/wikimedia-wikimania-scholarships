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
 * Interface message.
 */
class Message {

	/**
	 * @var LoggerInterface $logger
	 */
	protected $logger;

	/**
	 * @var string $key
	 */
	protected $key;

	/**
	 * @var array $params
	 */
	protected $params;

	/**
	 * @var I18nContext $ctx
	 */
	protected $ctx;

	/**
	 * @var string $format
	 */
	protected $format = 'plain';

	/**
	 * @var string $message
	 */
	protected $message;

	/**
	 * @param string $key
	 * @param array $params
	 * @param I18nContext $ctx
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		$key,
		array $params = array(),
		I18nContext $ctx,
		LoggerInterface $logger = null
	) {
		$this->logger = $logger ?: new \Psr\Log\NullLogger();
		$this->key = $key;
		$this->params = array_values( $params );
		$this->ctx = $ctx;
	}

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * Add parameters to the paramter list of this message.
	 *
	 * @param mixed $params,... Parameters as strings of a single argument that
	 * is an array of strings.
	 *
	 * @return Message Self
	 */
	public function params( /*...*/ ) {
		$args = func_get_args();
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args = $args[0];
		}
		$values = array_values( $args );
		$this->params = array_merge( $this->params, $values );
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @return I18nContext
	 */
	public function getI18nContext() {
		return $this->ctx;
	}

	/**
	 * Get message contents from MessageCache.
	 * @return string|bool False if message doesn't exist, otherwise raw
	 * message text
	 */
	protected function fetchMessage() {
		if ( $this->message === null ) {
			$this->message = $this->ctx->getMessageCache()->get(
				$this->key,
				array(
					$this->ctx->getCurrentLanguage(),
					$this->ctx->getDefaultLanguage(),
			) );
		}
		return $this->message;
	}

	/**
	 * @return bool
	 */
	public function exists() {
		return $this->fetchMessage() !== false;
	}

	/**
	 * @return bool
	 */
	public function isBlank() {
		$msg = $this->fetchMessage();
		return $msg === false || $msg === '';
	}

	/**
	 * @return string
	 */
	public function toString() {
		$lang = $this->ctx->getCurrentLanguage();
		if ( $lang === 'qqx' ) {
			return "({$this->key})";
		}
		$text = $this->fetchMessage();
		if ( $text === false ) {
			$this->logger->warning(
				'No translation for key "{key}" in {lang}',
				array(
					'method' => __METHOD__,
					'key' => $this->key,
					'lang' => $lang,
			) );
			$text = "<{$this->key}>";
		}

		// Replace any $N parameters
		$replace = array();
		foreach( $this->params as $n => $p ) {
			$replace['$' . ( $n + 1 )] = $p;
		}
		$text = strtr( $text, $replace );

		// Escape if asked
		if ( $this->format === 'escaped' ) {
			$text = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8', false );
		}

		return $text;
	}

	public function plain() {
		$this->format = 'plain';
		return $this->toString();
	}

	public function escaped() {
		$this->format = 'escaped';
		return $this->toString();
	}

	public function __toString() {
		try {
			return $this->toString();
		} catch ( Exception $ex ) {
			$this->logger->error(
				'Exception caught converting {{key}} to string',
				array(
					'key' => $this->key,
					'exception' => $ex,
			) );
			$text = "<{$this->key}>";
			if ( $this->format === 'escaped' ) {
				$text = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8', false );
			}
			return $text;
		}
	}
}
