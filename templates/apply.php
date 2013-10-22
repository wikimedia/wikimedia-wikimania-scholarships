<?php
$submitted = false;
$lang = 'en';
$app = new Application();

if ( isset( $_GET['special'] ) ) {
	$special = true;
}

if ( isset( $_POST['submit'] ) ) {
	$app->submit( $_POST );
	if ( $app->success === true ) {
		$submitted = true;
	}
}

include( 'header.php' );

if ( time() < $open_time ) {
	echo $wgLang->message( 'not-open' );
} elseif ( ( time() > $close_time ) && ( !isset( $special ) ) ) {
	echo '<div id="app-closed" class="fourteen columns">
<p>' . $wgLang->message( 'deadline-passed' ) . '</p>
</div>';

} else {

$templateHelper = new TemplateHelper();
$notice = $templateHelper->getNotice( $mock, $app->success, $app->haserrors );

echo $notice;

}

$defaults = array(
        'fname' => '',
        'lname' => '',
        'email' => '',
        'telephone' => '',
        'address' => '',
        'residence' => 0,
		'haspassport' => 0,
		//'passportnum' => '',
		'nationality' => 0,
		'airport' => '',
		'language' => '',
		'yy' => '',
		'sex' => 'd',
		'occupation' => '',
		'areaofstudy' => '',
		'wm05' => 0,
		'wm06' => 0,
		'wm07' => 0,
		'wm08' => 0,
		'wm09' => 0,
		'wm10' => 0,
		'wm11' => 0,
		'wm12' => 0,
		'presentation' => 0,
		'howheard' => '',
		'why' => '',
		//'future' => '',
		'englishability' => '',
		'username' => '',
		'project' => '',
		'projectlangs' => '',
		'involvement' => '',
		'contribution' => '',
		'wantspartial' => 0,
		'canpaydiff' => 0,
		'sincere' => 0,
		'willgetvisa' => 0,
		'willpayincidentals' => 0,
		'agreestotravelconditions' => 0,
		'mm' => 1,
		'dd' => 1,
		'chapteragree' => 1,
		'presentationTopic' => '',
		'wmfAgreeName' => '',
		'wmfagree' => 0
);

$values = array_merge( $defaults, $_POST );

if ( $submitted != true ) {

	$formHeader = '
<div id="introtext">' . $wgLang->message('text-intro') . '</div>
<div class="faq">' . $wgLang->message('confirm-faq') . '</div>
<form action="' . $BASEURL . 'apply" method="post">
<label class="required">' . $wgLang->message('required-field') . '</label><br/><br/>
<input type="hidden" name="lang" id="lang" value="$lang" />';

	echo $formHeader;

	$contact = '<!-- contact information start-->
<fieldset>
<legend>' . $wgLang->message('contact-info') . '</legend>
<ul id="form-contact-info" class="appform">';

	$templateHelper = new TemplateHelper();

	$contact .= $templateHelper->getField( $app, 'name-first', 'fname', $values['fname'], true );
	$contact .= $templateHelper->getField( $app, 'name-last', 'lname', $values['lname'], true );
	$contact .= $templateHelper->getField( $app, 'form-email', 'email', $values['email'], true );
	$contact .= $templateHelper->getField( $app, 'form-telephone', 'telephone', $values['telephone'] );
	$contact .= $templateHelper->getTextArea( $app, 'form-mailing-address', 'address', $values['address'] );

	$contact .= $templateHelper->getResidenceHtml( $app, $COUNTRY_NAMES, $values['residence'], true );

	$contact .= '</ul>
</fieldset>
<!-- contact information end-->';

	echo $contact;

	$personal = '
<!-- personal information start-->
<fieldset>
<legend>' . $wgLang->message('form-personalinfo') . '</legend>
<ul class="appform">';

	$personal .= $templateHelper->getHasPassport( $values['haspassport'] );
	$personal .= $templateHelper->getNationality( $app, $COUNTRY_NAMES, $values['nationality'], true );
	$personal .= $templateHelper->getField( $app, 'form-airport', 'airport', $values['airport'] );
	$personal .= $templateHelper->getField( $app, 'form-language', 'languages',
		isset( $values['languages'] ) ? $values['languages'] : null );

	$personal .= $templateHelper->getDateOfBirth( $values['dd'], $values['mm'] ) . '<br/>';
	$personal .= $templateHelper->getGender( $values['sex'] );
	$personal .= $templateHelper->getField( $app, 'form-occupation', 'occupation', $values['occupation'] );
	$personal .= $templateHelper->getField( $app, 'form-study', 'areaofstudy', $values['areaofstudy'] );

	$personal .= '
</ul>
</fieldset>
<!-- personal information end-->';

	echo $personal;

	$participation = '
<!-- participation start-->
<fieldset>
<legend>' . $wgLang->message('form-participation') . '</legend>
<ul class="appform">';

	$participation .= $templateHelper->getField( $app, 'form-username', 'username', $values['username'] );
	$participation .= $templateHelper->getField( $app, 'form-primary-project', 'project', $values['project'] );
	$participation .= $templateHelper->getField( $app, 'form-lang-version', 'projectlangs', $values['projectlangs'] );
	$participation .= $templateHelper->getTextArea( $app, 'form-extent-explain', 'involvement', $values['involvement'], true );
	$participation .= $templateHelper->getTextArea( $app, 'form-contrib-explain', 'contribution', $values['contribution'], true );
	$participation .= $templateHelper->getTextArea( $app, 'form-englishability-explain', 'englishability', $values['englishability'], true );

	$participation .= '
</ul>
</fieldset>
<!-- participation end-->';

	echo $participation;

	$interest = '
<!-- interest start-->
<fieldset>
<legend>' . $wgLang->message('form-interest') . '</legend>
<ul class="appform">';

	$years = array( 'wm05', 'wm06', 'wm07', 'wm08', 'wm09', 'wm10', 'wm11', 'wm12' );

	$attended = array();

	foreach( $years as $year ) {
		$attended[$year] = $values[$year] == 1;
	}

	$interest .= $templateHelper->getYearsAttended( $attended );
	$interest .= $templateHelper->getCheckbox( 'form-presenter', 'presentation', $values['presentation'] );
	$interest .= $templateHelper->getField( $app, 'form-presentation-topic', 'presentationTopic', $values['presentationTopic'], false );
	$interest .= $templateHelper->getHowHeard( $values['howheard'] );
	$interest .= $templateHelper->getTextArea( $app, 'form-enrichment', 'why', $values['why'], true );

	$interest .= '
	</ul>
</fieldset>
<!-- interest end-->';

	echo $interest;

	$partial = '
<!-- partial scholarship start-->
<fieldset>
<legend>' . $wgLang->message( 'form-partial' ) . '</legend>
' . $wgLang->message( 'form-partial-explain' ) . '<br />
<ul class="appform">';

	$partial .= $templateHelper->getPartial( $values['wantspartial'], $values['canpaydiff'] );
	$partial .= '
</ul>
</fieldset>
<!-- partial scholarship end-->';

	echo $partial;

	$agreement = '
<!-- agreement form start-->
<fieldset>
<legend>' . $wgLang->message( 'form-agree' ) . '</legend>
<ul class="appform">';

	$agreement .= $templateHelper->getCheckbox( 'form-sincere', 'sincere', $values['sincere'] );
	$agreement .= $templateHelper->getCheckbox( 'form-visa', 'willgetvisa', $values['willgetvisa'] );
	$agreement .= $templateHelper->getCheckbox( 'form-incidentals', 'willpayincidentals', $values['willpayincidentals'] );
	$agreement .= $templateHelper->getCheckbox( 'form-travel-conditions', 'agreestotravelconditions', $values['agreestotravelconditions'] );

	$agreement .= '</ul>
</fieldset>
<!-- agreement form end-->';

	echo $agreement;

	$privacy = '
<!-- privacy start-->
<fieldset>
<!-- scholarship committee -->
<legend>' . $wgLang->message( 'form-privacy' )  . '</legend>
<p>' . $wgLang->message( 'form-review' ) . '</p>
<ul class="appform">';

	$privacy .= $templateHelper->getChaptersAgree( $app, $values['chapteragree'] );
	$privacy .= '
</ul>';

	echo $privacy;

	echo $templateHelper->getRights();

	echo $templateHelper->getField( $app, 'form-wmfAgreeName', 'wmfAgreeName', $values['wmfAgreeName'], true );
	echo $templateHelper->getCheckbox( 'form-wmfagree', 'wmfagree', $values['wmfagree'] );

	echo '
</ul>
</fieldset>
<!-- privacy end-->';

	echo '
<input type="submit" id="submit" name="submit" value="' . $wgLang->message('form-submit-app') . '" />
</fieldset>
</form>';

}

echo '
</div>
<br clear="all" />';

include( 'footer.php' );
