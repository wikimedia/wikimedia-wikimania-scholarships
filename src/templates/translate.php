<?php

include( 'header.php' );

echo '
	<div id="translate">
	<h2>' . $wgLang->message( 'help-translate' ) . '</h2>'
	. $wgLang->message( 'translate-page' ) . '
	</div>';

include( 'footer.php' );
