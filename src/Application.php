<?php

class Application {

	public $haserrors;
	public $numerrors;
	public $errors;
	public $success;

	function __construct() {
		$this->haserrors = FALSE;
		$this->numerrors = 0;
		$this->errors = array();
		$this->success = FALSE;
	}

	function validate( $data ) {
		$this->errors = array();

		if ( isset( $data['submit'] ) ) {
			if ( $data['fname'] == '' && $_POST['lname'] == '' ) {
				if ( $data['fname'] == '' ) {
					array_push( $this->errors, 'fname' );
				}
				if ( $data['lname'] == '' ) {
					array_push( $this->errors, 'lname' );
				}
			}
			if ( $data['why'] == '' ) {
				array_push( $this->errors, 'why' );
			}
			/*if ($data['future'] == '') {
				array_push( $this->errors, 'future' );
			}*/

			if ( $data['involvement'] == '' ) {
				array_push( $this->errors, 'involvement' );
			}
			if ( $data['contribution'] == '' ) {
				array_push( $this->errors, 'contribution' );
			}
			if ( $data['englishability'] == '' ) {
				array_push( $this->errors, 'englishability' );
			}


			if ( $data['residence'] == 0 ) {
				array_push( $this->errors, 'residence' );
			}
			if ( $data['nationality'] == 0 ) {
				array_push( $this->errors, 'nationality' );
			}
			if ( !preg_match( '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $data['email'] ) ) {
				array_push( $this->errors, 'email' );
			}
			if ( isset( $data['presentation'] ) && $data['presentation'] == 1 && $data['presentationTopic'] == '' ) {
				array_push( $this->errors, 'presentationTopic' );
			}

			/*if($data['chapteragree']==0){
				array_push( $this->errors, 'chapteragree' );
			}*/
			if ( !isset( $data['wmfagree'] ) || $data['wmfagree'] != 1 ) {
				array_push( $this->errors, 'wmfagree' );
			}
			if ( $data['wmfAgreeName'] == '' ) {
				array_push( $this->errors, 'wmfAgreeName' );
			}
		}

		$this->numerrors = count( $this->errors );
		if ( $this->numerrors > 0 ) {
			$this->haserrors = TRUE;
		}
		return $this->haserrors;
	}

	function submit( $data ) {
		global $FIELDS, $columns;

		$haserrors = $this->validate( $data );

		if ( ( isset( $data['submit'] ) ) && ( $haserrors === FALSE ) ) {

			// FIXME: hardcoded columns
			// FIXME: does this match all form fields
			$colnames = array(
				"fname", "lname", "email", "telephone", "address", "residence",
				"nationality", "haspassport", "airport", "languages", "sex",
				"occupation", "areaofstudy", "wm05", "wm06", "wm07", "wm08",
				"wm09", "wm10", "wm11", "wm12", "presentation", "howheard",
				"why", "username", "project", "projectlangs", "involvement",
				"englistAbility", "contribution", "sincere", "willgetvisa",
				"willpayincidentals", "agreestotravelconditions", "chapteragree",
				"wantspartial", "canpaydiff", "dob", "rank", "ipaddr",
				"presentationTopic", "wmfAgreeName" );

			foreach ( $colnames as $i ) {
				if ( $i == 'residence' || $i == 'nationality' ) {
					if ( isset( $data[$i] ) && $data[$i] == 'Unspecified' ) {
						$data[$i] = NULL;
					}
				}

				if ( isset( $data[$i] ) || $i == 'dob' ) {
					if ( $i == 'dob' && isset( $data['yy'] ) &&
							 isset( $data['mm'] ) && isset( $data['dd'] ) ) {
						$date = sprintf( "%d-%d-%d", $data['yy'], $data['mm'], $data['dd'] );
						$time = strtotime( $date );
						if ( $time < strtotime( '2013-12-31' ) &&
								 $time > strtotime( '1882-01-01' ) ) {
							$answers['dob'] = $date;
						} else {
							$answers['dob'] = NULL;
						}

					} else {
						$answers[$i] = ( strlen( $data[$i] ) > 0 ) ? $data[$i] : null;
					}
				}
			}

			$answers['rank'] = 1;
			$answers['ipaddr'] = $_SERVER['REMOTE_ADDR'];

			$dao = new Dao();
			//FIXME: error handling
			$appId = $dao->saveApplication( $answers );
			if ( $appId !== false ) {
				$this->emailResponse( $answers );
			}
			$this->success = ( $appId !== false );
		}
	}

	function emailResponse( $answers ) {
		global $wgLang;

		$message = $wgLang->message( 'form-email-response' );
		$message = preg_replace( '/\$1/', $answers['fname'] . ' ' . $answers['lname'], $message );

		mail( $answers['email'],
			$wgLang->message( 'form-email-subject' ),
			wordwrap( $message, 72 ),
			"From: Wikimania Scholarships <wikimania-scholarships@wikimedia.org>\r\n" .
			"MIME-Version: 1.0\r\n" . "X-Mailer: Wikimania registration system\r\n" .
			"Content-type: text/plain; charset=utf-8\r\n" .
			"Content-Transfer-Encoding: 8bit" );
	}

	function isOpen() {
		$close_time = gmmktime( 0, 0, 0, /*Feb*/ 2, /*23rd*/ 23, 2013 );
		if ( time() > $close_time ) {
			if ( $chapters_application ) {
				$COUNTRY_NAMES = $COUNTRY_NAMES_CHAPTERS;
			} else 	{
				return 'done';
			}
		}
	}

}
