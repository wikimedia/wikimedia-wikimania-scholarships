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
 * @coversDefaultClass \Wikimedia\SimpleI18n\I18nContext
 * @uses \Wikimedia\SimpleI18n\I18nContext
 */
class I18nContextTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			unset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		}
		if ( isset( $_REQUEST['uselang'] ) ) {
			unset( $_REQUEST['uselang'] );
		}
		if ( isset( $_SESSION['uselang'] ) ) {
			unset( $_SESSION['uselang'] );
		}
	}

	/**
	 * @dataProvider acceptLanguageTests
	 * @covers ::parseAcceptLanguage
	 */
	public function testParseAcceptLanguage( $given, $expect ) {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $given;
		$got = I18nContext::parseAcceptLanguage();

		$this->assertEquals( $expect, $got );
	}

	public function acceptLanguageTests() {
		return array(
			array( null, array() ),
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
	 * @dataProvider langFromRequestTests
	 * @covers ::__construct
	 * @covers ::getCurrentLanguage
	 * @covers ::setCurrentLanguage
	 */
	public function testGetLangFromRequest( $value, $expect ) {
		$_REQUEST['uselang'] = $value;
		$fixture = new I18nContext( $this->getMockCache(), 'default' );
		$this->assertEquals( $expect, $fixture->getCurrentLanguage() );
		$this->assertEquals( $expect, $_SESSION['uselang'] );
	}

	public function langFromRequestTests() {
		return array(
			array( 'en', 'en' ),
			array( null, 'default' ),
			array( '', 'default' ),
			array( 'fr', 'default' ),
		);
	}

	/**
	 * @dataProvider langFromSessionTests
	 * @covers ::__construct
	 * @covers ::getCurrentLanguage
	 * @covers ::setCurrentLanguage
	 */
	public function testGetLangFromSession( $value, $expect ) {
		$_SESSION['uselang'] = $value;
		$fixture = new I18nContext( $this->getMockCache(), 'default' );
		$this->assertEquals( $expect, $fixture->getCurrentLanguage() );
		$this->assertEquals( $expect, $_SESSION['uselang'] );
	}

	public function langFromSessionTests() {
		return array(
			array( 'en', 'en' ),
			array( null, 'default' ),
			array( '', 'default' ),
			array( 'fr', 'default' ),
		);
	}

	/**
	 * @dataProvider langFromHeaderTests
	 * @covers ::__construct
	 * @covers ::getCurrentLanguage
	 * @covers ::parseAcceptLanguage
	 * @covers ::setCurrentLanguage
	 */
	public function testGetLangFromHeader( $header, $expect ) {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $header;
		$fixture = new I18nContext( $this->getMockCache(), 'default' );
		$this->assertEquals( $expect, $fixture->getCurrentLanguage() );
		$this->assertEquals( $expect, $_SESSION['uselang'] );
	}

	public function langFromHeaderTests() {
		return array(
			array( 'bar;q=0.8,baz,en-us,en,default', 'en' ),
			array( null, 'default' ),
			array( '', 'default' ),
			array( 'fr', 'default' ),
		);
	}


	protected function getMockCache( $langs = array('en', 'default' ) ) {
		$cache = $this->getMock('\Wikimedia\SimpleI18n\MessageCache');
		$cache->expects($this->any())
			->method('getAvailableLanguages')
			->will($this->returnValue($langs));
		return $cache;
	}
}
