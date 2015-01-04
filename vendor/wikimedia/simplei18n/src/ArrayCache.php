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
 * Load messages from an array.
 */
class ArrayCache implements MessageCache {

	/**
	 * @var array $messages
	 */
	protected $messages;

	/**
	 * @param array $messages
	 */
	public function __construct( array $messages ) {
		$this->messages = $messages;
	}

	public function getAvailableLanguages() {
		return array_keys( $this->messages );
	}

	public function get( $key, $langs ) {
		if ( !is_array( $langs ) ) {
			$langs = array( $langs );
		}
		foreach ( $langs as $lang ) {
			if ( isset( $this->messages[$lang][$key] ) ) {
				return $this->messages[$lang][$key];
			}
		}
		return false;
	}
}
