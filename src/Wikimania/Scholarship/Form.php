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
 * Collect and validate user input.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Form {

	/**
	 * Input parameters to expect.
	 * @var array $params
	 */
	protected $params = array();

	/**
	 * Values recieved after filtering.
	 * @var array $values
	 */
	protected $values = array();

	/**
	 * Fields with errors.
	 * @var array $errors
	 */
	protected $errors = array();


	/**
	 * Add an input expectation.
	 *
	 * @var string $name Parameter to expect
	 * @var int $filter Validation filter(s) to apply
	 * @var array $options Validation options
	 * @return Form Self, for message chaining
	 */
	public function expect( $name, $filter, $options = null ) {
		$options = ( is_array( $options ) ) ? $options : array();
		$flags = null;
		$required = false;
		$validate = null;

		if ( isset( $options['flags'] ) ) {
			$flags = $options['flags'];
			unset( $options['flags'] );
		}

		if ( isset( $options['required'] ) ) {
			$required = $options['required'];
			unset( $options['required'] );
		}

		if ( isset( $options['validate'] ) ) {
			$validate = $options['validate'];
			unset( $options['validate'] );
		}

		$this->params[$name] = array(
			'filter'   => $filter,
			'flags'    => $flags,
			'options'  => $options,
			'required' => $required,
			'validate' => $validate,
		);

		return $this;
	}

	public function expectBool( $name, $options = null ) {
		$options = ( is_array( $options ) ) ? $options : array();
		if ( !isset( $options['default'] ) ) {
			$options['default'] = false;
		}
		return $this->expect( $name, \FILTER_VALIDATE_BOOLEAN, $options );
	}

	public function expectTrue( $name, $options = null ) {
		$options = ( is_array( $options ) ) ? $options : array();
		$options['validate'] = function ($v) {
			return (bool)$v;
		};
		return $this->expectBool( $name, $options );
	}

	public function expectEmail( $name, $options = null ) {
		return $this->expect( $name, \FILTER_VALIDATE_EMAIL, $options );
	}

	public function expectFloat( $name, $options = null ) {
		return $this->expect( $name, \FILTER_VALIDATE_FLOAT, $options );
	}

	public function expectInt( $name, $options = null ) {
		return $this->expect( $name, \FILTER_VALIDATE_INT, $options );
	}

	public function expectIp( $name, $options = null ) {
		return $this->expect( $name, \FILTER_VALIDATE_IP, $options );
	}

	public function expectRegex( $name, $re, $options = null ) {
		$options = ( is_array( $options ) ) ? $options : array();
		$options['regexp'] = $re;
		return $this->expect( $name, \FILTER_VALIDATE_REGEXP, $options );
	}

	public function expectUrl( $name, $options = null ) {
		return $this->expect( $name, \FILTER_VALIDATE_URL, $options );
	}

	public function expectString( $name, $options = null ) {
		return $this->expectRegex( $name, '/^.+$/', $options );
	}

	public function expectAnything( $name, $options = null ) {
		return $this->expect( $name, \FILTER_UNSAFE_RAW, $options );
	}

	public function expectInArray( $name, $valids, $options = null ) {
		$options = ( is_array( $options ) ) ? $options : array();
		$options['validate'] = function ($val) use ($valids) {
			return in_array( $val, $valids );
		};
		return $this->expectAnything( $name, $options );
	}

	/**
	 * Validate the provided input data using this form's expectations.
	 *
	 * @param array $vars Input to validate (default $_POST)
	 * @return bool True if input is valid, false otherwise
	 */
	public function validate( $vars = null ) {
		$vars = $vars ?: $_POST;
		$this->values = array();
		$this->errors = array();

		foreach ( $this->params as $name => $opt ) {
			$var = isset( $vars[$name] ) ? $vars[$name] : null;
			$clean = filter_var( $var, $opt['filter'], $opt );

			if ( $clean === false && $opt['filter'] !== \FILTER_VALIDATE_BOOLEAN ) {
				$this->values[$name] = null;

			} else {
				$this->values[$name] = $clean;
			}

			if ( $opt['required'] && $this->values[$name] === null ) {
				$this->errors[] = $name;

			} elseif ( is_callable( $opt['validate'] ) &&
					call_user_func( $opt['validate'], $this->values[$name] ) === false ) {
				$this->errors[] = $name;
				$this->values[$name] = null;
			}
		}

		return count( $this->errors ) === 0;
	}

	public function get( $name ) {
		if ( isset( $this->values[$name] ) ) {
			return $this->values[$name];

		} elseif ( isset( $this->params[$name]['options']['default'] ) ) {
			return $this->params[$name]['options']['default'];

		} else {
			return null;
		}
	}

	public function getValues() {
		return $this->values;
	}

	public function getErrors() {
		return $this->errors;
	}

	public function hasErrors() {
		return count( $this->errors ) !== 0;
	}

}
