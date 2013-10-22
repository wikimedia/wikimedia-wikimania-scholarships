<?php
if ( ( isset($_GET['uselang']) ) or ( isset( $values['uselang']) ) ) {
	$res = array_merge( $_GET, $_POST );
	$lang = $wgLang->setLang($res);
}
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8 no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="no-js" xml:lang="<?= $lang ?>" lang="<?= $lang ?>"><!--<![endif]-->
<head>
	<meta http-equiv="Content-language" content="<?= $lang ?>"/>
	<meta charset="utf-8"/>
	<title><?php echo $wgLang->message('header-title');?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE; ?>css/base.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE; ?>css/skeleton.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $TEMPLATEBASE; ?>css/style.css"/>
	<link rel="shortcut icon" type="image/png" href="favicon.png"/>
        <!--[if lt IE 9]>
        <script type="text/javascript" src="<?php echo $TEMPLATEBASE; ?>js/html5.js"></script>
        <![endif]-->
</head>
<body>
<div class="container">
<div id="langbar" class="fifteen columns">
<ul class="langlist">
<li><a href="<?php echo $basepath; ?>?uselang=de">de</a></li>
<li><a href="<?php echo $basepath; ?>?uselang=en">en</a></li>
<li><a href="<?php echo $basepath; ?>?uselang=ja">ja</a></li>
<li><a href="<?php echo $basepath; ?>?uselang=pl">pl</a></li>
<li><a href="<?php echo $basepath; ?>?uselang=zh">zh</a></li>
<li class="last"><a href="<?php echo $BASEURL; ?>translate"><?php echo $wgLang->message('help-translate'); ?></a></li>
</ul>
</div>
<h1><a id="banner" href="<?php echo $BASEURL; ?>" title="Wikimania 2013"><img src="<?php echo $TEMPLATEBASE; ?>images/Wikimania-2013-bannerm.png" alt="Wikimania"/></a></h1>
