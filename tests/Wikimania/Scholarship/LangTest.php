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
class LangTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			unset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		}
		if ( isset( $_SESSION['uselang'] ) ) {
			unset( $_SESSION['uselang'] );
		}
	}

	/**
	 * @dataProvider acceptLanguageTests
	 * @covers Lang::parseAcceptLanguage
	 */
	public function testParseAcceptLanguage( $given, $expect ) {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $given;
		$got = Lang::parseAcceptLanguage();

		$this->assertEquals( $expect, $got );
	}

	public function acceptLanguageTests() {
		return array(
			array( '*', array() ),
			array( 'en', array( 'en' ) ),
			array( 'En', array( 'en' ) ),
			array( 'en;q=0', array() ),
			array(
				'en-ca,en;q=0.8,en-us;q=0.6,de-de;q=0.4,de;q=0.2',
				array( 'en-ca', 'en', 'en-us', 'de-de', 'de' )
			),
			array(
				'en-us, en-gb; q=0.8,en;q = 0.6,es-419',
				array( 'en-us', 'es-419', 'en-gb', 'en' )
			),
		);
	}

	/**
	 * @covers Lang::getLang
	 */
	public function testGetLangFromHeader() {
		$fixture = new Lang( __DIR__ . '/i18n', 'foo' );
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'bar;q=0.8,baz,en-us,en,foo';
		$this->assertEquals( 'en', $fixture->getLang() );
		$this->assertEquals( 'en', $_SESSION['uselang'] );
	}

	/**
	 * @covers Lang::getLang
	 */
	public function testGetLangWithoutHeader() {
		$fixture = new Lang( __DIR__ . '/i18n', 'foo' );
		$this->assertEquals( 'foo', $fixture->getLang() );
		$this->assertEquals( 'foo', $_SESSION['uselang'] );
	}

	/**
	 * @covers Lang::__construct
	 * @covers Lang::getLangs
	 */
	public function testGetLangs() {
		$fixture = new Lang( __DIR__ . '/i18n' );
		$this->assertEquals( array( 'bar', 'en', 'foo' ), $fixture->getLangs() );
	}

} //end LangTest
