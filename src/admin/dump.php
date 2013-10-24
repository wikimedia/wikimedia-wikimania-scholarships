<?php
if ( !isset( $_SESSION['user_id'] ) ) {
	header( 'location: ' . $BASEURL . 'user/login' );
	exit();
}

$dal = new DataAccessLayer();
$schols = $dal->GetFinalScoring( 0 );

foreach ( $schols as $row ) {
	print implode( "\t", $row ) . "\n";
}
