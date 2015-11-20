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

/**
 * Manage authentication and authorization.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class AuthManager extends \Wikimedia\Slimapp\Auth\AuthManager {

	/**
	 * Is the user an administrator?
	 * @return bool True if the user is authorized to perform admin tasks,
	 * false otherwise
	 */
	public function isAdmin() {
		$user = $this->getUserData();
		return $user ? $user->isAdmin() : false;
	}


	/**
	 * Is the user a reviewer?
	 * @return bool True if the user is authorized to perform review tasks,
	 * false otherwise
	 */
	public function isReviewer() {
		$user = $this->getUserData();
		return $user ? $user->isReviewer() : false;
	}
}
