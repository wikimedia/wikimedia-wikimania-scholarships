<?php
$lang = null;
if ( ( isset( $_GET['uselang'] ) ) or ( isset( $values['uselang'] ) ) ) {
	$res = array_merge( $_GET, $_POST );
	$lang = $wgLang->setLang( $res );
}
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><!--<![endif]-->
<head>
				<meta http-equiv="Content-language" content="<?= $lang ?>"/>
				<meta charset="utf-8"/>
	<title><?php echo $wgLang->message( 'header-title' ); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/base.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/skeleton.css" />
				<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/jquery-ui.css" />
				<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/flexigrid.css" />
				<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/flexigrid.pack.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/review.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE_REVIEW; ?>css/jquery-ui.css" />
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?= $TEMPLATEBASE_REVIEW ?>js/html5.js"></script>
	<![endif]-->
</head>
<body>
<div class="container">
<div id="header" class="clearfix review">
<div id="logo"><h1><a id="banner" href="<?php echo $BASEURL; ?>" title="Wikimania 2013"><img id="wm2013banner" src="<?php echo $TEMPLATEBASE_REVIEW; ?>images/Wikimania-2013-bannerm.png" alt="Wikimania 2013"/></a></h1></div></div>
