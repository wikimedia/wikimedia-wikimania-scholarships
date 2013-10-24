<?php
if ( !isset( $_SESSION['user_id'] ) ) {
	header( 'location: ' . $BASEURL . 'user/login' );
	exit();
}

$phase = 2;
if ( isset( $_GET['phase'] ) && is_numeric( $_GET['phase'] ) ) {
	$phase = $_GET['phase'];
} else if ( isset( $_POST['phase'] ) && is_numeric( $_GET['phase'] ) ) {
	$phase = $_POST['phase'];
}

$dal = new DataAccessLayer();
$user_id = $_SESSION['user_id'];
$username = $dal->GetUsername( $user_id );
$isadmin = $dal->IsSysAdmin( $user_id );

$unreviewed = $dal->myUnreviewed( $user_id, $phase );

$id = min( $unreviewed );
if ( isset( $_GET['id'] ) ) {
	$id = $_GET['id'];
}
if ( $id == '' or $id < 0 ) {
	echo( '<script>alert("please click Phase ' . $phase . ' to return the list")</script>' );
}

$myscorings = $dal->myRankings( $id, $user_id, $phase );
$allscorings = $dal->allRankings( $id, $phase );
$reviewers = $dal->getReviewers( $id, $phase );

if ( isset( $_POST['save'] ) ) {
	$criteria = array( 'valid', 'onwiki', 'offwiki', 'future', 'program', 'englishAbility' );
	$id = isset( $_POST['scholid'] ) ? $_POST['scholid'] : $id;
	foreach ( $criteria as $c ) {
		if ( isset( $_POST[$c] ) ) {
			$dal->InsertOrUpdateRanking( $user_id, $id, $c, $_POST[$c] );
		}
	}
	if ( isset( $_POST['notes'] ) && strlen( $_POST['notes'] ) > 0 ) {
		$dal->UpdateNotes( $id, $_POST['notes'] );
	}

	// $nextid = $dal->getNextId($user_id, $id, $phase);
	// $_SESSION['prev_id'] = $id;
	// header('Location: ' . $BASEURL . 'review/view?id=' . $nextid . '&phase=' . $phase);
	header( 'Location: ' . $BASEURL . 'review/view?id=' . $id . '&phase=' . $phase );
	exit();
}

$skipid = $dal->skipApp( $user_id, $id, $phase );
$schol = $dal->GetScholarship( $id );
?>
<?php include "templates/header_review.php" ?>
<div id="form-container" class="fourteen columns">
<form method="post" action="<?= $BASEURL; ?>review/view?phase=<?= $phase; ?>&id=<?= $id; ?>">
<?php include "templates/admin_nav.php" ?>
<!--score box start-->
<?php if ( $phase > 0 ): ?>
<div id="application-view">
<div id="rank-box" class="clearfix">
<h4>Rankings <span id="rankingstoggle">[show/collapse]</span></h4>
<ul class="sublinks">
<li><a href="<?php echo $BASEURL; ?>review/view?id=<?php echo isset( $_SESSION['previd'] ) ? $_SESSION['previd'] : $id - 1; ?>&phase=<?php echo $phase; ?>">Previous</a></li>
<li><a href="<?php echo $BASEURL; ?>review/view?id=<?php echo $schol['id']; ?>&phase=1">Phase 1</a></li>
<li><a href="<?php echo $BASEURL; ?>review/view?id=<?php echo $schol['id']; ?>&phase=2">Phase 2</a></li>
<li><a href="<?php echo $BASEURL; ?>review/view?id=<?php echo $skipid; ?>&phase=<?php echo $phase; ?>">Next</a></li>
</ul>
<div id="rankingitems">
<table>
<?php if ( $phase == 1 ): ?>
	<tr>
		<td>Valid:</td>
		<td><?= RankDropdownList( 'valid', $schol['id'] ) ?></td>
	</tr>
<?php else : ?>
	<tr>
		<input type="hidden" id="phase" name="phase" value="<?php echo $phase; ?>"/>
		<td>Future promise:
		<td><?= RankDropdownList( 'future', $schol['id'] ) ?></td>
		<td>In Wikimedia movement:</td>
		<td><?= RankDropdownList( 'onwiki', $schol['id'] ) ?></td>
	</tr>
	<tr>
		<td>Outside Wikimedia movement:</td>
		<td><?= RankDropdownList( 'offwiki', $schol['id'] ) ?></td>
		<td>English Ability:</td>
		<td><?= RankDropdownList( 'englishAbility', $schol['id'] ) ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?= RankDropdownList( 'program', $schol['id'] ) ?></td>
	</tr>
<?php endif; ?>
</table>
<div id="notes-box">
Notes:<br/>
<textarea id="notes" name="notes"><?= $schol['notes'] ?></textarea>
<input type="hidden" id="scholid" name="scholid" value='<?php echo $schol['id']; ?>' />
<ul>
<li><input type="submit" id="save" name="save" value="Save" /></li>
</ul>
</div>
</div>
</div>
<?php endif; ?>
<!--score box end-->

<fieldset>
<ul id="view-name" class="appview">
<li><?= $schol['fname'] . ' ' . $schol['lname'] ?></li>
</ul>

<ul id="wikiuserinfo" class="appview">
<?php if ( isset( $schol['username'] ) ): ?>
<li>User: <?= $schol['username'] ?> (<a href="http://toolserver.org/~vvv/sulutil.php?user=<?= $schol['username'] ?>" target="_blank">cross-wiki contribs</a></span>)</li>
<?php else : ?>
<li>User: no username</li>
<?php endif; ?>
</ul>

<ul id="countryinfo" class="appview">
<li>Residence: <?= $schol['residence_name'] ?></li>
<li>Citizenship: <?= $schol['country_name'] ?></li>
</ul>

<ul id="contactinfo" class="appview">
<li>Email: <a href="mailto:<?= $schol['email'] ?>"><?= $schol['email'] ?></a></li>
<li>Phone: <?= $schol['telephone'] ?></li>
</ul>

<ul id="ageinfo" class="appview">
<li>Date of birth:
<?php
if ( ( strtotime( $schol['dob'] ) > strtotime( '1875-01-01' ) ) &&
	( strtotime( $schols['dob'] ) < time() ) ) {
		echo $schol['dob'] . ' (' . YearsOld( $schol['dob'] ) . ' years old)';
	} else {
		echo 'Not specified';
	} ?>
</li>
</ul>

<ul id="genderinfo" class="appview">
<li>Sex: <?= Sex( $schol['sex'] ) ?></li>
</ul>

<ul id="lang-job-info" class="appview">
<li>Speaks <?= $schol['languages'] ?></li>
<?php if ( strlen( $schol['occupation'] ) > 0 ): ?>
<li>Occupation: <?= $schol['occupation'] ?></li>
<?php endif; ?> <?php if ( strlen( $schol['areaofstudy'] ) > 0 ): ?>
<li>Field of study: <?= $schol['areaofstudy'] ?></li>
<?php endif; ?> <?php if ( strlen( $schol['occupation'] ) == 0 && strlen( $schol['areaofstudy'] ) == 0 ): ?>
<li>Did not give an occupation or field of study.</li>
<?php endif; ?>
</ul>

<table id="past-wikimania">
	<tr>
		<th>2005</th>
		<th>2006</th>
		<th>2007</th>
		<th>2008</th>
		<th>2009</th>
		<th>2010</th>
		<th>2011</th>
		<th>2012</th>
	</tr>
	<tr>
		<td><?= $schol['wm05'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm06'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm07'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm08'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm09'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm10'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm11'] == 1 ? 'X' : '&nbsp;' ?></td>
		<td><?= $schol['wm12'] == 1 ? 'X' : '&nbsp;' ?></td>
	</tr>
</table>
<p>2012 Scholarship:
<?php
		if ( $schol['2012scholarship'] == "F" ) {
			echo "Full Scholarship";
		}
		else if ( $schol['2012scholarship'] == "D" ) {
			echo "Declined the full scholarship";
		}
		else if ( $schol['2012scholarship'] == "P" ) {
			echo "Partial Scholarship";
		}
		else {
			echo "NIL";
		}
?>
</p>
<p></p>

<p>
<?php
if ( $schol['presentation'] == 1 ) {
	echo "Will join 'Call for Participation' and Topic is " . $schol['presentationTopic'];
} else {
	echo "Will NOT join 'Call for Participation'";
}
?>
</p>

<?php if ( ( $schol['sincere'] == 0 ) || ( $schol['agreestotravelconditions'] == 0 ) || ( $schol['willgetvisa'] == 0 ) || ( $schol['willpayincidentals'] == 0 ) ) {  // non-editable Answerbox  ?>
<ul id="terms-agree">
<?php if ( $schol['sincere'] == 0 ) { ?>
<li>Did not agree that they understood application.</li>
<?php } ?> <?php if ( $schol['agreestotravelconditions'] == 0 ) { ?>
<li>Did not agree to travel conditions.</li>
<?php } ?> <?php if ( $schol['willgetvisa'] == 0 ) { ?>
<li>Did not agree to get own visa.</li>
<?php } ?> <?php if ( $schol['willpayincidentals'] == 0 ) { ?>
<li>Did not agree to pay incidentals</li>
<?php } ?>
</ul>
<?php } ?>

<p>Partial scholarships: <br/>
<p><?= $schol['wantspartial'] ? 'Wants partial scholarship' : 'Doesn\'t want partial scholarship' ?><br/>
<?= $schol['canpaydiff'] ? 'Can pay the rest of the sum if awarded partial scholarship' : 'Cannot pay the rest of the sum if awarded partial scholarship'?></p>
</fieldset>

<fieldset><legend>Why do you want to attend?</legend> <?php if ( strlen( $schol['why'] ) > 0 ): ?>
<!--<p><?= $schol['why'] ?></p>-->
<p>
<?php
$temp = str_replace( "\n", "<br />", $schol['why'] );
echo $temp;
?>
</p>
<?php else : ?>
<p>Declined to state.</p>
<?php endif; ?></fieldset>

<!--<fieldset><legend>How will Wikimania affect your future involvement?</legend> <?php if ( strlen( $schol['future'] ) > 0 ): ?>
<p><?= $schol['future'] ?></p>
<?php else : ?>
<p>Declined to state.</p>
<?php endif; ?></fieldset>-->

<fieldset><legend>What is your involvement with Wikimedia?</legend> <?php if ( strlen( $schol['involvement'] ) > 0 ): ?>
<!--<p><?= $schol['involvement'] ?></p>-->
<p>
<?php
$temp = str_replace( "\n", "<br />", $schol['involvement'] );
echo $temp;
?>
</p>
<?php else : ?>
<p>Declined to state.</p>
<?php endif; ?></fieldset>

<fieldset><legend>What contribution have you made to free knowledge and free software?</legend>
<?php if ( strlen( $schol['contribution'] ) > 0 ): ?>
<!--<p><?= $schol['contribution'] ?></p>-->
<p>
<?php
$temp = str_replace( "\n", "<br />", $schol['contribution'] );
echo $temp;
?>
</p>
<?php else : ?>
<p>Declined to state.</p>
<?php endif; ?></fieldset>

<fieldset><legend>English Ability <br />(Question: Hong Kong is an international gourmet paradise. Please use less than 200 words to describe your favourite dish of food.)</legend>
<?php if ( strlen( $schol['englistAbility'] ) > 0 ): ?>
<!--<p><?= $schol['englistAbility'] ?></p>-->
<p>
<?php
$temp = str_replace( "\n", "<br />", $schol['englistAbility'] );
echo $temp;
?>
</p>
<?php else : ?>
<p>Declined to state.</p>
<?php endif; ?></fieldset>

<!--<fieldset><legend><a href="#" id="showfulldump">Show full dump</a></legend>
<div id="dump"><?php foreach ( $schol as $k => $v ): ?>
<p><?= $k ?>: <?= $v ?></p>
<?php endforeach; ?></div>
</fieldset>-->
<fieldset><legend>Show full dump</legend>
<?php foreach ( $schol as $k => $v ): ?>
<p><?= $k ?>: <?= $v ?></p>
<?php endforeach; ?>
</fieldset>

<fieldset><legend>Scorings</legend>
<div style="float: left;">
<?php
if ( count( $myscorings ) > 0 ) {
	print "My scorings:<br/><br/>";
	print "<table id='view-scorings'>
		<tr><th>Criteria</th><th>Rank</th></tr>";
	foreach ( $myscorings as $r ) {
		print '<tr><td>' . $r['criterion'] . '</td><td>' . $r['rank'] . '</td></tr>';
	}
	print "</table>";
}
?>
</div>
<div style="float: left; margin-left: 2em;">
<?php
if ( count( $reviewers ) > 0 ) {
	print "Reviewers:<br/><br/>";
	print "<ul>";
	foreach ( $reviewers as $reviewer ) {
		print '<li>' . $reviewer['username'] . '</li>';
	}
	print "</ul>";
}
?>
</fieldset>

<input type="hidden" id="id" name="id" value="<?= $id ?>" /> <input
	type="hidden" id="last_id" name="last_id" value="<?= $schol['id'] ?>" />
</form>
</div>
<?php include "templates/footer_review.php";
