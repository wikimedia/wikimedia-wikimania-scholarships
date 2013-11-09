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

namespace Wikimania\Scholarship;

/**
 * Configuration registry.
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Config {

	/**
	 * @var array $settings
	 */
	protected $settings;

	protected function __construct() {

		$this->settings = array(
			'mock' => self::getBool( 'MOCK' ),
			'application_open' => self::getDate( 'APPLICATION_OPEN' ),
			'application_close' => self::getDate( 'APPLICATION_CLOSE' ),
			'db_dsn' => self::getStr( 'DB_DSN' ),
			'db_user' => self::getStr( 'DB_USER' ),
			'db_pass' => self::getStr( 'DB_PASS' ),
		);
	}


	/**
	 * Get a configuration setting.
	 * @param string $name Setting
	 * @return mixed Configuration value
	 */
	public static function get( $name ) {
		static $conf;
		if ( $conf === null ) {
			$conf = new self();
		}
		return $conf->getSetting( $name );
	}


	protected function getSetting ( $name ) {
		if ( array_key_exists( $name, $this->settings ) ) {
			return $this->settings[$name];
		}
		return null;
	}


	protected static function getBool( $name ) {
		$var = getenv( $name );
		return filter_var( $var, \FILTER_VALIDATE_BOOLEAN  );
	}


	protected static function getStr( $name ) {
		$var = getenv( $name );
		return filter_var( $var,
			\FILTER_SANITIZE_STRING,
			\FILTER_FLAG_STRIP_LOW | \FILTER_FLAG_STRIP_HIGH
		);
	}


	protected static function getDate( $name ) {
		return strtotime( self::getStr( $name ) );
	}

} //end Config
