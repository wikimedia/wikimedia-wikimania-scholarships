<?php
/**
 * @section LICENSE
 * This file is part of Wikimania Scholarship Application.
 *
 * Wikimania Scholarship Application is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Wikimania Scholarship Application is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with Wikimania Scholarship Application.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @file
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 * @copyright © 2013 Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
 * @copyright © 2012-2013 Katie Filbert, Wikimania 2012 Washington DC organizing team
 * @copyright © 2011 Harel Cain, Wikimania 2011 Haifa organizing team
 * @copyright © 2010 Wikimania 2010 Gdansk organizing team
 * @copyright © 2009 Wikimania 2009 Buenos Aires organizing team
 */

require_once '../vendor/autoload.php';
require_once '../src/init.php';

session_name( '_s' );
session_start();

$wgLang = new Lang();
// FIXME: make lang sticky via session
$lang = $wgLang->setLang( $_REQUEST );

$router = new Router( $BASEURL, $routes, $defaultRoute );
$path = $router->route();
$basepath = array_search( $path, $routes );

$twigLoader = new Twig_Loader_Filesystem( '../src/tmpl' );
$twig = new Twig_Environment( $twigLoader, array(
	'debug' => true,
	'strict_variables' => true,
	'autoescape' => true,
));

$twigCtx = array(
	'basepath' => $basepath,
	'BASEURL' => $BASEURL,
	'lang' => $lang,
	'TEMPLATEBASE' => $TEMPLATEBASE,
	'wgLang' => $wgLang,
);

//echo $twig->render( 'base.html', $twigCtx );
//die();

include $path;

