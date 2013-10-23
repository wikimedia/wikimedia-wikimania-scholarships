<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$apps = 'all';
if( isset( $_GET['apps'] )) {
	if ( in_array( $_GET['apps'], array('unreviewed', 'all', 'myapps')) ) {
		$apps = $_GET['apps'];
	}
}

$items = $default_pp;
$p = 0;

if ( isset( $_GET['items'] ) )  {
	$items = intval($_GET['items']);
}
else
	$items = 30;

if ( isset( $_GET['p'] ) )  {
        $p = intval($_GET['p']);
}

$dal = new DataAccessLayer();
$params = array(
	'min' => isset( $_GET['min'] ) ? $_GET['min'] : -2,
	'max' => isset( $_GET['max'] ) ? $_GET['max'] : 999,
	'phase' => 1,
	'items' => $items,
	'offset' => $p,
	'apps' => $apps,
        'baseurl' => $BASEURL,
	'page' => 'review/phase1'
);

$schols = $dal->gridData($params);
?>
<?php include "templates/header_review.php" ?>
<div id="form-container" class="fourteen columns">
<?php include "templates/admin_nav.php" ?>
<ul class="sublinks">
<li><a href="<?php echo $BASEURL; ?>review/phase1?apps=all">All applications</a></li>
<li><a href="<?php echo $BASEURL; ?>review/phase1?apps=unreviewed">All unreviewed</a></li>
<li><a href="<?php echo $BASEURL; ?>review/phase1?apps=myapps">My unreviewed</a></li>
</ul>
<form method="get" action="<?php echo $BASEURL; ?>review/grid">
<table class="grid" id="grid" style="width: 100%;">
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
		<td><a href="<?php echo $BASEURL; ?>review/view?id=<?= $row['id'] ?>&phase=1"><?= $row['fname'] . ' ' . $row['lname']; ?></a></td>
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
<?php include "templates/footer_review.php";
