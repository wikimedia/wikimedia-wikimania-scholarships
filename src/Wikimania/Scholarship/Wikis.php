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
 */

namespace Wikimania\Scholarship;

class Wikis {

	/**
	 * Names of wikis
	 *
	 * @var array $WIKI_NAMES
	 */
	public static $WIKI_NAMES = array(
		'Wikipedia',
		'Wiktionary',
		'Wikimedia Commons',
		'Wikibooks',
		'Wikisource',
		'Wikidata',
		'Wikiversity',
		'Wikiquote',
		'Wikispecies',
		'Wikinews',
		'Wikivoyage',
		'Meta',
		'Outreach',
		'Incubator',
		'Tool Labs',
		'MediaWiki'
	);


	/**
	 * Construction not allowed for utility class.
	 */
	private function __construct() {
		// no-op
	}
}
