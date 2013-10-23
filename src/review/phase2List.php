<?php
require_once('init.php');

session_start();

if (!isset($_SESSION['user_id']))
{
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$partial = $_GET['partial'] ? $_GET['partial'] : 0;
$regionSelected = $_GET['regionSelected'] ? $_GET['regionSelected'] : 'All'; 

$dal = new DataAccessLayer();
$regionList = $dal->GetRegionListNoCount();
array_push($regionList,array('region'=>'All'));

$schols = $dal->GetP2List($partial, $regionSelected);

if($_GET["action"]=="export"){
	$partialName = "";
	if($partial == 0)
		$partialName = "full";
	else{
		if($partial == 1)
			$partialName = "partial";
		else
			$partialName = "all";
	}
	
	header('Content-type: text/download; charset=utf-8');
	header('Content-Disposition: attachment; filename="p2'.$partialName.$regionSelected.'_' . gmdate('ymd_Hi', time() ) . '.csv"');
	echo "id,name,email,residence,sex,age,partial?,# p2 scorers,onwiki,offwiki,future,English Ability,p2 score\n";
	foreach ($schols as $row):
		echo $row['id'].','.$row['fname'] . ' ' . $row['lname'].','.$row['email'].','.$row['country_name'].','.$row['sex'].','.$row['age'].','.
			$row['partial'].','.$row['nscorers'].','.round($row['onwiki'],3).','.round($row['offwiki'],3).','.round($row['future'],3).','.
			round($row['englishAbility'],3).','.round($row['p2score'],4)."\n";
	endforeach;
	return;
}

$ctr=1;
?>
<?php include "templates/header_review.php" ?>
<style>
table, td, th
{
border:1px solid black;
}
</style>

<form method="get" action="<?php echo $BASEURL; ?>review/p2/list">
<h1>Phase 2 List</h1>
<?php include "templates/admin_nav.php" ?>
<select name="partial">
<option value="2" <?php if($partial==2) echo 'selected="selected"'; ?>>all</option>
<option value="1" <?php if($partial==1) echo 'selected="selected"'; ?>>partial</option>
<option value="0" <?php if($partial==0) echo 'selected="selected"'; ?>>full</option>
</select>
<select name="regionSelected">
<?php
	foreach ($regionList as $list):
		echo '<option value="'.$list['region'].'"';
		if($list['region']==$regionSelected )
			echo ' selected="selected"';
		echo '>'.$list['region'].'</option>';
	endforeach;
?>
</select>
<input type="submit" value="Submit">
<?php
$tempRegionSelected = $regionSelected;
$tempRegionSelected=str_replace("&","%26", $tempRegionSelected);
?>
<p><a href="<?php echo $BASEURL; ?>review/p2/list?action=export&partial=<?php echo $partial; ?>&regionSelected=<?php echo $tempRegionSelected; ?>">Click here to export the list</a></p>
<p></p>

<table style="width: 100%" border="1" style="border:1px solid black;">
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
		<td width=25%><a href="../view.php?id=<?= $row['id'] ?>&phase=2" target="_blank"><?= $row['fname'] . ' ' . $row['lname']; ?></a></td>
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
