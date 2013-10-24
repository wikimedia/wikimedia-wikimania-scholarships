<?php
	function YearsOld($date) {
		$born = preg_replace('/^([^-]*).*$/', '\1', $date);
		return 2011 - $born;
	}

	function Sex($sex) {
		switch ($sex) {
			case 'm':
				return 'male';
			case 'f':
				return 'female';
			default:
				return 'declined to state';
		}
	}

	if (!isset($_SESSION['user_id']))
	{
		header('location: ' . $BASEURL . '/user/login');
		exit();
	}

	$user_id = $_SESSION['user_id'];

	if ($_GET['id'])
		$id = $_GET['id'];
	else if ($_POST['id'])
		$id = $_POST['id'];
	else
		die("No ID supplied!");

	$dal = new DataAccessLayer();
	$username = $dal->GetUsername($_SESSION['user_id']);

	if (isset($_POST['up'])) {
		$dal->InsertOrUpdateRanking($user_id, $_POST['last_id'], 'valid', 1);
	} else if (isset($_POST['down'])) {
		$dal->InsertOrUpdateRanking($user_id, $_POST['last_id'], 'valid', -1);
	} else if (isset($_POST['skip'])) {
		$dal->InsertOrUpdateRanking($user_id, $_POST['last_id'], 'valid',  0);
	} else if (isset($_POST['save'])) {
		$dal->UpdateField('notes', $_POST['last_id'], $_POST['notes']);
		$dal->UpdateField('residence', $_POST['last_id'], $_POST['residence']);
		$dal->UpdateField('nationality', $_POST['last_id'], $_POST['nationality']);
		$dal->UpdateField('exclude', $_POST['last_id'], $_POST['exclude']);
		$dal->UpdateField('address', $_POST['last_id'], $_POST['address']);
	}

	if (isset($_POST['save']))
		$schol = $dal->GetScholarship($_POST['last_id']);
	else $schol = ($id == 'unranked')
		? $dal->GetNextPhase1($user_id)
		: $dal->GetScholarship($id);
	$rankings = $dal->GetPhase1Rankings($schol['id']);

?>
<?php include "templates/header_review.php" ?>
	<script type="text/javascript">
		function toggleDump() {
			var dump = document.getElementById('dump');

			if (dump.style.display == 'block')
				dump.style.display = 'none';
			else
				dump.style.display = 'block';
		}

		function insertStamp() {
			notes = document.getElementById('notes');
			now = new Date();
			year = now.getUTCFullYear();
			month = now.getUTCMonth() + 1;
			day = now.getUTCDate();
			hours = now.getUTCHours();
			minutes = now.getUTCMinutes();
			notes.value = month + '/' + day + ' '
				+ hours + ':' + (minutes < 10 ? '0' + minutes : minutes)
				+ ' ' + "<?= $username['username'] ?>"
				+ ": \n\n" + notes.value;
		}
	</script>

	<form method="post" action="edit.php">
	<h2>Edit application</h2>
<?php include "templates/admin_nav.php" ?>
	<div style="position: fixed; top: 20%; left: 3%">
	<p><input type="submit" id="up" name="up" value="+" style="color: green; font-size: 300%; width: 50px; height: 50px" /><br/>
	<input type="submit" id="skip" name="skip" value="Skip" style="width: 50px; height: 50px" /><br/>
	<input type="submit" id="down" name="down" value="-" style="color: red; font-size: 300%; width: 50px; height: 50px" /></p>
	</div>

	<div id="application-edit">

	<fieldset>
        <p><span style="font-size: 200%"><?= $schol['fname'] . ' ' . $schol['lname'] ?></span><br/><span style="font-size: 125%;">User:<a href="http://toolserver.org/~vvv/sulutil.php?user=<?= $schol['username'] ?>" target="_blank" style="color:#000088;"><?= $schol['username'] ?></a></span> (Click to view cross-wiki contributions)</p>

<div id="rightcol">
        <div id="notes-box">
        <ul>
        <li><input type="button" id="stamp" name="stamp" value="Insert stamp" onclick="insertStamp();" /></li>
        <li><textarea id="notes" name="notes" style="width: 30em; height: 15ex"><?= $schol['notes']; ?></textarea></li>
        </ul>

<div id="agree-box">
<?php if (($schol['sincere']==0)||($schol['agreestotravelconditions']==0)||($schol['willgetvisa']==0)||($schol['willpayincidentals']==0)) {  // non-editable Answerbox  ?>
<ul id="terms-agree">
<?php if ($schol['sincere']==0) { ?>
<li>Did not agree that they understood application.</li>
<?php } ?> <?php if ($schol['agreestotravelconditions']==0) { ?>
<li>Did not agree to travel conditions.</li>
<?php } ?> <?php if ($schol['willgetvisa']==0) { ?>
<li>Did not agree to get own visa.</li>
<?php } ?> <?php if ($schol['willpayincidentals']==0) { ?>
<li>Did not agree to pay incidentals</li>
<?php } ?>
</ul>
<?php } ?>
</div>
        </div>

	<ul id="countryinfo" class="appview">
        <li>Address: <textarea id="address" name="address"><?= $schol['address']?></textarea></li>
        <li>Residence: <select id="residence" name="residence">
        <option>Select one:</option>
        <?php foreach (range(0, count($COUNTRY_NAMES)-1) as $i)
        if ($schol['residence']==$i) {
                printf('<option value="%d" selected="selected">%s</option>', $i, $COUNTRY_NAMES[$i]);
            } else {
                printf('<option value="%d">%s</option>', $i, $COUNTRY_NAMES[$i]);
            }
        ?></select></li>
	<li>Citizenship: <select id="nationality" name="nationality">
        <option>Select one:</option>
        <?php foreach (range(0, count($COUNTRY_NAMES)-1) as $i)
        if ($schol['nationality']==$i) {
                printf('<option value="%d" selected="selected">%s</option>', $i, $COUNTRY_NAMES[$i]);
            } else {
                printf('<option value="%d">%s</option>', $i, $COUNTRY_NAMES[$i]);
            }
        ?></select></li>
	</ul>

        <ul id="contactinfo" class="appview">
	<li>Email: <a href="mailto:<?= $schol['email'] ?>"><?= $schol['email'] ?></a></li>
	<li>Phone: <?= $schol['telephone'] ?></li>
	</ul>

	<ul id="ageinfo" class="appview">
        <li>Date of birth: <?= $schol['dob'] ?> (~<?= YearsOld($schol['dob']) ?> years old)</li>
	</ul>

	<ul id="genderinfo" class="appview">
	<li>Sex: <?= Sex($schol['sex']) ?></li>
	</ul>

	<ul id="lang-job-info" class="appview">
        <li>Speaks <?= $schol['languages'] ?></li>
        <?php if (strlen($schol['occupation']) > 0): ?>
        <li>Occupation: <?= $schol['occupation'] ?></li>
        <?php endif; ?>
        <?php if (strlen($schol['areaofstudy']) > 0): ?>
        <li>Area of study: <?= $schol['areaofstudy'] ?></li>
        <?php endif; ?>
        <?php if (strlen($schol['occupation']) == 0 && strlen($schol['areaofstudy']) == 0): ?>
        <li>Did not give an occupation or area of study.</li>
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
        </tr>
        <tr>
                <td><?= $schol['wm05'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm06'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm07'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm08'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm09'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm10'] == 1 ? 'X' : '&nbsp;' ?></td>
                <td><?= $schol['wm11'] == 1 ? 'X' : '&nbsp;' ?></td>
        </tr>
</table>

        <p>Exclude this application? <input type="checkbox" id="exclude" name="exclude" value="1" <?= $schol['exclude']==1?'checked="checked" ':''; ?>/>  
	<input type="submit" id="save" name="save" value="Save"/>
	</fieldset>
	</div>

	<fieldset>
	<legend>Why do you want to attend?</legend>
	<?php if (strlen($schol['why']) > 0): ?>
	<p><?= $schol['why'] ?></p>
	<?php else: ?>
	<p>Declined to state.</p>
	<?php endif; ?>
	</fieldset>
	
	<fieldset>
	<legend>How will Wikimania affect your future involvement?</legend>
	<?php if (strlen($schol['future']) > 0): ?>
	<p><?= $schol['future'] ?></p>
	<?php else: ?>
	<p>Declined to state.</p>
	<?php endif; ?>
	</fieldset>

	<fieldset>
	<legend>What is your involvement?</legend>
	<?php if (strlen($schol['involvement']) > 0): ?>
	<p><?= $schol['involvement'] ?></p>
	<?php else: ?>
	<p>Declined to state.</p>
	<?php endif; ?>
	</fieldset>

	<fieldset>
	<legend>What contributions have you made to free knowledge and free software?</legend>
	<?php if (strlen($schol['contribution']) > 0): ?>
	<p><?= $schol['contribution'] ?></p>
	<?php else: ?>
	<p>Declined to state.</p>
	<?php endif; ?>
	</fieldset>

	<fieldset>
	<legend><a href="#" onClick="toggleDump();">Show full dump</a></legend>
	<div id="dump">
	<?php foreach ($schol as $k => $v): ?>
	<p><?= $k ?>: <?= $v ?></p>
	<?php endforeach; ?>
	</div>
	</fieldset>

	<fieldset>
	<legend>Rankings</legend>
	<?php if (count($rankings) > 0): foreach ($rankings as $r): ?>
	<p><?= $r['username'] ?> voted <?= sprintf('%+d', $r['rank']) ?></p>
	<?php endforeach; else: ?>
	<p>This application has not been ranked.</p>
	<?php endif; ?>
	</fieldset>

	<input type="hidden" id="id" name="id" value="<?= $id ?>"/>
	<input type="hidden" id="last_id" name="last_id" value="<?= $schol['id'] ?>"/>
	</form>
	</div>
<?php include "templates/footer_review.php";
