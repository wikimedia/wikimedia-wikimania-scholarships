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
 * @coversDefaultClass \Wikimedia\SimpleI18n\Message
 * @uses \Wikimedia\SimpleI18n\Message
 * @uses \Wikimedia\SimpleI18n\I18nContext
 * @uses \Wikimedia\SimpleI18n\ArrayCache
 */
class MessageTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider messageGenerationTests
	 * @covers \Wikimedia\SimpleI18n\Message
	 */
	public function testMessageGeneration(
		$key, $message, $params, $plain, $escaped
	) {
		$ctx = $this->stubI18nContext( array(
			'en' => array(
				$key => $message,
			),
		) );

		$fixture = new Message( $key, $params, $ctx );

		$this->assertSame( $key, $fixture->getKey() );
		$this->assertTrue( $fixture->exists() );
		$this->assertFalse( $fixture->isBlank() );
		$this->assertSame( $plain, $fixture->plain() );
		$this->assertSame( $escaped, $fixture->escaped() );
	}

	public function messageGenerationTests() {
		return array(
			array(
				'test-key',
				'<something>',
				array(),
				'<something>',
				'&lt;something&gt;'
			),
			array(
				'test-key',
				'<$1>',
				array( 'something' ),
				'<something>',
				'&lt;something&gt;'
			),
			array(
				'test-key',
				'<$1>',
				array(),
				'<$1>',
				'&lt;$1&gt;'
			),
		);
	}

	/**
	 * @covers \Wikimedia\SimpleI18n\Message
	 */
	public function testMissingKey() {
		$ctx = $this->stubI18nContext( array( 'en' => array() ) );
		$fixture = new Message( 'missing-key', array(), $ctx );

		$this->assertSame( 'missing-key', $fixture->getKey() );
		$this->assertFalse( $fixture->exists() );
		$this->assertTrue( $fixture->isBlank() );
		$this->assertSame( '<missing-key>', $fixture->plain() );
		$this->assertSame( '&lt;missing-key&gt;', $fixture->escaped() );
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
