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

namespace Wikimania\Scholarship\Controllers\Admin;

use Wikimania\Scholarship\Controller;

/**
 * View/edit Review Settings
 *
 * @author Kushal Khandelwal <kushal124@wikimedia.org>
 * @author Niharika Kohli <niharikakohli29@gmail.com>
 * @copyright © 2014 Wikimedia Foundation and contributors.
 */
class Settings extends Controller {

	protected function handleGet() {
		$settings = $this->dao->getSettings();
		$this->view->set( 'set', $settings );
		$this->render( 'admin/settings.html' );
	}


	protected function handlePost() {
		$this->form->expectInt( 'phase1pass', array( 'required' => true ) );
		$this->form->expectInt( 'phase2pass', array( 'required' => true ) );
		$this->form->expectFloat( 'weightonwiki', array( 'required' => true ) );
		$this->form->expectFloat( 'weightoffwiki', array(
			'required' => true,
		) );
		$this->form->expectFloat( 'weightinterest', array(
			'required' => true,
		) );

		if ( $this->form->validate() ) {
			$settings = array(
				'phase1pass' => $this->form->get( 'phase1pass' ),
				'phase2pass' => $this->form->get( 'phase2pass' ),
				'weightonwiki' => $this->form->get( 'weightonwiki' ),
				'weightoffwiki' => $this->form->get( 'weightoffwiki' ),
				'weightinterest' => $this->form->get( 'weightinterest' ),
			);

			if ( $settings['weightonwiki'] +
				$settings['weightoffwiki'] +
				$settings['weightinterest'] != 1
			) {
				$this->flash( 'error', 'Sum of weights must be one' );

			} else {
				if ( $this->dao->updateSettings( $settings ) ) {
					$this->flash( 'info', 'Settings succesfully updated.' );
				} else {
					$this->flash( 'error', 'Settings could not be updated. ' );
				}
			}
		} else {
			//FIXME: actually pass form errors back to view
			$this->flash( 'error', 'Invalid input.' );
		}

		$this->redirect( $this->urlFor( 'admin_settings' ) );
	}

}
