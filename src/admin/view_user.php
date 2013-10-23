<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$user_id = $_SESSION['user_id'];

if ($_GET['id'])
$id = $_GET['id'];
else if ($_POST['id'])
$id = $_POST['id'];
else
$id = 'new';

$dal = new DataAccessLayer();
$username = $dal->GetUsername($_SESSION['user_id']);

if (isset($_POST['save'])) {
	$fieldnames = array("username","password","email","reviewer","isvalid","isadmin","blocked");
	foreach ($fieldnames as $i) {
		$answers[$i] = mysql_real_escape_string($_POST[$i]);
	}
	if ($_POST['formstate']=='new') {
		$answers['password'] = md5($_POST['password']);
		$id = $dal->NewUserCreate($answers);
	} else {
		$dal->UpdateUserInfo($answers,$_POST['id']);
	}
}

if ($id!="new")
$user = $dal->GetUserInfo($id);

?>
<?php include "templates/header_review.php" ?>

<form method="post" action="<?php echo $BASEURL; ?>user/view">
<h1>View User Info</h1>
<?php include "templates/admin_nav.php";
 $isadmin =
$dal->IsSysAdmin($_SESSION['user_id']); if ($isadmin == 1) { ?>
<fieldset><input type="hidden" name="id" id="id"
	value="<?= $user['id'] ?>" /> <input type="hidden" name="formstate"
	id="formstate" value="<?= ($id=="new") ? "new" : "" ; ?>" />
<p><span style="font-size: 150%">ID: <strong><?= $user['id'] ?></strong></span></p>

<p>Username: <input type="text" name="username" id="username"
	value="<?= $user['username'] ?>" /></p>
<?php if ($id=="new") { ?>
<p>Password: <input type="password" name="password" id="password"
	value="<?= $user['password'] ?>" /></p>
<?php } ?>
<p>Email: <input type="text" name="email" id="email"
	value="<?= $user['email'] ?>"/ ></p>
<p>Is Reviewer?: <input type="checkbox" name="reviewer" id="reviewer"
	value="1" <?= $user['reviewer']==1?'checked="checked"':''; ?> /></p>
<p>Is Valid?: <input type="checkbox" name="isvalid" id="isvalid"
	value="1" <?= $user['isvalid']==1?'checked="checked"':''; ?> /></p>
<p>Is Admin?: <input type="checkbox" name="isadmin" id="isadmin"
	value="1" <?= $user['isadmin']==1?'checked="checked"':''; ?> /></p>
<p>Blocked?: <input type="checkbox" name="blocked" id="blocked" 
        value="1" <?= $user['blocked']==1?'checked="checked"':''; ?> /></p>
</fieldset>
<input type="submit" id="save" name="save" value="Save" style="width: 10em" />
</form>
<?php } ?>
<?php include "templates/footer.php";
