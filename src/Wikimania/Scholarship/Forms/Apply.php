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

		$this->expectString( 'fname', array( 'required' => true ) );
		$this->expectString( 'lname', array( 'required' => true ) );
		$this->expectEmail( 'email', array( 'required' => true ) );
		$this->expectString( 'telephone' );
		$this->expectString( 'address' );
		$this->expectInArray( 'residence', $validCountries, array( 'required' => true ) );

		$this->expectBool( 'haspassport' );
		$this->expectInArray( 'nationality', $validCountries, array( 'required' => true ) );
		$this->expectString( 'airport' );
		$this->expectString( 'languages' );
		$this->expectInt( 'yy' );
		$this->expectInt( 'mm' );
		$this->expectInt( 'dd' );
		$this->expectInArray( 'sex', array( 'm', 'f', 't', 'd' ),
			array( 'required' => true ) );
		$this->expectString( 'occupation' );
		$this->expectString( 'areaofstudy' );

		$this->expectString( 'username' );
		$this->expectString( 'project' );
		$this->expectString( 'projectlangs' );
		$this->expectString( 'involvement', array( 'required' => true ) );
		$this->expectString( 'contribution', array( 'required' => true ) );
		$this->expectString( 'englishAbility', array( 'required' => true ) );

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
		));
		$this->expectInt( 'howheard' );
		$this->expectString( 'why', array( 'required' => true ) );

		$this->expectBool( 'wantspartial' );
		$this->expectBool( 'canpaydiff' );

		$this->expectBool( 'sincere' );
		$this->expectBool( 'willgetvisa' );
		$this->expectBool( 'willpayincidentals' );
		$this->expectBool( 'agreestotravelconditions' );

		$this->expectBool( 'chapteragree' );
		$this->expectString( 'wmfAgreeName', array( 'required' => true ) );
		$this->expectTrue( 'wmfagree' );
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
	 * Save the collected user input to the database.
	 */
	public function save() {
		// FIXME: does this match all form fields?
		$colnames = array(
			'fname', 'lname', 'email', 'telephone', 'address', 'residence',

			'haspassport', 'nationality', 'airport', 'languages', 'dob', 'sex',
			'occupation', 'areaofstudy',

			'username', 'project', 'projectlangs', 'involvement', 'contribution',
			'englishAbility',

			'wm05', 'wm06', 'wm07', 'wm08', 'wm09', 'wm10', 'wm11', 'wm12', 'wm13',
			'presentation', 'presentationTopic', 'howheard', 'why',

			'wantspartial', 'canpaydiff',

			'sincere', 'willgetvisa', 'willpayincidentals',
			'agreestotravelconditions',
			
			'chapteragree', 'wmfAgreeName',

			'rank',
		);

		$answers = array();

		foreach ( $colnames as $col ) {
			if ( $col == 'dob' ) {
				if ( isset( $this->values['yy'] ) &&
					isset( $this->values['mm'] ) &&
					isset( $this->values['dd'] )
				) {
					$date = sprintf( '%d-%d-%d',
						$this->values['yy'], $this->values['mm'], $this->values['dd'] );

					$time = strtotime( $date );
					if ( $time < strtotime( 'now' ) &&
						$time > strtotime( '1882-01-01' )
					) {
						$answers['dob'] = $date;

					} else {
						$answers['dob'] = null;
					}
				}

			} elseif ( isset( $this->values[$col] ) ) {
				$val = $this->values[$col];
				if ( is_string( $val ) ) {
					$answers[$col] = ( strlen( $val ) > 0 ) ? $val : null;

				} else {
					$answers[$col] = $val;
				}
			}
		}

		$answers['rank'] = 1;

		$appId = $this->dao->saveApplication( $answers );
		return $appId !== false;
	}
}
