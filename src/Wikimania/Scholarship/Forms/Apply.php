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
use Wikimania\Scholarship\Communities;
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
		$validCommunities = array_keys( Communities::$COMMUNITY_NAMES );
		$validWikis = Wikis::$WIKI_NAMES;

		// Contact information
		$this->requireString( 'fname' );
		$this->requireString( 'lname' );
		$this->requireEmail( 'email' );
		$this->requireInArray( 'residence', $validCountries );

		// Personal information
		$this->expectBool( 'haspassport' );
		$this->requireInArray( 'nationality', $validCountries );
		$this->expectString( 'airport' );
		$this->expectString( 'languages' );
		$this->requireInt( 'yy' );
		$this->requireInt( 'mm' );
		$this->requireInt( 'dd' );
		$this->requireInArray( 'gender', array( 'm', 'f', 'o', 'd' ) );
		$this->expectString( 'gender_other', array(
			'validate' => array( $this, 'validateGenderOther' ),
		) );
		$this->expectString( 'occupation' );
		$this->expectString( 'areaofstudy' );

		// Participation in the Wikimedia projects
		$this->requireString( 'username' );
		$this->expectString( 'alt_users' );
		$this->requireInArray( 'community', $validCommunities );
		$this->requireInArray( 'project', $validWikis );
		$this->expectInArray( 'project2', $validWikis );
		$this->expectBool( 'engage1' );
		$this->expectBool( 'engage2' );
		$this->expectBool( 'engage3' );
		$this->expectBool( 'engage4' );
		$this->expectBool( 'engage5' );
		$this->expectBool( 'engage6' );
		$this->expectBool( 'engage7' );
		$this->expectBool( 'engage8' );
		$this->expectBool( 'engage9' );
		$this->expectBool( 'engage10' );
		$this->requireString( 'involvement' );
		$this->requireString( 'contribution' );

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
		$this->expectBool( 'wm14' );
		$this->requireBool( 'prev_scholar' );
		$this->expectString( 'goals', array(
			'validate' => array( $this, 'validatePrevScholar' )
		) );
		$this->expectBool( 'presentation' );
		$this->expectString( 'presentationTopic', array(
			'validate' => array( $this, 'validatePresentationTopic' ),
		) );
		$this->expectInt( 'howheard' );
		$this->requireString( 'why' );

		// Application agreement
		$this->expectTrue( 'willgetvisa' );
		$this->expectTrue( 'willpayincidentals' );
		$this->expectTrue( 'agreestotravelconditions' );

		// Privacy
		$this->expectBool( 'chapteragree' );
		$this->expectTrue( 'wmfagree' );
		$this->requireString( 'wmfAgreeName' );
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
	 * Validate that goals is provided if prev_scholar is set.
	 *
	 * @param mixed $value Value of param
	 * @return bool True if value is valid, false otherwise
	 */
	protected function validatePrevScholar ( $value ) {
		return $this->get( 'prev_scholar' ) ? (bool)$value : true;
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

		if ( !($this->get( 'engage1' ) ||
			$this->get( 'engage2' ) ||
			$this->get( 'engage3' ) ||
			$this->get( 'engage4' ) ||
			$this->get( 'engage5' ) ||
			$this->get( 'engage6' ) ||
			$this->get( 'engage7' ) ||
			$this->get( 'engage8' ) ||
			$this->get( 'engage9' ) ||
			$this->get( 'engage10' )
		) ) {
			// At least one checkbox must be selected
			$this->errors[] = 'engage10';
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
			'username', 'alt_users', 'project', 'project2', 'community',
			'engage1', 'engage2', 'engage3', 'engage4', 'engage6',
			'engage6', 'engage7', 'engage8', 'engage9', 'engage10',
			'involvement', 'contribution',
			'prev_scholar', 'goals', 'involvement', 'contribution',
			'wm05', 'wm06', 'wm07', 'wm08', 'wm09', 'wm10', 'wm11', 'wm12', 'wm13', 'wm14',
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
