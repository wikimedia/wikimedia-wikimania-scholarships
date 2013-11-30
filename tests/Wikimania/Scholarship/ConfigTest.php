<?php
/**
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace Wikimania\Scholarship;

/**
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {

	public function testLoad() {
		Config::load( __DIR__ . '/ConfigTest.env' );

		$this->assertEnv( 'foo', 'FOO' );
		$this->assertEnv( 'with internal spaces', 'SPACED' );
		$this->assertEnv( '', 'EMPTY' );

		$this->assertEnv( 'foo', 'QFOO' );
		$this->assertEnv( 'with internal spaces', 'QSPACED' );
		$this->assertEnv( '', 'QEMPTY' );
		$this->assertEnv( '"hello world"', 'QESC' );

		$this->assertEnv( "this isn't simple = true;", 'COMPLEX' );
	}


	/**
	 * Assert that an expected value is present in getenv(), $_ENV and $_SERVER.
	 * @param string $expect Expected value
	 * @param string $var Variable to assert on
	 */
	protected function assertEnv( $expect, $var ) {
		$this->assertEquals( $expect, getenv( $var ) );
		$this->assertEquals( $expect, $_ENV[$var] );
		$this->assertEquals( $expect, $_SERVER[$var] );
	}

} //end ConfigTest
