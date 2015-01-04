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
 * @covers \Wikimedia\SimpleI18n\TwigExtension
 * @uses \Wikimedia\SimpleI18n\Message
 * @uses \Wikimedia\SimpleI18n\I18nContext
 * @uses \Wikimedia\SimpleI18n\ArrayCache
 */
class TwigIntegrationTest extends \Twig_Test_IntegrationTestCase {

	public function getExtensions() {
		return array(
			new TwigExtension( $this->stubI18nContext( array(
				'en' => array(
					'test-key-no-params' => '<something>',
					'test-key-1-param' => '<$1>',
					'test-key-2-params' => '<$1 $2>',
					'test-key-3-params' => '<$1 $2 $3>',
				),
			) ) ),
		);
	}

	public function getFixturesDir() {
		return __DIR__ . '/fixtures/TwigIntegrationTest';
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
