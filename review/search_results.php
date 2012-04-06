<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$dal = new DataAccessLayer();
$params = array(
        'baseurl' => $BASEURL,
	'phase' => 1,
	'items' => $default_pp,
	'offset' => 0,
	'page' => 'review/search/results'
);

if ( isset( $_GET['items'] ) ) {
	$params['items'] = $_GET['items'];
}

if ( isset( $_GET['p'] ) ) {
	$params['offset'] = $_GET['p'];
}

if ( isset( $_GET['last'] ) ) {
	$params['last'] = $_GET['last'];
}

if ( isset( $_GET['first'] ) ) {
	$params['first'] = $_GET['first'];
}

if ( isset( $_GET['citizen'] ) ) {
	$params['citizen'] = $_GET['citizen'];
}

if ( isset( $_GET['residence'] ) ) {
	$params['residence'] = $_GET['residence'];
}

if ( isset( $_GET['region'] ) ) {
	$params['region'] = $_GET['region'];
}

$schols = $dal->search($params);
?>
<?php include TEMPLATEPATH . "header_review.php" ?>
<h2>Scholarship Applications</h2>
<div id="form-container" class="fourteen columns">
<?php include TEMPLATEPATH . "admin_nav.php" ?>
<form method="get" action="<?php echo $BASEURL; ?>review/search/results">
<table id="grid" class="grid" style="width: 100%;">
	<tr>
		<th style="width: 4%;">id</th>
		<th style="width: 16%;">name</th>
		<th style="width: 28%;">email</th>
		<th style="width: 10%;">residence</th>
		<th style="width: 5%;">sex</th>
		<th style="width: 4%;">age</th>
		<th style="width: 6%;">partial</th>
		<th style="width: 5%;">p1</th>
	</tr>
	<?php foreach ($schols as $row): ?>
	<tr>
		<td><?= $row['id']; ?></td>
		<td><a href="<?php echo $BASEURL; ?>review/view?id=<?= $row['id'] ?>&phase=2"><?= $row['fname'] . ' ' . $row['lname']; ?></a></td>
		<td><?= $row['email']; ?></td>
		<td><?= $row['country_name']; ?></td>
		<td><?= $row['sex']; ?></td>
		<td><?= $row['age']; ?></td>
		<td><?= $row['partial']; ?></td>
		<td><?
if ( $row['p1count'] == 0 ) {
	echo "-";
} else {
	if ( $row['p1score'] == 0 ) {
		echo "0";
	} else {
		echo $row['p1score'];
	}
}
?></td>
	</tr>
	<?php endforeach; ?>
</table>
</form>
<?php
$pager = new Pagination($params, $default_pp);
$pager->render();
?>

</div>
<?php include TEMPLATEPATH . "footer_review.php" ?>
