<?php
/**
 * Destroy the current user session.
 */

// drop all data in the session
foreach ( $_SESSION as $key => $value ) {
	unset($_SESSION[$key]);
}

// delete the session cookie on the client
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
			$params['path'], $params['domain'],
			$params['secure'], $params['httponly']
		);
}

// destroy local session storage
session_destroy();

// FIXME: should be fully qaulified URL
header('location: ' . $BASEURL);
exit();
