<?php

class TemplateHelper {

	public function getNotice( $mock, $success, $haserrors ) {
		global $wgLang;

		$html = '';

		if ( $mock ) {
			$html .= $wgLang->message( 'mock' );
		}

		if ( $success ) {
			$html .= sprintf( '<h1>%s</h1>
				<div id="appresponse">
				%s
				</div>',
				$wgLang->message( 'confirm-thanks' ),
				$wgLang->message( 'confirm-text' )
			);
		}

		if ( $haserrors ) {
			$html .= sprintf( '<div class="errorbox">
				%s
				</div>', $wgLang->formHasErrors( 'form-error' )
			);
		}

		return $html;
	}

	public function getField( $app, $messageKey, $fieldName, $value = null, $required = false ) {
		global $wgLang;

		$html = '<li' . $this->haserror( $fieldName, $app ) . '><label ';

		if ( $required ) {
			$html .= 'class="required"';
		}

		$html .= '>' . $wgLang->message( $messageKey ) . '</label> '
			. '<input type="text" id="' . $fieldName . '" name="' . $fieldName . '" ';

		$html .= $value ? 'value="' . $value . '" ' : ' ';

		if ( $required ) {
			$html .= ' required ';
		}

		$html .= '/></li>';

		return $html;
	}

	public function getResidenceHtml( $app, $countries, $value, $required = false ) {
		global $wgLang;

		$html = '<li ' . $this->haserror( 'residence', $app ) . '><label';

		if ( $required ) {
			$html .= ' class="required"';
		}

		$html .= '>' . $wgLang->message( 'form-country-residence' ) . '</label>';

		$html .= '<select id="residence" name="residence">
			<option>' . $wgLang->message( 'form-select' ) . '</option>';

		asort( $countries );

		foreach ( $countries as $key => $val ) {
			if ( $value == $key ) {
				$html .= sprintf( '<option value="%d" selected="selected">%s</option>\r\n', $key, $val );
			} else {
				$html .= sprintf( '<option value="%d">%s</option>\r\n', $key, $val );
			}
		}

		$html .= '
			</select>
			</li>';

		return $html;
	}

	public function getHasPassport( $hasPassport ) {
		global $wgLang;

		$html = '<li>' . $wgLang->message( 'form-haspassport' ) .
			' <input type="radio" id="haspassport" name="haspassport" value="1" ';

		$html .= $hasPassport == 1 ? 'checked = "checked" ' : '';
		$html .= '/>' . $wgLang->message( 'form-yes' );
		$html .= '<input type="radio" id="haspassport" name="haspassport" value="0" ';
		$html .= $hasPassport == 0 ? 'checked = "checked" ' : '';
		$html .= '/>' . $wgLang->message( 'form-no' ) . '</li>';

		return $html;
	}

	public function getNationality( $app, $countries, $value, $required = false ) {
		global $wgLang;

		$html = '<li ' . $this->haserror( 'nationality', $app ) . '><label';

		if ( $required ) {
			$html .= ' class="required"';
		}

		$html .= '>' . $wgLang->message( 'form-nationality' ) . '</label>';
		$html .= '<select id="nationality" name="nationality" ';

		if ( $required ) {
			$html .= 'required';
		}

		$html .= '>
			<option>' . $wgLang->message( 'form-select' ) . '</option>';

		asort( $countries );

		foreach ( $countries as $key => $val ) {
			if ( $value == $key ) {
				$html .= sprintf( '<option value="%d" selected="selected">%s</option>\r\n', $key, $val );
			} else {
				$html .= sprintf( '<option value="%d">%s</option>\r\n', $key, $val );
			}
		}

		$html .= '</select>
			</li>';

		return $html;
	}

	public function getDateOfBirth( $dd, $mm ) {
		global $wgLang;

		$html = '
			<li id="dob">' . $wgLang->message( 'form-dateofbirth' ) . '<br/>
			<select id="dd" name="dd">';

		foreach ( range( 1, 31 ) as $i ) {
			if ( $dd == $i ) {
				$html .= sprintf( '<option value="%02d" selected="selected">%d</option>', $i, $i );
			} else {
				$html .= sprintf( '<option value="%02d">%d</option>', $i, $i );
			}
		}

		$html .= '</select><select id="mm" name="mm">';

		$month_names = $wgLang->message( 'MONTH_NAMES' );

		foreach ( range( 0, 11 ) as $i ) {
			if ( $mm == $i + 1 ) {
				$html .= sprintf( '<option value="%02d" selected="selected">%s</option>',
					$i + 1, $month_names[$i] );
			} else {
				$html .= sprintf( '<option value="%02d">%s</option>', $i + 1, $month_names[$i] );
			}
		}

		$html .= '</select><select id="yy" name="yy">';

		$now = intval( strftime( '%Y' ) );
		$start = $now - 130;

		for ( $i = 2013; $i >= $start; $i-- ) {
			$html .= "<option value='$i'>$i</option>";
		}

		$html .= '</select>
			</li>';

		return $html;
	}

	public function getGender( $value ) {
		global $wgLang;

		$html = '<li>' . $wgLang->message( 'form-gender' )
			. '<select id="sex" name="sex">
			<option value="m" ';

		$html .= $value == 'm' ? 'selected = "selected" ' : '';
		$html .= '>' . $wgLang->message( 'form-gender-male' ) . '</option>
			<option value="f" ';

		$html .= $value == 'f' ? 'selected = "selected" ' : '';
		$html .= '>' . $wgLang->message( 'form-gender-female' ) . '</option>
			<option value="t" ';
		$html .= $value == 't' ? 'selected = "selected" ' : '';
		$html .= '>' . $wgLang->message( 'form-gender-transgender' ) . '</option>
			<option value="d" ';
		$html .= $value == 'd' ? 'selected = "selected" ' : '';
		$html .= '>' . $wgLang->message( 'form-gender-unspecified' ) . '</option>
			</select>
			</li>';

		return $html;
	}

	public function getTextArea( $app, $messageKey, $field, $value, $required = false ) {
		global $wgLang;

		$html = '<li' . $this->haserror( $field, $app ) . '><label';

		if ( $required ) {
			$html .= " class='required'";
		}

		$html .= '>' . $wgLang->message( $messageKey ) . "</label><br />
			<textarea id='$field' name='$field' cols='80' rows='3'";

		if ( $required ) {
			$html .= ' required';
		}

		$html .= '>';
		$html .= $value ? $value : ' ';
		$html .= '</textarea></li>';

		return $html;
	}

	public function getYearsAttended( $values ) {
		global $wgLang;

		$html = '<li>' . $wgLang->message( 'form-attended' ) . '
			<ul class="appform single">';

		$years = array(
			'wm05' => 2005,
			'wm06' => 2006,
			'wm07' => 2007,
			'wm08' => 2008,
			'wm09' => 2009,
			'wm10' => 2010,
			'wm11' => 2011,
			'wm12' => 2012
		);

		foreach ( $years as $key => $year ) {
			$html .= "
				<li><input type='checkbox' id='$key' name='$key' value='1' ";
			$html .= $values[$key] ? "checked = 'checked' " : '';
			$html .= " style='margin-left: 1em'/> $year</li>";
		}

		$html .= '
			</ul>
			</li>';

		return $html;
	}

	public function getCheckbox( $messageKey, $field, $value ) {
		global $wgLang;

		$html = "
			<li><input type='checkbox' id='$field' name='$field' value='1' ";
		$html .= $value == 1 ? "checked = 'checked' " : '';
		$html .= '/>' . $wgLang->message( $messageKey ) . '</li>';

		return $html;
	}

	public function getHowHeard( $value ) {
		global $wgLang;

		$options = array( 'email', 'project', 'vp', 'wom', 'other' );
		$html = '<select id="howheard" name="howheard">';

		$i = 1;

		foreach ( $options as $option ) {
			$html .= "<option value='$option' ";
			$html .= $value == $option ? 'selected = "selected" ' : '';
			$html .= '>' . $wgLang->message( 'form-howheard' . $i ) . '</option>';

			$i++;
		}

		$html .= '
			</select></li>';

		return $html;
	}

	public function getPartial( $wantsPartial, $canPayDiff ) {
		global $wgLang;

		$html = '
			<li>' . $wgLang->message( 'form-wantspartial' ) . ' <input type="radio" id="wantspartial" '
			. 'name="wantspartial" value="1" ';
		$html .= $wantsPartial == 1 ? 'checked = "checked" ' : '';
		$html .= '/>' . $wgLang->message( 'form-yes' ) .
			' <input type="radio" id="wantspartial" name="wantspartial" value="0" ';
		$html .= $wantsPartial == 0 ? 'checked = "checked" ' : '';
		$html .= '/>' . $wgLang->message( 'form-no' ) . '</li>';
		$html .= '<li><input type="checkbox" id="canpaydiff" name="canpaydiff" value="1" ';
		$html .=  $canPayDiff == 1 ? 'checked = "checked" ' : '';
		$html .= '/> ' . $wgLang->message( 'form-canpaydiff' ) . '</li>';

		return $html;
	}

	public function getChaptersAgree( $app, $value ) {
		global $wgLang;

		$html = '
			<li ' . $this->haserror( 'chapteragree', $app ) . '><label class="required">
			<input type="radio" id="chapteragree" name="chapteragree" value="1" ';
		$html .= $value == 1 ? 'checked = "checked" ' : '';
		$html .= '/>' . $wgLang->message( 'form-yes' ) . '
			<input type="radio" id="chapteragree" name="chapteragree" value="0"';
		$html .= $value == 0 ? 'checked = "checked" ' : '';
		$html .=  '/>' . $wgLang->message( 'form-no' ) . '
			&nbsp;&nbsp;-&nbsp;&nbsp;' . $wgLang->message( 'form-chapteragree' ) . '</label>
			</li>';

		return $html;
	}

	public function getRights() {
		global $wgLang;

		$rights = "
			<!-- WMF -->
			<p>
			<b>" . $wgLang->message( 'form-rights-heading' ) . "</b>
			<br /><br />
			" . $wgLang->message( 'form-rights' ) . "
			</p>
			<ul class='appform'>";

		return $rights;
	}

	protected function haserror( $field, $app ) {
		if ( in_array( $field, $app->errors ) ) {
			return ' class="fieldWithErrors"';
		} else {
			return '';
		}
	}
}
