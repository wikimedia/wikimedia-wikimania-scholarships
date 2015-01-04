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
 * @coversDefaultClass \Wikimedia\SimpleI18n\ArrayCache
 * @uses \Wikimedia\SimpleI18n\ArrayCache
 */
class ArrayCacheTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::__construct
	 * @covers ::get
	 * @covers ::getAvailableLanguages
	 */
	public function testHappyPath() {
		$fixture = new ArrayCache( array(
			'en' => array( 'lang' => 'en' ),
			'foo' => array( 'lang' => 'foo' ),
		) );
		$langs = $fixture->getAvailableLanguages();
		$this->assertEquals( array( 'en', 'foo' ), $langs );
		foreach ( $langs as $lang ) {
			$this->assertEquals( $lang, $fixture->get( 'lang', $lang ) );
			$this->assertFalse( $fixture->get( 'key-not-found', $lang ) );
		}
	}
}
