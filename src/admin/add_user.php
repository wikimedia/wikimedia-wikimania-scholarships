<?php
if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$user_id = $_SESSION['user_id'];

$dal = new DataAccessLayer();
$username = $dal->GetUsername($_SESSION['user_id']);

include 'templates/header_review.php';
include 'templates/admin_nav.php';

if (isset($_POST['save'])) {
	$data = $_POST;
	if ( !isset( $data['isadmin'] ) ) { $data['isadmin'] = 0; }
	if ( !isset( $data['isvalid'] ) ) { $data['isvalid'] = 0; }
	if ( !isset( $data['reviewer'] ) ) { $data['reviewer'] = 0; }
        if ( !isset( $data['email'] ) ) { $data['email'] = ''; }

        if ( ( !isset( $data['password'] ) ) or ( !isset( $data['username'] ) ) ) {
                echo "Empty fields.  Please try again";
	} else {
		$fieldnames = array("username","password","email","reviewer","isvalid","isadmin");
		foreach ($fieldnames as $i) {
			$answers[$i] = mysql_real_escape_string($_POST[$i]);
		}
		$answers['password'] = md5($_POST['password']);
		$res = $dal->NewUserCreate($answers);
		if ( $res === false ) {
			echo "<strong>Error: User already exists</strong>";
		} else {
			echo "<strong>" . $data['username'] . ' added' . "</strong>"; 
	                mail($answers['email'], $wgLang->message('new-account-subject'),
        	         wordwrap( sprintf( $wgLang->message('new-account-email'), $data['username'], $data['password'] ), 72),
 	                "From: Wikimania Scholarships <wikimania-scholarships@wikimedia.org>\r\n" .
        	         "MIME-Version: 1.0\r\n" . "X-Mailer: Wikimania registration system\r\n" .
                	 "Content-type: text/plain; charset=utf-8\r\n" .
	                 "Content-Transfer-Encoding: 8bit");

		}?>
		<form method="post" action="add_user.php">
		<h1>Add another user</h1>
<?
	}
} else {

?>
<form method="post" action="<?php echo $BASEURL; ?>user/add">
<h1>Add new user</h1>
<?
} ?>
<?php 
$isadmin = $dal->IsSysAdmin($_SESSION['user_id']); 
if ($isadmin == 1) { 
$randpass = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$^&*~?/><01234567890123456789',9)),0,9);
?>
<fieldset>
<input type="hidden" name="id" id="id" value="" /> 
<input type="hidden" name="formstate" id="formstate" value="new" />
<p>Username: <input type="text" name="username" id="username" value="" /></p>
<p>Password: <input type="text" name="password" id="password" value="<?php echo $randpass; ?>" /></p>
<p>Email: <input type="text" name="email" id="email" value=""/ ></p>
<p>Is Reviewer?: <input type="checkbox" name="reviewer" id="reviewer" value="1" /></p>
<p>Is Valid?: <input type="checkbox" name="isvalid" id="isvalid" value="1" /></p>
<p>Is Admin?: <input type="checkbox" name="isadmin" id="isadmin" value="1" /></p>
</fieldset>
<input type="submit" id="save" name="save" value="Save" />
</form>
<?php 
} else {
  print "Permission denied for this page.";
}
include 'templates/footer.php';
