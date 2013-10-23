<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}
$dal = new DataAccessLayer();
?>
<?php include "templates/header_review.php" ?>
<h2>Scholarship Applications</h2>
<div id="form-container" class="fourteen columns">
<?php include "templates/admin_nav.php" ?>
<form method="get" action="<?php echo $BASEURL; ?>review/search/results">
<div style="width: 90%; margin: 0 auto;">
<div style='width: 50%; float: left;'>
<ul class='formitems'>
<li><label for='lastname'>Last name</label></li>
<li><input name='last' id='lastname' value='' type='text' /></li>
<li><label for='residence'>Residence</label></li>
<li><input name='residence' id='residence' value='' type='text' /></li>
</ul>
</div>
<div style='width: 50%; float: left;'>
<ul class='formitems'>
<li><label for='firstname'>First name</label></li>
<li><input name='first' id='firstname' value='' type='text' /></li>
<li><label for='region'>Region</label></li>
<li><input name='region' id='region' value='' type='text' /></li>
</ul>
</div>
<br clear="all" />
<div style='text-align: right;'>
<input type='hidden' name='p' value='0' />
<input type='submit' value='Search' />
</div>
</div>
</form>
<br clear="all" />
</div>
<?php include "templates/footer_review.php";
