<?php
if ( !isset( $_SESSION['user_id'] ) ) {
	header( 'location: ' . $BASEURL . 'user/login' );
	exit();
}

$dal = new DataAccessLayer();
$data = $dal->export();

header( "Content-Type: text/plain" );

foreach ( $data as $row ) {
	str_replace( ',', ' ', $row );
	str_replace( '"', ' ', $row );
	print( '"' . join( $row, '","' ) . '"' . "\n" );
}
