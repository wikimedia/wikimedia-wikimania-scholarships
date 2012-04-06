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

if ( isset( $_GET['items'] ) )  {
	if ( $_GET['items'] != 'all' ) {
	        $items = intval($_GET['items']);
	} else {
		$items = 'all';
	}
} else { 
	$items = $default_pp;
}

if ( isset( $_GET['p'] ) )  {
        $p = intval($_GET['p']);
} else {
	$p = 0;
}

$dal = new DataAccessLayer();
$params = array(
        'min' => isset( $_GET['min'] ) ? $_GET['min'] : -2,
        'max' => isset( $_GET['max'] ) ? $_GET['max'] : 999,
        'phase' => 2,
	'items' => $items,
	'apps' => $apps,
	'offset' => $p,
	'baseurl' => $BASEURL,
	'page' => 'review/phase2'
);

$schols = $dal->gridData($params);
?>
<?php include TEMPLATEPATH . "header_review.php" ?>
<div id="form-container" class="fourteen columns">
<?php include TEMPLATEPATH . "admin_nav.php" ?>
<ul class="sublinks">
<li><a href="<?php echo $BASEURL; ?>review/phase2?apps=all">All applications</a></li>
<li><a href="<?php echo $BASEURL; ?>review/phase2?apps=unreviewed">All unreviewed</a></li>
<li><a href="<?php echo $BASEURL; ?>review/phase2?apps=myapps">My unreviewed</a></li>
</ul>
<form method="post" action="<?php echo $BASEURL; ?>review/grid">
<table id="grid" class="grid" style="width: 100%;">
	<tr>
		<th style="width: 4%;">id</th>
		<th style="width: 16%;">name</th>
		<th style="width: 28%;">email</th>
		<th style="width: 12%;">residence</th>
		<th style="width: 6%;">sex</th>
		<th style="width: 6%;">age</th>
		<th style="width: 8%;">partial</th>
		<th style="width: 5%;">p1</th>
		<th style="width: 5%;">p2</th>
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
		<td><?= $row['p1score']; ?></td>
		<td><?= $row['nscorers']; ?></td>
		<td>-</td>
	</tr>
	<?php endforeach; ?>
</table>
</form>
<?php
$pager = new Pagination($params, $default_pp);
$pager->render();
?>

<?php include TEMPLATEPATH . "footer_review.php" ?>
