<?php
if ( !isset( $_SESSION['user_id'] ) ) {
	header( 'location: ' . $BASEURL . 'user/login' );
	exit();
}

$state = ( isset( $_GET['state'] ) ) ? $_GET['state']:'all';

$dal = new Dao();
$users = $dal->GetListofUsers( $state );

$rowstyleeven = 0;
?>
<?php include "templates/header_review.php" ?>
	<form method="post" action="<?php echo $BASEURL; ?>review/grid">
	<h1>Applications</h1>
<?php include "templates/admin_nav.php" ?>
	<table style="width: 100%">
		<tr>
			<th>id</th>
			<th>username</th>
			<th>email</th>
			<?php if ( $state == "reviewer" ) { ?><th>remaining</th><?php
			} else { ?><th>valid?</th><?php } ?>
			</tr>
			<?php foreach ( $users as $row ): ?>
			<tr class="<?php echo ( $rowstyleeven == 1 ) ? "evenrow":"oddrow"; ?>">
			<td><?= $row['id']; ?></td>
			<td><a href="<?php echo $BASEURL; ?>user/view?id=<?= $row['id'] ?>"><?= $row['username']; ?></a></td>
			<td><?= $row['email']; ?></td>
			<?php if ( $state == "reviewer" ) { ?><td><?php $outs = $dal->GetCountAllUnscored( $row['id'] ); echo $outs['COUNT(*)']; ?></td><?php
			} else { ?><td><?= $row['isvalid']; ?></th><?php } ?>
			</tr>
			<?php	$rowstyleeven = ( $rowstyleeven == 1 ) ? 0:1;
endforeach; ?>
	</table>
	</form>
	<?php include "templates/footer.php";
