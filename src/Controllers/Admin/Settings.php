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

use Wikimedia\Slimapp\Controller;

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
		$this->form->requireInt( 'phase1pass' );
		$this->form->requireInt( 'phase2pass' );
		$this->form->requireFloat( 'relexp' );
		$this->form->requireFloat( 'expshare' );
		$this->form->requireDateTime( 'apply_open', 'Y-m-d' );
		$this->form->requireDateTime( 'apply_close' , 'Y-m-d' );

		if ( $this->form->validate() ) {
			$settings = array(
				'phase1pass' => $this->form->get( 'phase1pass' ),
				'phase2pass' => $this->form->get( 'phase2pass' ),
				'relexp' => $this->form->get( 'relexp' ),
				'expshare' => $this->form->get( 'expshare' ),
				'apply_open' => $this->form->get( 'apply_open' )->format( 'Y-m-d' ),
				'apply_close' => $this->form->get( 'apply_close' )->format( 'Y-m-d' ),
			);

			if ( ( $settings['relexp'] + $settings['expshare'] ) != 1 ) {
				$this->flash( 'error', 'Sum of weights must be one' );

			} else {
				if ( $this->dao->updateSettings( $settings ) ) {
					$this->config( 'period.open', $this->form->get( 'apply_open' ) );
					$this->config( 'period.close', $this->form->get( 'apply_close' ) );
					$this->flash( 'info', 'Settings successfully updated.' );
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
