<?php

$submitted = FALSE;
$lang = 'en';
$app = new Application();

function haserror( $field, $app ) {
	if ( in_array( $field, $app->errors ) ) {
		return ' class="fieldWithErrors"';
	} else {
		return '';
	}
}

if (isset($_GET['special'])) {
        $special = TRUE;
}

if (isset($_POST['submit'])) {
	$app->submit($_POST);
	if ($app->success === TRUE) {
		$submitted = TRUE;
	}
}

include( 'header.php' ); 

if (time() < $open_time) {
        echo $wgLang->message('not-open');
} else if ((time() > $close_time) && (!isset($special) ) ) { ?>
<div id="app-closed" class="fourteen columns">
<p><? echo $wgLang->message('deadline-passed'); ?></p>
</div>
<?
} else {
?>

<h2><?php echo $wgLang->message('page-header'); ?></h2>

<?php if ($mock) {?>
<div class="notice">
<?php echo $wgLang->message('mock'); ?>
</div>
<?php }?>
<?php 
if ($app->success) {?>
<h1><?php echo $wgLang->message('confirm-thanks'); ?></h1>
<div id="appresponse">
<?php  echo $wgLang->message('confirm-text'); ?>
</div>
<?
}
?>
<?php 
if ($app->haserrors) {
  echo '<div class="errorbox">';
  echo $wgLang->formHasErrors('form-error');
  echo '</div>';
}
?>
<?php 
$defaults = array(
        'fname' => '',
        'lname' => '',
        'email' => '',
        'telephone' => '',
        'address' => '',
        'residence' => 0,
		'haspassport'=> 0,
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
		'englistAbility' => '',
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
		'wmfAgreeName' => ''
);
$values = array_merge( $defaults, $_POST );
?>
<?php 
if ($submitted != TRUE) { 
?>
<div id="introtext"><?php echo $wgLang->message('text-intro'); ?></div>
<div class="faq"><?php echo $wgLang->message('confirm-faq'); ?></div>
<form action="<?php echo $BASEURL ?>apply" method="post">
<label class="required"><?php echo $wgLang->message('required-field');?></label><br/><br/>
<input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>" />

<!-- contact information start-->
<fieldset>
<legend><?php echo $wgLang->message('contact-info'); ?></legend>
<ul id="form-contact-info" class="appform">
<li <?php echo haserror('fname', $app); ?>><label class='required'><?php echo $wgLang->message('name-first'); ?></label> <input type="text" id="fname" name="fname" <?= isset($values['fname'])?'value="' . $values['fname'] . '"':''; ?> required /></li>
<li <?php echo haserror('lname', $app); ?>><label class='required'><?php echo $wgLang->message('name-last'); ?></label> <input type="text" id="lname" name="lname" <?= isset($values['lname'])?'value="' . $values['lname'] . '"':''; ?> required /></span></li>
<li <?php echo haserror('email', $app); ?>><label class='required'><?php echo $wgLang->message('form-email'); ?></label> <input type="email" id="email" name="email" <?= isset($values['email'])?'value="' . $values['email'] . '"':''; ?> required /></span></li>
<li><?php echo $wgLang->message('form-telephone'); ?> <input type="text" id="telephone" name="telephone"  <?= isset($values['telephone'])?'value="' . $values['telephone'] . '"':''; ?> /></li>
<li><?php echo $wgLang->message('form-mailing-address'); ?><br/><textarea id="address" name="address" cols="40" rows="3" ><?= isset($values['address'])? $values['address']:''; ?></textarea></li> 
<li <?php echo haserror('residence', $app); ?>><label class="required"><?php echo $wgLang->message('form-country-residence'); ?></label>

<select id="residence" name="residence">
    <option><?= $wgLang->message('form-select'); ?></option>
    <?php 
//foreach (range(0, count($COUNTRY_NAMES)-1) as $i)
asort($COUNTRY_NAMES);
foreach ($COUNTRY_NAMES as $key => $val)
    if ($values['residence']==$key) {
        printf('<option value="%d" selected="selected">%s</option>\r\n', $key, $val);
    } else {
        printf('<option value="%d">%s</option>\r\n', $key, $val);
    }
?>
</select>
</li>  
</ul>
</fieldset>
<!-- contact information end-->

<!-- personal information start-->
<fieldset>
<legend><?php echo $wgLang->message('form-personalinfo'); ?></legend>
<ul class="appform">
<li><?php echo $wgLang->message('form-haspassport'); ?> <input type="radio" id="haspassport" name="haspassport" value="1" <?= ($values['haspassport']==1)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-yes'); ?> <input type="radio" id="haspassport" name="haspassport" value="0" <?= ($values['haspassport']==0)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-no'); ?></li> 
<li <?php echo haserror('nationality', $app); ?>><label class="required"><?php echo $wgLang->message('form-nationality'); ?></label>
<select id="nationality" name="nationality" required>
    <option><?= $wgLang->message('form-select'); ?></option>
    <?php 
//foreach (range(0, count($COUNTRY_NAMES)-1) as $i)
asort($COUNTRY_NAMES);
foreach ($COUNTRY_NAMES as $key => $val)
    if ($values['nationality']==$key) {
        printf('<option value="%d" selected="selected">%s</option>\r\n', $key, $val);
    } else {
        printf('<option value="%d">%s</option>\r\n', $key, $val);
    }
?></select>
</li>
<!--<li><?php echo $wgLang->message('form-passport-number'); ?>: <?php echo $wgLang->message('form-passport-explain'); ?><input type="text" id="passportnum" name="passportnum" <?= isset($values['passportnum'])?'value="' . $values['passportnum'] . '"':''; ?> /></li>-->
<li><?php echo $wgLang->message('form-airport'); ?> <input type="text" id="airport" name="airport" <?= isset($values['airport'])?'value="' .$values['airport'] . '"':''; ?> /></li>
<li><?php echo $wgLang->message('form-language'); ?> <input type="text" id="languages" name="languages" <?= isset($values['languages'])?'value="' .$values['languages'] . '"':''; ?> /></li>
<li id="dob"><?php echo $wgLang->message('form-dateofbirth'); ?><br/>
<select id="dd" name="dd">
    <?php foreach (range(1, 31) as $i)
    if ($values['dd']==$i) {
    	printf('<option value="%02d" selected="selected">%d</option>', $i, $i); 
    } else {
    	printf('<option value="%02d">%d</option>', $i, $i);
    }
?></select><select id="mm" name="mm">
    <?php 
    $month_names = $wgLang->message('MONTH_NAMES');
    foreach (range(0, 11) as $i)
    if ($values['mm']==$i+1) {
        printf('<option value="%02d" selected="selected">%s</option>', $i+1, $month_names[$i]);
    } else {
        printf('<option value="%02d">%s</option>', $i+1, $month_names[$i]);
    }
?>
</select><select id="yy" name="yy">
<?php 
$now = intval(strftime( '%Y' ));
$start = $now - 130;
for ( $i = 2013; $i >= $start; $i-- ) {
	print "<option value='$i'>$i</option>";
}
?>
</select>
</li><br/>
<li><?php echo $wgLang->message('form-gender'); ?>
	<select id="sex" name="sex">
		<option value="m" <?= ($values['sex']=='m')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-gender-male'); ?></option>
		<option value="f" <?= ($values['sex']=='f')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-gender-female'); ?></option>
		<option value="t" <?= ($values['sex']=='t')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-gender-transgender'); ?></option>
		<option value="d" <?= ($values['sex']=='d')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-gender-unspecified'); ?></option>
	</select>
</li>
<li><?php echo $wgLang->message('form-occupation'); ?> <input type="text" id="occupation" name="occupation" <?= isset($values['occupation'])?'value="' .$values['occupation'] . '"':''; ?> /></li>
<li><?php echo $wgLang->message('form-study'); ?> <input type="text" id="areaofstudy" name="areaofstudy" <?= isset($values['areaofstudy'])?'value="' .$values['areaofstudy'] . '"':''; ?> /></li>
</ul>
</fieldset>
<!-- personal information end-->

<!-- participation start-->
<fieldset>
<legend><?php echo $wgLang->message('form-participation'); ?></legend>
<ul class="appform">
<li><?php echo $wgLang->message('form-username'); ?> <input type="text" id="username" name="username" <?= isset($values['username'])?'value="' .$values['username'] . '"':''; ?> /></li>
<li><?php echo $wgLang->message('form-primary-project'); ?> <input type="text" id="project" name="project" <?= isset($values['project'])?'value="' .$values['project'] . '"':''; ?> /></li>
<li><?php echo $wgLang->message('form-lang-version'); ?> <input type="text" id="projectlangs" name="projectlangs" <?= isset($values['projectlangs'])?'value="' .$values['projectlangs'] . '"':''; ?> /></li>
<li <?php echo haserror('involvement', $app); ?>><label class='required'>
<?php echo $wgLang->message('form-extent-explain'); ?></label><br />
<textarea id="involvement" name="involvement" cols="80" rows="3" required><?= isset($values['involvement'])?$values['involvement']:''; ?></textarea></li>
<li <?php echo haserror('contribution', $app); ?>><label class='required'>
<?php echo $wgLang->message('form-contrib-explain'); ?></label><br />
<textarea id="contribution" name="contribution" cols="80" rows="3" required><?= isset($values['contribution'])?$values['contribution']:''; ?></textarea></li>
<li <?php echo haserror('englistAbility', $app); ?>><label class='required'>
<?php echo $wgLang->message('form-englistAbility-explain'); ?></label><br />
<textarea id="englistAbility" name="englistAbility" cols="80" rows="3" required><?= isset($values['englistAbility'])?$values['englistAbility']:''; ?></textarea></li>
</ul>
</fieldset>
<!-- participation end-->

<!-- interest start-->
<fieldset>
<legend><?php echo $wgLang->message('form-interest'); ?></legend>
<ul class="appform">
<li><?php echo $wgLang->message('form-attended'); ?>
  <ul class="appform single">
    <li><input type="checkbox" id="wm05" name="wm05" value="1" <?= ($values['wm05']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2005</li>
    <li><input type="checkbox" id="wm06" name="wm06" value="1" <?= ($values['wm06']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2006</li>
    <li><input type="checkbox" id="wm07" name="wm07" value="1" <?= ($values['wm07']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2007</li>
    <li><input type="checkbox" id="wm08" name="wm08" value="1" <?= ($values['wm08']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2008</li>
    <li><input type="checkbox" id="wm09" name="wm09" value="1" <?= ($values['wm09']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2009</li>
    <li><input type="checkbox" id="wm10" name="wm10" value="1" <?= ($values['wm10']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2010</li>
    <li><input type="checkbox" id="wm11" name="wm11" value="1" <?= ($values['wm11']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2011</li>
	<li><input type="checkbox" id="wm12" name="wm12" value="1" <?= ($values['wm12']==1)?'checked = "checked" ':''; ?> style="margin-left: 1em"/> 2012</li>
  </ul>
</li>
<li><input type="checkbox" id="presentation" name="presentation" value="1" <?= ($values['presentation']==1)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-presenter'); ?></li>
<li <?php echo haserror('presentationTopic', $app); ?>><label class='required_present'><?php echo $wgLang->message('form-presentation-topic'); ?> <input type="text" id="presentationTopic" name="presentationTopic" <?= isset($values['presentationTopic'])?'value="' .$values['presentationTopic'] . '"':''; ?> /></label></li>
<li><?php echo $wgLang->message('form-howheard'); ?></li>
<select id="howheard" name="howheard">
<option value="email" <?= ($values['howheard']=='email')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-howheard1'); ?></option>
<option value="project" <?= ($values['howheard']=='project')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-howheard2'); ?></option>
<option value="vp" <?= ($values['howheard']=='vp')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-howheard3'); ?>
</option>
<option value="wom" <?= ($values['howheard']=='wom')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-howheard4'); ?>
</option>
<option value="other" <?= ($values['howheard']=='other')?'selected = "selected" ':''; ?>><?php echo $wgLang->message('form-howheard5'); ?>
</option>
</select></li>
<li <?php echo haserror('why', $app); ?>><label class='required'>
<?php echo $wgLang->message('form-enrichment'); ?></label><br/>
<textarea id="why" name="why" cols="80" rows="3" required><?= isset($values['why'])?$values['why']:''; ?></textarea></li>
<!--<li <?php echo haserror('future', $app); ?>><label class='required'>
<?php echo $wgLang->message('form-future-explain'); ?></label><br />
<textarea id="future" name="future" cols="80" rows="3" required><?= isset($values['future'])?$values['future']:''; ?></textarea></span></li>-->
</ul>
</fieldset>
<!-- interest end-->

<!-- partial scholarship start-->
<fieldset>
<legend><?php echo $wgLang->message('form-partial');?></legend>
<?php echo $wgLang->message('form-partial-explain');?><br />
<ul class="appform">
<li><?php echo $wgLang->message('form-wantspartial'); ?> <input type="radio" id="wantspartial" name="wantspartial" value="1" <?= ($values['wantspartial']==1)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-yes'); ?> <input type="radio" id="wantspartial" name="wantspartial" value="0" <?= ($values['wantspartial']==0)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-no'); ?></li>
<li><input type="checkbox" id="canpaydiff" name="canpaydiff" value="1" <?= ($values['canpaydiff']==1)?'checked = "checked" ':''; ?> /> <?php echo $wgLang->message('form-canpaydiff'); ?></li>
</ul>
</fieldset>
<!-- partial scholarship end-->

<!-- agreement form start-->
<fieldset>
<legend><?php echo $wgLang->message('form-agree'); ?></legend>
<ul class="appform">
<li><input type="checkbox" id="sincere" name="sincere" value="1" <?= ($values['sincere']==1)?'checked = "checked" ':''; ?> /> <?php echo $wgLang->message('form-sincere'); ?></li>
<li><input type="checkbox" id="willgetvisa" name="willgetvisa" value="1" <?= ($values['willgetvisa']==1)?'checked = "checked" ':''; ?> /> <?php echo $wgLang->message('form-visa'); ?></li>
<li><input type="checkbox" id="willpayincidentals" name="willpayincidentals" value="1" <?= ($values['willpayincidentals']==1)?'checked = "checked" ':''; ?> /> <?php echo $wgLang->message('form-incidentals'); ?></li>
<li><input type="checkbox" id="agreestotravelconditions" name="agreestotravelconditions" value="1" <?= ($values['agreestotravelconditions']==1)?'checked = "checked" ':''; ?> /> <?php echo $wgLang->message('form-travel-conditions'); ?></li>
</ul>
</fieldset>
<!-- agreement form end-->

<!-- privacy start-->
<fieldset>
<!-- scholarship committee -->
<legend><?php echo $wgLang->message('form-privacy'); ?></legend>
<p><?php echo $wgLang->message('form-review'); ?></p>
<ul class="appform">
<li <?php echo haserror('chapteragree', $app); ?>><label class='required'>
<input type="radio" id="chapteragree" name="chapteragree" value="1" <?= ($values['chapteragree']==1)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-yes'); ?> 
<input type="radio" id="chapteragree" name="chapteragree" value="0" <?= ($values['chapteragree']==0)?'checked = "checked" ':''; ?> /><?php echo $wgLang->message('form-no'); ?>
&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $wgLang->message('form-chapteragree');?></label>
</li>
</ul>
<!-- WMF -->
<p>
<b>Applicant Rights & Consent to Data Processing and Transfer</b>
<br /><br />
I as an applicant for a Wikimedia Foundation ('WMF') scholarship relating to my participation in the 2013 Wikimania conference hosted in Hong Kong, hereby acknowledge and affirmatively consent to the following as a prerequisite to the consideration of my 2013 Wikimania scholarship application ('Application'):
<br /><br />
(a) Any personal data or sensitive personal data that I submit as part of or in relation to my Application (collectively 'my Data') may be accessed and reviewed by (i) WMF; (ii) official Wikimedia Chapters, a representative list of which can be found at:  <a href='http://meta.wikimedia.org/wiki/Wikimedia_chapters'>http://meta.wikimedia.org/wiki/Wikimedia_chapters</a> ('WM Chapters'); and (iii) members of a scholarship committee made up of Wikimedia movement members, a representative list of which can be found at: 
<a href='https://wikimania2013.wikimedia.org/wiki/Scholarship_committee'>https://wikimania2013.wikimedia.org/wiki/Scholarship_committee</a> ('Scholarship Committee'), for the purposes of evaluating my Application.  
<br /><br />
(b) Any Data that I submit as a part of or in relation to my Application may be collected, stored, used, modified, communicated, archived, destroyed, or otherwise processed (collectively 'Processed' or 'Processing') by WMF, WM Chapters, Wikimedia CH ('WMCH'), and members of the Scholarship Committee.  My Data may be retained by WMF, WM Chapters, and/or WMCH until I request in writing to WMF that my Data be destroyed or for as long as required by applicable law.
<br /><br />
(c) My Data may be transmitted to or from WMF in the United States, WMCH in Switzerland, members of the Scholarship Committee in the country which they reside, and/or WM Chapters in the country which the chapter is based.
<br /><br />
(d) The Data that I have submitted is true and accurate to the best of my knowledge.  However, if any of my Data is inaccurate, I understand that I may request that my Data be corrected by emailing <a href='mailto:wikimania-scholarships@wikimedia.org'>wikimania-scholarships@wikimedia.org</a>.
<br /><br />
(e) I understand that I may ask WMF if my Data is being Processed and may request that WMF provide information about: what of my Data WMF currently holds; the purpose of Processing my Data; the categories of my Processed Data; who the individuals involved in Processing my Data are; and who the individuals who are designated to receive my Data are.  I understand that I can request any of the information enumerated in this section by emailing <a href='mailto:wikimania-scholarships@wikimedia.org'>wikimania-scholarships@wikimedia.org</a>.
</p>
<ul class="appform">
<li <?php echo haserror('form-wmfAgreeName', $app); ?>><label class='required'>
Please enter your full name:</label> <input type="text" id="wmfAgreeName" name="wmfAgreeName" <?= isset($values['wmfAgreeName'])?'value="' .$values['wmfAgreeName'] . '"':''; ?> />
</li>
<li <?php echo haserror('wmfagree', $app); ?>><label class='required'>
<input type="checkbox" id="wmfagree" name="wmfagree" value="1"/><?php echo $wgLang->message('form-wmfagree');?>
</label></li>
</ul>
</fieldset>
<!-- privacy end-->

<input type="submit" id="submit" name="submit" value="<?php echo $wgLang->message('form-submit-app'); ?>" />
</fieldset>
</form>
<?php 
} 

}
?>
</div>
<br clear="all" />
<?php include( 'footer.php' ); ?>
