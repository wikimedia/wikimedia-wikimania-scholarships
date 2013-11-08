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

namespace Wikimania\Scholarship;

$APP_ROOT = dirname( dirname( __FILE__ ) );

require_once "{$APP_ROOT}/vendor/autoload.php";
// FIXME: this needs to go
require_once "{$APP_ROOT}/src/init.php";

session_name( '_s' );
session_cache_limiter(false);
session_start();

$app = new \Slim\Slim( array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => "{$APP_ROOT}/data/templates",
) );

$app->wgLang = new Lang( "{$APP_ROOT}/data/i18n" );
// FIXME: make lang sticky via session
$lang = $app->wgLang->setLang( $_REQUEST );

// configure twig views
$view = $app->view();
$view->parserOptions = array(
	// FIXME: configurable
	'debug' => true,
	'strict_variables' => true,
	'autoescape' => true,
);
$view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new \Wikimania\Scholarship\TwigExtension(),
);

// set default view data
$view->setData( array(
	'app' => $app,
	'lang' => $lang,
	'wgLang' => $app->wgLang,
) );

// routes
$app->get( '/', function () use ($app) {
	$app->redirect( $app->urlFor( 'apply' ));
})->name( 'home' );
Routes\Apply::addRoutes( $app );
Routes\Auth::addRoutes( $app );
Routes\Review::addRoutes( $app, '/review' );
Routes\Admin::addRoutes( $app, '/admin' );

// run the app
$app->run();
