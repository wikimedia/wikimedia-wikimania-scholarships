<?php
function GetUserTable( $username, $minedits ) { // CB added
	$ret = file_get_contents( 'http://toolserver.org/~vvv/sulutil.php?user=' . $uname . '&editcount=' . $minedits );
	var_dump( $ret );
	return $ret;
}

function GetStaggeredUserData( $username ) {
	if ( $username != "" ) {
		$ret = GetUserTable( $username, 50 );
		if ( $ret == "<strong class='error'>Returned no results</strong>" ) {
			$ret = GetUserTable( $username, 10 );
			if ( $ret == "<strong class='error'>Returned no results</strong>" ) {
				$ret = GetUserTable( $username, 0 );
			}
		}
	} else {
		$ret = "No username listed";
	}
	return $ret;
}

$uname = $_GET['user'];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us">
<head>
	<meta http-equiv="Content-language" content="en-us"/>
	<meta http-equiv="Content-type" content="application/xhtml+xml; charset=utf-8"/>
	<title>Wikimania 2012 Scholarships</title>
	<link rel="stylesheet" type="text/css" href="/global.css"/>
	<link rel="stylesheet" type="text/css" href="/attendee.css"/>
	<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
</head>
<body style="font-size:0.85em">
				<?= GetStaggeredUserData( $uname ); ?>
</body>
</html>
