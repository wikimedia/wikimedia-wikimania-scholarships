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

namespace Wikimania\Scholarship\Forms;

use Wikimania\Scholarship\Countries;
use Wikimania\Scholarship\Dao\Apply as ApplyDao;
use Wikimania\Scholarship\Form;
use Wikimania\Scholarship\Wikis;

use DateTime;

/**
 * Collect and validate user input.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Apply extends Form {

	/**
	 * @var object $dao
	 */
	protected $dao;


	public function __construct( $dao = null ) {
		$this->dao = $dao ?: new ApplyDao();
		$validCountries = array_keys( Countries::$COUNTRY_NAMES );
		$validWikis = Wikis::$WIKI_NAMES;

		// Contact information
		$this->expectString( 'fname', array( 'required' => true ) );
		$this->expectString( 'lname', array( 'required' => true ) );
		$this->expectEmail( 'email', array( 'required' => true ) );
		$this->expectInArray( 'residence', $validCountries, array( 'required' => true ) );

		// Personal information
		$this->expectBool( 'haspassport' );
		$this->expectInArray( 'nationality', $validCountries, array( 'required' => true ) );
		$this->expectString( 'airport' );
		$this->expectString( 'languages' );
		$this->expectInt( 'yy', array( 'required' => true ) );
		$this->expectInt( 'mm', array( 'required' => true ) );
		$this->expectInt( 'dd', array( 'required' => true ) );
		$this->expectInArray( 'gender', array( 'm', 'f', 'o', 'd' ),
			array( 'required' => true ) );
		$this->expectString( 'gender_other', array(
			'validate' => array( $this, 'validateGenderOther' ),
		) );
		$this->expectString( 'occupation' );
		$this->expectString( 'areaofstudy' );

		// Participation in the Wikimedia projects
		$this->expectString( 'username' );
		$this->expectString( 'alt_users' );
		$this->expectInArray( 'project', $validWikis );
		$this->expectInArray( 'project2', $validWikis );
		$this->expectInArray( 'project3', $validWikis );
		$this->expectString( 'involvement', array( 'required' => true ) );
		$this->expectString( 'contribution', array( 'required' => true ) );

		// Interest and involvement in Wikimania
		$this->expectBool( 'wm05' );
		$this->expectBool( 'wm06' );
		$this->expectBool( 'wm07' );
		$this->expectBool( 'wm08' );
		$this->expectBool( 'wm09' );
		$this->expectBool( 'wm10' );
		$this->expectBool( 'wm11' );
		$this->expectBool( 'wm12' );
		$this->expectBool( 'wm13' );
		$this->expectBool( 'presentation' );
		$this->expectString( 'presentationTopic', array(
			'validate' => array( $this, 'validatePresentationTopic' ),
		) );
		$this->expectInt( 'howheard' );
		$this->expectString( 'why', array( 'required' => true ) );

		// Application agreement
		$this->expectBool( 'willgetvisa' );
		$this->expectBool( 'willpayincidentals' );
		$this->expectBool( 'agreestotravelconditions' );

		// Privacy
		$this->expectBool( 'chapteragree' );
		$this->expectTrue( 'wmfagree' );
		$this->expectString( 'wmfAgreeName', array( 'required' => true ) );
		$this->expectString( 'wmfAgreeGuardian', array(
			'validate' => array( $this, 'validateWmfAgreeGuardian' ),
		) );
	}

	/**
	 * Validate that gender_other is provided if gender == 'o'.
	 *
	 * @param mixed $value Value of param
	 * @return bool True if value is valid, false otherwise
	 */
	protected function validateGenderOther ( $value ) {
		return $this->get( 'gender' ) == 'o' ? (bool)$value : true;
	}

	/**
	 * Validate that presentationTopic is provided if presentation is set.
	 *
	 * @param mixed $value Value of param
	 * @return bool True if value is valid, false otherwise
	 */
	protected function validatePresentationTopic ( $value ) {
		return $this->get( 'presentation' ) ? (bool)$value : true;
	}

	/**
	 * Validate that wmfAgreeGuardian is provided if applicant is under 18.
	 *
	 * @param mixed $value Value of param
	 * @return bool True if value is valid, false otherwise
	 */
	protected function validateWmfAgreeGuardian ( $value ) {
		$dob = $this->getDob();
		if ( $dob !== null ) {
			$diff = $dob->diff( new DateTime() );
			return $diff->y < 18 ? (bool)$value : true;
		}
		// Assume things are fine if we have no DOB.
		return true;
	}

	/**
	 * Get the date of birth composite key value.
	 *
	 * @return DateTime|null Timestamp or null if invalid
	 */
	protected function getDob () {
		$year = $this->get( 'yy' );
		$month = $this->get( 'mm' );
		$day = $this->get( 'dd' );

		$result = null;

		if ( $year !== null && $month !== null && $day !== null ) {
			$date = sprintf( '%4d-%02d-%02d', $year, $month, $day );
			$time = strtotime( $date );
			if ( $time < strtotime( 'now' ) &&
				$time > strtotime( '1882-01-01' )
			) {
				$result = new DateTime( "@{$time}" );
				if ( $result->format( 'Y-m-d') != $date ) {
					// Date wasn't really valid (eg 1970-02-31)
					$result = null;
				}
			}
		}

		return $result;
	}

	/**
	 * Validate that the date of birth constructed from the input is a valid
	 * date.
	 */
	protected function customValidationHook () {
		if ( $this->getDob() === null ) {
			$this->errors[] = 'yy';
			$this->errors[] = 'mm';
			$this->errors[] = 'dd';
		}
	}

	/**
	 * Save the collected user input to the database.
	 */
	public function save() {
		$colnames = array(
			'fname', 'lname', 'email', 'residence',

			'haspassport', 'nationality', 'airport', 'languages', 'dob',
			'gender', 'gender_other', 'occupation', 'areaofstudy',

			'username', 'alt_users', 'project', 'project2', 'project3',
			'involvement', 'contribution',

			'wm05', 'wm06', 'wm07', 'wm08', 'wm09', 'wm10', 'wm11', 'wm12', 'wm13',
			'presentation', 'presentationTopic', 'howheard', 'why',

			'willgetvisa', 'willpayincidentals', 'agreestotravelconditions',
			
			'chapteragree', 'wmfAgreeName', 'wmfAgreeGuardian'
		);

		$answers = array();

		foreach ( $colnames as $col ) {
			if ( $col == 'dob' ) {
				$dob = $this->getDob();
				if ( $dob !== null ) {
					$dob = $dob->format( 'Y-m-d' );
				}
				$answers['dob'] = $dob;

			} elseif ( isset( $this->values[$col] ) ) {
				$val = $this->values[$col];
				if ( is_string( $val ) ) {
					$answers[$col] = ( strlen( $val ) > 0 ) ? $val : null;

				} else {
					$answers[$col] = $val;
				}
			}
		}

		$appId = $this->dao->saveApplication( $answers );
		return $appId !== false;
	}
}
