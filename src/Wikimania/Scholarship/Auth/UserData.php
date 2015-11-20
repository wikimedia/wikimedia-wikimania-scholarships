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
 */

namespace Wikimania\Scholarship\Auth;

use ArrayAccess;
use Wikimedia\Slimapp\Auth\UserData as SlimUserData;

/**
 * User data
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2015 Bryan Davis and Wikimedia Foundation.
 */
class UserData implements ArrayAccess, SlimUserData {

	/**
	 * @var array $data
	 */
	protected $data;

	public function __construct( array $data ) {
		$this->data = $data;
	}


	/**
	 * Get user's unique numeric id.
	 * @return int
	 */
	public function getId() {
		return isset( $this->data['id'] ) ? $this->data['id'] : false;
	}

	/**
	 * Get user's password.
	 * @return string
	 **/
	public function getPassword() {
		return isset( $this->data['password'] ) ?
			$this->data['password'] : '';
	}

	/**
	 * Is this user blocked from logging into the application?
	 * @return bool True if user should not be allowed to log in to the
	 *   application, false otherwise
	 */
	public function isBlocked() {
		return $this->getFlag( 'blocked' );
	}

	/**
	 * Is the user an administrator?
	 * @return bool True if the user is authorized to perform admin tasks,
	 * false otherwise
	 */
	public function isAdmin() {
		return $this->getFlag( 'isadmin' );
	}


	/**
	 * Is the user a reviewer?
	 * @return bool True if the user is authorized to perfom review tasks, false
	 * otherwise
	 */
	public function isReviewer() {
		return $this->getFlag( 'reviewer' );
	}

	protected function getFlag( $flag ) {
		return isset( $this->data[$flag] ) ? (bool)$this->data[$flag] : false;
	}

	/**
	 * @inherit
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}

	/**
	 * @inherit
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * @inherit
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	/**
	 * @inherit
	 */
	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
}
