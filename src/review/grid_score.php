<?php
session_start();

if (!isset($_SESSION['user_id']))
{
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$partial = $_GET['partial'] ? $_GET['partial'] : 0;

$dal = new DataAccessLayer();
$schols = $dal->GetFinalScoring($partial);
?>
<?php include "templates/header_review.php" ?>
<form method="post" action="<?php echo $BASEURL; ?>grid">
<h1>Applications</h1>
<?php include "templates/admin_nav.php" ?>
<table style="width: 100%" border="1">
	<tr>
		<th>counter</th>
		<th>id</th>
		<th>name</th>
		<th>email</th>
		<th>residence</th>
		<th>sex</th>
		<th>age</th>
		<th>partial?</th>
		<th># p2 scorers</th>
		<th>onwiki</th>
		<th>offwiki</th>
		<th>future</th>
		<th>English Ability</th>
		<th>p2 score</th>
	</tr>
	<?php foreach ($schols as $row): ?>
	<tr>
		<td><?= $ctr++; ?></td>
		<td><?= $row['id']; ?></td>
		<td width=25%><a href="../view.php?id=<?= $row['id'] ?>&phase=0" target="_blank"><?= $row['fname'] . ' ' . $row['lname']; ?></a></td>
		<td width=20%><?= $row['email']; ?></td>
		<td width=25%><?= $row['country_name']; ?></td>
		<td width=8%><?= $row['sex']; ?></td>
		<td width=8%><?= $row['age']; ?></td>
		<td width=8%><?= $row['partial']; ?></td>
		<td width=8%><?= $row['nscorers']; ?></td>
		<td width=8%><?= round($row['onwiki'],3); ?></td>
		<td width=8%><?= round($row['offwiki'],3); ?></td>
		<td width=8%><?= round($row['future'],3); ?></td>
		<td width=8%><?= round($row['englishAbility'],3); ?></td>
		<td width=8%><?= round($row['p2score'],4); ?></td>
	</tr>
	<?php endforeach; ?>
</table>
</form>
<?php include "templates/footer_review.php";
