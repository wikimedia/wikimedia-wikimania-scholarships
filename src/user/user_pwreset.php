<?php
if ( !isset( $_SESSION['user_id'] ) ) {
	header( 'location: ' . $BASEURL . 'user/login' );
	exit();
}

$user_id = $_SESSION['user_id'];

if ( $_GET['id'] )
	$id = $_GET['id'];
else if ( $_POST['id'] )
	$id = $_POST['id'];

$userDao = new User();
$username = $userDao->GetUsername( $_SESSION['user_id'] );
$isadmin = $userDao->IsSysAdmin( $_SESSION['user_id'] );

if ( isset( $_POST['save'] ) ) {
	if ( $_POST['force'] == 1 ) {
		$reset = $userDao->UpdatePassword( NULL, $_POST['newpw1'], $_POST['id'], 1 );
	} else {
		if ( $_POST['newpw1'] == $_POST['newpw2'] ) {
			$reset = $userDao->UpdatePassword( $_POST['oldpw'], $_POST['newpw1'], $_POST['id'] );
			if ( $reset == 0 ) {
				$error = 1;
				$errmsg = "Old password incorrect.";
			}
		} else {
			$error = 1;
			$errmsg = "New passwords do not match.";
		}
	}

}

if ( ( $isadmin == 1 ) && ( isset( $_GET['id'] ) ) ) {
	$user = $userDao->GetUserInfo( $id );
	$forceset = 1;
} else {
	$user = $userDao->GetUserInfo( $user_id );
}

?>
<?php include "templates/header_review.php" ?>

	<form method="post" action="<?php echo $BASEURL; ?>user/password/reset">
	<h1>View User Info</h1>
<?php include "templates/admin_nav.php" ?>

	<fieldset>
	<input type="hidden" name="id" id="id" value="<?= $user['id'] ?>" />
	<p><span style="font-size:150%">Username: <strong><?= $user['username'] ?></strong></span></p>
	<?php if ( $reset != 1 ) {
		if ( $forceset != 1 ) { ?>
	<p>Old Password: <input type="password" name="oldpw" id="oldpw" value="" /></p>
	<p>New Password: <input type="password" name="newpw1" id="newpw1" value="" /></p>
	<p>New Password (again): <input type="password" name="newpw2" id="newpw2" value="" /></p>
	<p><?= $errmsg ?></p>
	<?php } else { ?>
	<input type="hidden" name="force" id="force" value="1" />
	<p>New Password: <input type="password" name="newpw1" id="newpw1" value="" /></p>
	<?php  }
	} else { ?>
	<p> Password reset successful! </p>
	<?php } ?>
	</fieldset>
	<input type="submit" id="save" name="save" value="Save" style="width: 10em" />

	</form>
	<?php include "templates/footer.php";
