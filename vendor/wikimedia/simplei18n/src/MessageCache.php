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

/**
 * Container for localized messages.
 */
interface MessageCache {

	/**
	 * Get a list of all languages available in the message cache.
	 *
	 * @return array
	 */
	public function getAvailableLanguages();

	/**
	 * Get a message.
	 *
	 * @param string $key Message name
	 * @param string|array $langs List of possible languages in preference order
	 * @return string|bool False if message doesn't exist, otherwise the
	 * message
	 */
	public function get( $key, $langs );
}
