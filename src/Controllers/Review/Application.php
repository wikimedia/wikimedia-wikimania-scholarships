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

namespace Wikimania\Scholarship\Controllers\Review;

use Wikimedia\Slimapp\Controller;

/**
 * Process a scholarship application.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Application extends Controller {

	protected function handleGet() {
		$this->form->expectInt( 'id', [ 'min_range' => 0 ] );
		$this->form->expectInt( 'phase',
			[ 'min_range' => 0, 'max_range' => 2, 'default' => 2 ]
		);
		$this->form->validate( $_GET );
		$id = $this->form->get( 'id' );
		$phase = $this->form->get( 'phase' );
		$userId = $this->authManager->getUserId();

		if ( $id === null ) {
			// Attempt to find first unreviewed application for the current user
			$unreviewed = $this->dao->myUnreviewed( $phase );
			if ( $unreviewed ) {
				$id = min( $unreviewed );
			}
		}

		$this->view->set( 'phase', $phase );
		$this->view->set( 'id', $id );

		$schol = false;
		if ( $id !== null && $id >= 0 ) {
			$schol = $this->dao->getScholarship( $id );
		}
		$this->view->set( 'schol', $schol );

		if ( $schol === false ) {
			$this->flashNow( 'error', 'Application not found' );

		} else {
			$this->view->set( 'myscorings', $this->dao->myRankings( $id, $phase ) );
			$this->view->set( 'reviewers', $this->dao->getReviewers( $id, $phase ) );
			$this->view->set( 'nextid', $this->dao->nextApp( $id, $phase ) );
			$this->view->set( 'previd', $this->dao->prevApp( $id, $phase ) );
		}

		$this->render( 'review/view.html' );
	}

	protected function handlePost() {
		$criteria = [ 'valid', 'relexp', 'expshare' ];

		$this->form->expectInt( 'phase', [ 'min_range' => 0, 'max_range' => 2, 'default' => 2 ] );
		$this->form->requireInt( 'id', [ 'min_range' => 0 ] );
		$this->form->expectString( 'notes' );
		foreach ( $criteria as $c ) {
			$this->form->expectInt( $c, [ 'min_range' => 0 ] );
		}

		if ( $this->form->validate() ) {
			$id = $this->form->get( 'id' );

			$success = true;
			foreach ( $criteria as $c ) {
				$score = $this->form->get( $c );
				if ( $score !== null ) {
					$success &= $this->dao->insertOrUpdateRanking( $id, $c, $score );
				}
			}

			if ( $this->form->get( 'notes' ) !== null ) {
				$success &= $this->dao->updateNotes( $id, $this->form->get( 'notes' ) );
			}
			if ( $success ) {
				$this->flash( 'info', 'Changes saved.' );
			} else {
				$this->flash( 'error', 'Error(s) saving changes. See logs.' );
			}

		} else {
			// FIXME: log error(s)
			$this->flash( 'error', 'Invalid submission' );
		}
		$phase = $this->form->get( 'phase' );
		$this->redirect( $this->urlFor( 'review_view' ) . "?id={$id}&phase={$phase}" );
	}
}
