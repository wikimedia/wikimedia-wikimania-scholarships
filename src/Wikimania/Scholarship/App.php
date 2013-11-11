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
 */

namespace Wikimania\Scholarship;

/**
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class App {

	/**
	 * @var string $deployDir
	 */
	protected $deployDir;

	/**
	 * @var \Slim\Slim $slim
	 */
	protected $slim;

	public function __construct( $deployDir ) {
		$this->deployDir = $deployDir;

		$this->slim = new \Slim\Slim( array(
			'view' => new \Slim\Views\Twig(),
			'templates.path' => "{$this->deployDir}/data/templates",
			'i18n.path' => "{$this->deployDir}/data/i18n",
		) );

		$di = $this->slim->container;

		$di->singleton( 'userDao', function ( $c ) {
			return new \Wikimania\Scholarship\Dao\User();
		});

		$di->singleton( 'authManager', function ( $c ) {
			return new \Wikimania\Scholarship\AuthManager( $c['userDao'] );
		});

		$di->singleton( 'applyDao', function ( $c ) {
			$uid = $c->authManager->getUserId();
			return new \Wikimania\Scholarship\Dao\Apply( $uid );
		});

		$di->singleton( 'wgLang', function ( $c ) {
			return new Lang( $c['settings']['i18n.path'] );
		});

		$di->singleton( 'applyForm', function ( $c ) {
			$dao = $c->applyDao;
			return new \Wikimania\Scholarship\Forms\Apply( $dao );
		});

	}

	/**
	 * Main entry point for all requests.
	 */
	public function run () {
		session_name( '_s' );
		session_cache_limiter(false);
		session_start();

		$slim = $this->slim;

		$slim->mock = Config::get( 'mock' );
		$lang = $slim->wgLang->setLang( $_REQUEST );

		// configure twig views
		$view = $slim->view();
		$view->parserOptions = array(
			// FIXME: configurable?
			'charset' => 'utf-8',
			'debug' => true,
			'strict_variables' => true,
			'autoescape' => true,
		);
		$view->parserExtensions = array(
			new \Slim\Views\TwigExtension(),
			new TwigExtension(),
		);

		// set default view data
		$view->replace( array(
			'app' => $slim,
			'lang' => $lang,
			'wgLang' => $slim->wgLang,
		) );

		$this->addRoutes();

		// run the app
		$slim->run();
	}


	protected function addRoutes() {
		$slim = $this->slim;

		// "Root" routes for non-autenticated users
		$slim->group( '/', function () use ( $slim ) {

			$slim->get( '', function () use ( $slim ) {
				$slim->flashKeep();
				$slim->redirect( $slim->urlFor( 'apply' ) );
			})->name( 'home' );

			$slim->get( 'apply', function () use ( $slim ) {
				$page = new Controllers\ScholarshipApplication( $slim );
				$page->setForm( $slim->applyForm );
				$page();
			})->name( 'apply' );

			$slim->post( 'apply', function () use ( $slim ) {
				$page = new Controllers\ScholarshipApplication( $slim );
				$page->setForm( $slim->applyForm );
				$page();
			})->name( 'apply_post' );

			App::template( $slim, 'contact' );
			App::template( $slim, 'credits' );
			App::template( $slim, 'privacy' );
			App::template( $slim, 'translate' );

			$slim->get( 'login', function () use ( $slim ) {
				$page = new Controllers\Login( $slim );
				$page();
			})->name( 'login' );

			$slim->post( 'login.post', function () use ( $slim ) {
				$page = new Controllers\Login( $slim );
				$page();
			})->name( 'login_post' );

			$slim->get( 'logout', function () use ( $slim ) {
				$slim->authManager->logout();
				$slim->redirect( $slim->urlFor( 'home' ) );
			})->name( 'logout' );

		});

		// middlewear route that requires authentication
		$requireUser = function () use ( $slim ) {
			if ( $slim->authManager->isAnonymous() ) {
				$uri = $slim->request->getpathInfo();
				$qs = \Wikimania\Scholarship\Form::qsMerge();
				if ( $qs ) {
					$uri = "{$uri}?{$qs}";
				}
				$_SESSION[AuthManager::NEXTPAGE_SESSION_KEY] = $uri;
				$slim->flash( 'error', 'Login required' );
				$slim->flashKeep();
				$slim->redirect( $slim->urlFor( 'login' ) );
			}

			$user = $slim->authManager->getUser();
			$slim->view->set( 'user', $user );
			$slim->view->set( 'isadmin', $slim->authManager->isAdmin() );
		};

		// routes for authenticated users
		$slim->group( '/user/', $requireUser, function () use ( $slim ) {

			$slim->get( '', function () use ( $slim ) {
				$slim->flashKeep();
				$slim->redirect( $slim->urlFor( 'user_changepassword' ) );
			})->name( 'user_home' );

			$slim->get( 'changePassword', function () use ( $slim ) {
				$page = new Controllers\User\ChangePassword( $slim );
				$page();
			})->name( 'user_changepassword' );

			$slim->post( 'changePassword.post', function () use ( $slim ) {
				$page = new Controllers\User\ChangePassword( $slim );
				$page->setDao( $slim->userDao );
				$page();
			})->name( 'user_changepassword_post' );

		});

		// routes for reviewers
		$slim->group( '/review/', $requireUser, function () use ( $slim ) {

			$slim->get( '', function () use ( $slim ) {
				$slim->flashKeep();
				$slim->redirect( $slim->urlFor( 'review_phase1' ) );
			})->name( 'review_home' );

			$slim->get( 'view', function () use ( $slim ) {
				$page = new Controllers\Review\Application( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_view' );

			$slim->post( 'view.post', function () use ( $slim ) {
				$page = new Controllers\Review\Application( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_view_post' );

			$slim->get( 'phase1', function () use ( $slim ) {
				$page = new Controllers\Review\PhaseGrid( $slim );
				$page->setDao( $slim->applyDao );
				$page->setPhase( 1 );
				$page();
			})->name( 'review_phase1' );

			$slim->get( 'phase2', function () use ( $slim ) {
				$page = new Controllers\Review\PhaseGrid( $slim );
				$page->setDao( $slim->applyDao );
				$page->setPhase( 2 );
				$page();
			})->name( 'review_phase2' );

			$slim->get( 'p1/successList', function () use ( $slim ) {
				$page = new Controllers\Review\Phase1List( $slim );
				$page->setDao( $slim->applyDao );
				$page->setType( Controllers\Review\Phase1List::TYPE_SUCCESS );
				$page();
			})->name( 'review_p1_success' );

			$slim->get( 'p1/failList', function () use ( $slim ) {
				$page = new Controllers\Review\Phase1List( $slim );
				$page->setDao( $slim->applyDao );
				$page->setType( Controllers\Review\Phase1List::TYPE_FAIL );
				$page();
			})->name( 'review_p1_fail' );

			$slim->get( 'p2/list', function () use ( $slim ) {
				$page = new Controllers\Review\Phase2List( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_p2_list' );

			$slim->get( 'search', function () use ( $slim ) {
				$page = new Controllers\Review\Search( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_search' );

			$slim->get( 'countries', function () use ( $slim ) {
				$page = new Controllers\Review\Countries( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_countries' );

			$slim->get( 'scores', function () use ( $slim ) {
				$page = new Controllers\Review\Scores( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_scores' );

			$slim->get( 'regions', function () use ( $slim ) {
				$page = new Controllers\Review\Regions( $slim );
				$page->setDao( $slim->applyDao );
				$page();
			})->name( 'review_regions' );

		});

		// middlewear that requires admin rights
		$requireAdmin = function () use ( $slim ) {
			if ( !$slim->authManager->isAdmin() ) {
				$uri = $slim->request->getpathInfo();
				$qs = \Wikimania\Scholarship\Form::qsMerge();
				if ( $qs ) {
					$uri = "{$uri}?{$qs}";
				}
				$_SESSION[AuthManager::NEXTPAGE_SESSION_KEY] = $uri;
				$slim->flash( 'error', 'Admin rights required' );
				$slim->flashKeep();
				$slim->redirect( $slim->urlFor( 'login' ) );
			}
		};

		$slim->group( '/admin/', $requireUser, $requireAdmin, function () use ( $slim ) {

			$slim->get( 'users', function () use ( $slim ) {
				$page = new Controllers\Admin\Users( $slim );
				$page->setDao( $slim->userDao );
				$page();
			})->name( 'admin_users' );

			$slim->get( 'user/:id', function ( $id ) use ( $slim ) {
				$page = new Controllers\Admin\User( $slim );
				$page->setDao( $slim->userDao );
				$page( $id );
			})->name( 'admin_user' );

			$slim->post( 'user.post', function () use ( $slim ) {
				$page = new Controllers\Admin\User( $slim );
				$page->setDao( $slim->userDao );
				$page();
			})->name( 'admin_user_post' );

		});
	}


	/**
	 * Add a static template route to the app.
	 * @param \Slim\Slim $slim App
	 * @param string $name Page name
	 * @param string $routeName Name for the route
	 */
	public static function template( $slim, $name, $routeName = null ) {
		$routeName = $routeName ?: $name;

		$slim->get( $name, function () use ( $slim, $name ) {
			$slim->render( "{$name}.html" );
		})->name( $routeName );
	}

} //end App
