<?php 
if (isset($_POST['username']) && isset($_POST['password'])) {
	$user = new DataAccessLayer();
	$res = $user->GetUser($_POST['username']);
	if ( ( md5($_POST['password']) == $res['password'] ) AND ( $user->UserIsBlocked() == 0 ) ) {
		session_start();
		$_SESSION['user_id'] = $res['id'];
		header('location: ' . $BASEURL . 'review/phase2');
		exit();
	} else {
		$error = "Invalid credentials.";
	}
}
?>
<?php include "templates/header_review.php" ?>
<div id="form-container" class="fourteen columns">
	<form method="post" action="<?php echo $BASEURL; ?>user/login" >
	<h2>Log in</h2>
	<?php if (isset($error)) print "<p>" . $error . "</p>\n"; ?>
	<fieldset>
	<p>Username: <input type="text" id="username" name="username" /></p>
	<p>Password: <input type="password" id="password" name="password" /></p>
	<p><input type="submit" value="Log in" />
	</fieldset>
	</form>
</div>
<?php include "templates/footer.php";
