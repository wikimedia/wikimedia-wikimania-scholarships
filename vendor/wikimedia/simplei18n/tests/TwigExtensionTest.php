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
 * @coversDefaultClass \Wikimedia\SimpleI18n\TwigExtension
 * @uses \Wikimedia\SimpleI18n\TwigExtension
 */
class TwigExtensionTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		if ( !class_exists( '\Twig_Extension' ) ) {
			self::markTestSkipped( 'Unable to find \Twig_Extension.' );
		}
	}

	/**
	 * @dataProvider messageFilterCallbackTests
	 * @covers ::messageFilterCallback
	 * @uses \Wikimedia\SimpleI18n\Message
	 * @uses \Wikimedia\SimpleI18n\I18nContext
	 * @uses \Wikimedia\SimpleI18n\ArrayCache
	 */
	public function testMessageFilterCallback(
		$key, $message, $params, $expect
	) {
		$ctx = $this->stubI18nContext( array(
			'en' => array(
				$key => $message,
			),
		) );

		$fixture = new TwigExtension( $ctx );
		$this->assertSame(
			$expect,
			call_user_func_array(
				array( $fixture, 'messageFilterCallback' ),
				array_merge( array( $key ), $params )
		) );
	}

	public function messageFilterCallbackTests() {
		return array(
			array(
				'test-key',
				'<something>',
				array(),
				'<something>',
			),
			array(
				'test-key',
				'<$1>',
				array( 'something' ),
				'<something>',
			),
			array(
				'test-key',
				'<$1 $2>',
				array( 'something', '$3' ),
				'<something $3>',
			),
			array(
				'test-key',
				'<$1 $2>',
				array( array( 'something', '$3' ) ),
				'<something $3>',
			),
			array(
				'test-key',
				'<$1>',
				array(),
				'<$1>',
			),
		);
	}


	protected function stubI18nContext(
		$messages, $current = 'en', $default = 'en'
	) {
		$cache = new ArrayCache( $messages );
		$ctx = $this->getMockBuilder( '\Wikimedia\SimpleI18n\I18nContext' )
			->setConstructorArgs( array( $cache, $default ) )
			->setMethods( array( 'getCurrentLanguage' ) )
			->getMock();
		$ctx->expects( $this->any() )
			->method( 'getCurrentLanguage' )
			->will( $this->returnValue( $current ) );
		return $ctx;
	}
}
