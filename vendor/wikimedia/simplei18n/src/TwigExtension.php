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
 * Integrate SimpleI18n with the Twig template engine.
 */
class TwigExtension extends \Twig_Extension {

	/**
	 * @var I18nContext $ctx
	 */
	protected $ctx;

	/**
	 * @param I18nContext $ctx
	 */
	public function __construct( I18nContext $ctx ) {
		$this->ctx = $ctx;
	}

	public function getName() {
		return 'Wikimedia-SimpleI18n';
	}

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter(
				'message', array( $this, 'messageFilterCallback' )
			),
		);
	}

	/**
	 * Callback for 'message' filter.
	 *
	 * <code>
	 * {{ 'my-message-key'|message }}
	 * {{ 'my-message-key'|message( 'foo', 'bar' ) }}
	 * {{ 'my-message-key'|message( [ 'foo', 'bar' ] ) }}
	 * {{ 'my-message-key'|message( 'foo', 'bar' )|raw }}
	 * </code>
	 *
	 * @param string $key Message key
	 * @param string ... Message params
	 * @return string Unescaped message content
	 * @see I18nContext::message
	 */
	public function messageFilterCallback( $key /*...*/ ) {
		$params = func_get_args();
		array_shift( $params );
		if ( count( $params ) == 1 && is_array( $params[0] ) ) {
			// Unwrap args array
			$params = $params[0];
		}
		$msg = $this->ctx->message( $key, $params );
		return $msg->plain();
	}

	// 'foo'|message(p1, p2, p3)
}
