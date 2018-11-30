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
 *
 * @covers Wikimania\Scholarship\Countries
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class CountriesTest extends \PHPUnit\Framework\TestCase {

	public function testHaveAllCountries() {
		$this->assertCount( 250, Countries::$COUNTRY_NAMES );
	}

	public function testCountryKeysAreIso2Codes() {
		foreach ( Countries::$COUNTRY_NAMES as $key => $value ) {
			$this->assertStringMatchesFormat( '%c%c', $key );
		}
	}

	public function testCountryValuesAreNonEmpty() {
		foreach ( Countries::$COUNTRY_NAMES as $key => $value ) {
			$this->assertStringMatchesFormat( '%s', $value );
		}
	}

} // end CountriesTest
