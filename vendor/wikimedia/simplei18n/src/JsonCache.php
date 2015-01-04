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
 * Load messages from JSON files in a given directory.
 */
class JsonCache extends ArrayCache {

	/**
	 * @var LoggerInterface $logger
	 */
	protected $logger;

	/**
	 * @var string $messageDirectory
	 */
	protected $messageDirectory;

	/**
	 * @param string $directory Directory of json message files
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		$directory, LoggerInterface $logger = null
	) {
		$this->logger = $logger ?: new \Psr\Log\NullLogger();
		$this->messageDirectory = $directory;
		if ( !is_dir( $this->messageDirectory ) ) {
			$this->logger->error( 'Directory not found.', array(
				'method' => __METHOD__,
				'messageDirectory' => $this->messageDirectory,
			) );
		}
	}

	/**
	 * Load messages from message directory.
	 */
	protected function loadMessages() {
		if ( $this->messages === null ) {
			$this->messages = array();
			foreach ( glob( "{$this->messageDirectory}/*.json" ) as $file ) {
				$lang = strtolower( substr( basename( $file ), 0, -5 ) );
				if ( $lang === 'qqq' ) {
					// Ignore message documentation
					continue;
				}

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

					$this->messages[$lang] = $data;
				}
			}
		}
	}

	public function getAvailableLanguages() {
		$this->loadMessages();
		return parent::getAvailableLanguages();
	}

	public function get( $key, $langs ) {
		$this->loadMessages();
		return parent::get( $key, $langs );
	}
}
