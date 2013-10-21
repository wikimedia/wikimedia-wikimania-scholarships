<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id']))
{
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$user_id = $_SESSION['user_id'];
$dal = new DataAccessLayer();

if (isset( $_GET['id']) ) {
	$id = $_GET['id'];
} else {
	$id = $_POST['id'];
}

if (isset($_POST['save'])) {
	$dal->UpdateCountryRank( $id , $_POST['rank'] );
	$id++;
}

$country = $dal->GetCountryInfo($id);

?>
<?php include TEMPLATEPATH . "header_review.php" ?>

<form method="post" action="<?php echo $BASEURL; ?>review/country/edit">
<h1>Edit Country Info</h1>
<?php include TEMPLATEPATH . "admin_nav.php" ?>
<div id="country-grid-return">
<a href="<?php echo $BASEURL; ?>review/country/grid">Return to country grid</a>
</div>
<fieldset><input type="hidden" name="id" id="id"
	value="<?= $country['id'] ?>" />
<p><span style="font-size: 150%">ID: <strong><?= $country['id'] ?></strong></span></p>
<p>Country Name: <strong><?= $country['country_name'] ?></strong></p>
<p>Rank: <input type="text" name="rank" id="rank"
	value="<?= $country['country_rank'] ?>"/ ></p>
</fieldset>
<input type="submit" id="save" name="save" value="Save"
	style="width: 10em" /></form>
<?php include TEMPLATEPATH . "footer_review.php";
