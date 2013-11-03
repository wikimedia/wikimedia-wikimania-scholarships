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
 * Password management utility.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Password {

	/**
	 * Blowfish hashing salt prefix for crypt.
	 * @var string BLOWFISH_PREFIX
	 */
	const BLOWFISH_PREFIX = '$2y$';


	/**
	 * Compare a plain text string to a stored password hash.
	 *
	 * @param string $plainText Password to check
	 * @param string $hash Stored hash to compare with
	 * @return bool True if plain text matches hash, false otherwise
	 */
	public static function comparePasswordToHash( $plainText, $hash ) {
		if ( self::isBlowfishHash( $hash ) ) {
			$check = crypt( $plainText, $hash );

		} else {
			// horrible unsalted md5 that legacy app used for passwords
			$check = md5( $plainText );
		}

		return $check === $hash;
	}


	/**
	 * Encode a password for database storage.
	 *
	 * Do not use the direct output of this function for comparison with stored
	 * values. Modern password hashes use unique salts per encoding and will not
	 * be directly comparable. Use the comparePasswordToHash() function for
	 * validation instead.
	 *
	 * @param string $plainText Password in plain text
	 * @return string Encoded password
	 */
	public static function encodePassword( $plainText ) {
		$salt = self::blowfishSalt();
		return crypt( $plainText, $salt );
	}


	/**
	 * Generate a blowfish salt specification.
	 *
	 * @param int $cost Cost factor
	 * @return string Blowfish salt
	 */
	public static function blowfishSalt( $cost = 8 ) {
		// encoding algorithm from http://www.openwall.com/phpass/
		$itoa = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		if ( $cost < 4 || $cost > 31 ) {
			$cost = 8;
		}
		$random = self::getBytes( 16 );

		$output = self::BLOWFISH_PREFIX;
		$output .= chr( ord( '0' ) + $cost / 10 );
		$output .= chr( ord( '0' ) + $cost % 10 );
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord( $random[$i++] );
			$output .= $itoa[$c1 >> 2];
			$c1 = ( $c1 & 0x03 ) << 4;
			if ( $i >= 16 ) {
				$output .= $itoa[$c1];
				break;
			}

			$c2 = ord( $random[$i++] );
			$c1 |= $c2 >> 4;
			$output .= $itoa[$c1];
			$c1 = ( $c2 & 0x0f ) << 2;

			$c2 = ord( $random[$i++] );
			$c1 |= $c2 >> 6;
			$output .= $itoa[$c1];
			$output .= $itoa[$c2 & 0x3f];
		} while ( 1 );

		return $output;
	}


	/**
	 * Get N high entropy random bytes.
	 *
	 * @param int $count Number of bytes to generate
	 * @return string String of random bytes
	 */
	public static function getBytes( $count ) {

		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$bytes = openssl_random_pseudo_bytes( $count, $strong );

			if ( $strong && strlen( $bytes ) == $count ) {
				return $bytes;
			}
		} // end if openssl_random_pseudo_bytes

		if ( is_readable( '/dev/urandom' ) ) {
			$fh = @fopen( '/dev/urandom', 'rb' );
			if ( false === $fh ) {
				return false;
			}

			$bytes = fread( $fh, $count );
			fclose( $fh );

			if ( strlen( $bytes ) == $count ) {
				return $bytes;
			}
		} // end if /dev/urandom

		// create a high entropy seed value
		$seed = microtime() . uniqid( '', true );
		if ( function_exists( 'getmypid' ) ) {
			$seed .= getmypid();
		}

		$bytes = '';
		for ( $i = 0; $i < $count; $i += 16 ) {
			$seed = md5( microtime() . $seed );
			$bytes .= pack( 'H*', md5( $seed ) );
		}

		return substr( $bytes, 0, $count );
	}


	/**
	 * Check a salt specification to see if it is a blowfish crypt value.
	 *
	 * @param string $hash Hash to check
	 * @return bool True if blowfish, false otherwise.
	 */
	public static function isBlowfishHash( $hash ) {
		$peek = strlen( self::BLOWFISH_PREFIX );
		return strlen( $hash ) == 60 &&
			substr( $hash, 0, $peek ) == self::BLOWFISH_PREFIX;
	}

	/**
	 * Construction of utility class is not allowed.
	 */
	private function __construct() {
		// no-op
	}

} // end Password
