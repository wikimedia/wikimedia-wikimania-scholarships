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

namespace Wikimania\Scholarship\Dao;

/**
 * Data access object for scholarship applications.
 */
class Apply extends AbstractDao {

	/**
	 * @var int $userid
	 */
	protected $userid;

	/**
	 * @var array $settings
	 */
	protected $settings = array(
		'phase1pass'     => 3,     // p1score needed to pass into phase2
		'weightonwiki'   => 0.5,   // contribution of onwiki to final score
		'weightoffwiki'  => 0.2,   // contribution of offwiki to final score
		'weightinterest' => 0.3,   // contribution of interest to final score
	);

	/**
	 * @param string $dsn PDO data source name
	 * @param string $user Database user
	 * @param string $pass Database password
	 * @param int|bool $uid Authenticated user
	 * @param array $settings Configuration settings
	 * @param LoggerInterface $logger Log channel
	 */
	public function __construct( $dsn, $user, $pass,
		$uid = false, $settings = null, $logger = null
	) {
		parent::__construct( $dsn, $user, $pass, $logger );
		$this->userid = $uid;
		$settings = is_array( $settings ) ? $settings : array();
		$this->settings = array_merge( $this->settings, $settings );
	}


	/**
	 * Save a new application.
	 *
	 * @param array $answers Application data
	 * @return int|bool Application id or false if an exception was generated
	 */
	public function saveApplication( $answers ) {
		$cols = array_keys( $answers );
		$parms = array_map( function ($elm) { return ":{$elm}"; }, $cols );
		$sql = self::concat(
			'INSERT INTO scholarships (',
			implode( ',', $cols ),
			') VALUES (',
			implode( ',', $parms ),
			')'
		);
		return $this->insert( $sql, $answers );
	}


	/**
	 * @param array $params Query parameters
	 */
	public function gridData( $params ) {
		$defaults = array(
			'apps'   => 'unreviewed',
			'items'  => 50,
			'page' => 0,
			'min'    => -2,
			'max'    => 999,
			'phase'  => 1,
		);
		$params = array_merge( $defaults, $params );

		$where = array();
		$bindVars = array();
		$bindVars['int_userid'] = $this->userid ?: 0;

		if ( $params['items'] == 'all' ) {
			$limit = '';
			$offset = '';

		} else {
			$bindVars['int_limit'] = (int)$params['items'];
			$bindVars['int_offset'] = (int)$params['page'] * (int)$params['items'];
			$limit = 'LIMIT :int_limit';
			$offset = 'OFFSET :int_offset';
		}

		$fields = array(
			's.id',
			's.fname',
			's.lname',
			's.email',
			's.residence',
			's.exclude',
			's.gender',
			'(YEAR(NOW()) - YEAR(s.dob)) as age',
			'c.country_name',
			'COALESCE(p1score, 0) as p1score',
			'mycount',
		);

		if ( $params['phase'] == 1 ) {
			switch( $params['apps'] ) {
				case 'unreviewed':
					$where[] = 'p1count IS NULL';
					break;
				case 'myapps':
					$where[] = 'mycount IS NULL';
					break;
				default:
					break;
			}

		} else if ( $params['phase'] == 2 ) {
			switch( $params['apps'] ) {
				case 'unreviewed':
					$where[] = 'nscorers IS NULL';
					break;
				case 'myapps':
					$where[] = 'mycount IS NULL';
					break;
				default:
					break;
			}
		}

		$p1scoreSql = $this->makeAggregateRankSql( 'valid', 'SUM', 'p1score' );
		$p1countSql = $this->makeAggregateRankSql( 'valid', 'COUNT', 'p1count' );

		$mycountSql = self::concat(
			"SELECT scholarship_id, COUNT(rank) AS mycount",
			"FROM rankings",
			"WHERE criterion = 'valid'",
			"AND user_id = :int_userid",
			"GROUP BY scholarship_id"
		);

		$p2scoreSql = self::concat(
			"SELECT scholarship_id, SUM(rank) AS p2score",
			"FROM rankings",
			"WHERE criterion <> 'valid'",
			"GROUP BY scholarship_id"
		);

		$nscorersSql = self::concat(
			"SELECT scholarship_id, COUNT(DISTINCT user_id) AS nscorers",
			"FROM rankings",
			"WHERE criterion <> 'valid'",
			"GROUP BY scholarship_id"
		);

		$mycount2Sql = self::concat(
			"SELECT scholarship_id, COUNT(rank) AS mycount",
			"FROM rankings",
			"WHERE criterion <> 'valid'",
			"AND user_id = :int_userid",
			"GROUP BY scholarship_id"
		);

		$joins = array(
			"LEFT OUTER JOIN iso_countries c ON s.residence = c.code",
			"LEFT OUTER JOIN ({$p1scoreSql}) r1 ON s.id = r1.scholarship_id",
		);

		$havingExtra = '';

		if ( $params['phase'] == 1 ) {
			$fields[] = 'p1count';

			$joins = array_merge( $joins, array(
				"LEFT OUTER JOIN ({$p1countSql}) r2 on s.id = r2.scholarship_id",
				"LEFT OUTER JOIN ( {$mycountSql} ) r3 on s.id = r3.scholarship_id",
			) );

			$havingExtra = "AND p1score >= :int_min AND p1score <= :int_max";
			$bindVars['int_min'] = (int)$params['min'];
			$bindVars['int_max'] = (int)$params['max'];

		} else {
			$fields[] = 'COALESCE(p2score, 0) as p2score';
			$fields[] = 'COALESCE(nscorers, 0) as nscorers';

			$where[] = 'p1score >= :int_phase1pass';
			$bindVars['int_phase1pass'] = (int)$this->settings['phase1pass'];

			$joins = array_merge( $joins, array(
				"LEFT OUTER JOIN ({$p2scoreSql}) r2 ON s.id = r2.scholarship_id",
				"LEFT OUTER JOIN ({$nscorersSql}) r3 ON s.id = r3.scholarship_id",
				"LEFT OUTER JOIN ({$mycount2Sql}) r4 ON s.id = r4.scholarship_id",
			) );
		}

		$sql = self::concat(
			"SELECT SQL_CALC_FOUND_ROWS", implode( ',', $fields ),
			"FROM scholarships s",
			$joins,
			self::buildWhere( $where ),
			"GROUP BY s.id, s.fname, s.lname, s.email, s.residence",
			"HAVING s.exclude = 0", $havingExtra,
			"ORDER BY s.id",
			$limit,
			$offset
		);


		return $this->fetchAllWithFound( $sql, $bindVars );
	} // end gridData


	public function myUnreviewed( $phase ) {
		if ( $phase == 1 ) {
			$crit = 'valid';
		} else {
			$crit = 'onwiki';
		}

		$sql = self::concat(
			"SELECT s.id",
			"FROM scholarships s",
			"WHERE s.id NOT IN (",
			"SELECT scholarship_id",
			"FROM rankings",
			"WHERE user_id = :int_uid",
			"AND criterion = :crit)",
			"ORDER BY s.id"
		);

		$res = $this->fetchAll( $sql, array(
			'int_uid' => $this->userid,
			'crit' => $crit,
		) );
		return array_map( function ($row) { return $row['id']; }, $res );
	}


	public function search( $params ) {
		$defaults = array(
			'first' => null,
			'last' => null,
			'residence' => null,
			'region' => null,
			'size' => null,
			'globalns' => null,
			'items'  => 50,
			'page' => 0,
		);
		$params = array_merge( $defaults, $params );

		$where = array();
		$crit = array(
			'int_uid' => $this->userid ?: 0,
		);

		$limit = "LIMIT :int_limit";
		$crit['int_limit'] = $params['items'];

		$offset = "OFFSET :int_offset";
		$crit['int_offset'] = (int)$params['page'] * (int)$params['items'];

		if ( $params['last'] !== null ) {
			$where[] = "s.lname = :last";
			$crit['last'] = $params['last'];
		}
		if ( $params['first'] !== null ) {
			$where[] = "s.fname = :first";
			$crit['first'] = $params['first'];
		}
		if ( $params['residence'] !== null ) {
			$where[] = "c.country_name = :residence";
			$crit['residence'] = $params['residence'];
		}
		if ( $params['region'] !== null ) {
			$where[] = "c.region = :region";
			$crit['region'] = $params['region'];
		}
		if ( $params['size'] !== null ) {
			$where[] = "l.size = :size";
			$crit['size'] = $params['size'];
		}
		if ( $params['globalns'] !== null ) {
			$where[] = "c.globalns = :globalns";
			$crit['globalns'] = $params['globalns'];
		}

		$where[] = "s.exclude = 0";

		$fields = array(
			"s.id",
			"s.fname",
			"s.lname",
			"s.email",
			"s.residence",
			"s.exclude",
			"s.gender",
			"(YEAR(NOW()) - YEAR(s.dob)) AS age",
			"c.country_name",
			"c.region",
			"c.globalns",
			"l.size",
			"COALESCE(p1score, 0) AS p1score",
			"p1count",
			"mycount",
		);

		$p1scoreSql = $this->makeAggregateRankSql( 'valid', 'SUM', 'p1score' );
		$p1countSql = $this->makeAggregateRankSql( 'valid', 'COUNT', 'p1count' );

		$mycountSql = self::concat(
			"SELECT scholarship_id, COUNT(rank) AS mycount",
			"FROM rankings",
			"WHERE criterion = 'valid'",
			"AND user_id = :int_uid",
			"GROUP BY scholarship_id"
		);

			$sql = self::concat(
				"SELECT SQL_CALC_FOUND_ROWS", implode( ',', $fields ),
				"FROM scholarships s",
				"LEFT OUTER JOIN ( {$p1scoreSql} ) r1 ON s.id = r1.scholarship_id",
				"LEFT OUTER JOIN ( {$p1countSql} ) r2 ON s.id = r2.scholarship_id",
				"LEFT OUTER JOIN ( {$mycountSql} ) r3 ON s.id = r3.scholarship_id",
				"LEFT OUTER JOIN iso_countries c ON s.residence = c.code",
				"LEFT OUTER JOIN language_communities l ON s.community = l.code",
				self::buildWhere( $where ),
				"ORDER BY s.id",
				$limit,
				$offset
			);

		return $this->fetchAllWithFound( $sql, $crit );
	}


	public function getScholarship( $id ) {
		$fields = array(
			's.*',
			's.id',
			'c.country_name',
			'r.country_name AS residence_name',
			'l.language AS community_name',
		);
		$sql = self::concat(
			'SELECT', implode( ',', $fields ),
			'FROM scholarships s',
			'LEFT OUTER JOIN iso_countries c ON s.nationality = c.code',
			'LEFT OUTER JOIN iso_countries r ON s.residence = r.code',
			'LEFT OUTER JOIN language_communities l ON s.community = l.code',
			'WHERE s.id = :int_id'
		);
		return $this->fetch( $sql, array( 'int_id' => $id ) );
	}


	/**
	 * Find the next unreviewed id after the given id.
	 * @return int|bool Next id or false if none available
	 */
	public function nextApp( $id, $phase ) {
		$myapps = $this->myUnreviewed( $phase );
		foreach ( $myapps as $app ) {
			if ( $app > $id ) {
				return $app;
			}
		}
		return false;
	}


	public function prevApp( $id, $phase ) {
		$myapps = $this->myUnreviewed( $phase );
		$prior = false;
		foreach ( $myapps as $app ) {
			if ( $app >= $id ) {
				return $prior;
			}
			$prior = $app;
		}
		return false;
	}


	public function insertOrUpdateRanking( $scholarship_id, $criterion, $rank ) {
		$sql = self::concat(
			'INSERT INTO rankings (user_id, scholarship_id, criterion, rank)',
			'VALUES (:int_uid, :int_sid, :str_crit, :int_rank)',
			'ON DUPLICATE KEY UPDATE rank = :int_rank, entered_on = now()'
		);

		return $this->update( $sql, array(
			'int_uid' => $this->userid,
			'int_sid' => $scholarship_id,
			'str_crit' => $criterion,
			'int_rank' => $rank,
		) );
	}


	public function updateNotes( $id, $notes ) {
		return $this->update(
			'update scholarships set notes = :notes where id = :int_id',
			array(
				'int_id' => $id,
				'notes' => $notes,
			)
		);
	}


	public function getReviewers( $id, $phase ) {
		$where = array( "r.scholarship_id = :int_sid" );
		if ( $phase == 1 ) {
			$where[] = "r.criterion = 'valid'";
		} else {
			$where[] = "r.criterion = 'onwiki'";
		}

		$sql = self::concat(
			"SELECT DISTINCT(u.username) AS username",
			"FROM rankings r",
			"INNER JOIN users u ON r.user_id = u.id",
			self::buildWhere( $where ),
			"ORDER BY u.username"
		);
		return $this->fetchAll( $sql, array( 'int_sid' => $id ) );
	}


	public function myRankings( $id, $phase ) {
		$where = array( "r.scholarship_id = :int_sid" );
		if ( $phase == 1 ) {
			$where[] = "r.criterion = 'valid'";

		} else {
			$where[] = "r.criterion <> 'valid'";
		}
		$where[] = "u.id = :int_uid";

		$sql = self::concat(
			"SELECT r.scholarship_id, u.username, r.rank, r.criterion",
			"FROM rankings r",
			"INNER JOIN users u ON r.user_id = u.id",
			self::buildWhere( $where ),
			"ORDER BY r.criterion, r.rank"
		);
		return $this->fetchAll( $sql, array(
			'int_sid' => $id,
			'int_uid' => $this->userid,
		) );
	}


	/**
	 * Get a list of scholarships at the close of phase 1 screening.
	 *
	 * @param bool $success True to get phase 2 eligable applicants, false for
	 * rejects
	 * @return array Query results
	 */
	protected function getPhase1List( $success ) {
		$fields = array(
			's.id',
			's.fname',
			's.lname',
			's.email',
			's.exclude',
			'COALESCE(p1score, 0) as p1score',
		);

		$p1scoreSql = $this->makeAggregateRankSql( 'valid', 'SUM', 'p1score' );

		$op = ( $success ) ? '>=' : '<' ;

		return $this->fetchAll( self::concat(
			"SELECT" , implode( ',', $fields ),
			"FROM scholarships s",
			"LEFT OUTER JOIN ({$p1scoreSql}) r2 ON s.id = r2.scholarship_id",
			"GROUP BY s.id, s.fname, s.lname, s.email",
			"HAVING p1score {$op} :int_phase1pass AND s.exclude = 0"
		), array(
			'int_phase1pass' => (int)$this->settings['phase1pass'],
		) );
	}


	public function getPhase1EarlyRejects() {
		return $this->getPhase1List( false );
	}


	public function getPhase1Success() {
		return $this->getPhase1List( true );
	}


	public function getRegionList() {
		$res = $this->fetchAll( "SELECT DISTINCT region FROM iso_countries" );
		return array_map( function ($row) { return $row['region']; }, $res );
	}


	public function getP2List( $region = 'All' ) {
		$params = array();

		$fields = array(
			"s.id",
			"s.fname",
			"s.lname",
			"s.email",
			"s.residence",
			"s.exclude",
			"s.gender",
			"YEAR(NOW()) - YEAR(s.dob) AS age",
			"c.country_name",
			"COALESCE(p1score, 0) AS p1score",
			"COALESCE(nscorers, 0) AS nscorers",
			"rk_ow.onwiki AS onwiki",
			"rk_ofw.offwiki AS offwiki",
			"rk_i.interest AS interest",
			"(COALESCE(:weightonwiki * rk_ow.onwiki, 0) + " .
			"COALESCE(:weightoffwiki * rk_ofw.offwiki, 0) + " .
			"COALESCE(:weightinterest * rk_i.interest, 0)) as p2score",
		);

		$params['weightonwiki'] = (float)$this->settings['weightonwiki'];
		$params['weightoffwiki'] = (float)$this->settings['weightoffwiki'];
		$params['weightinterest'] = (float)$this->settings['weightinterest'];

		$sqlOnWiki = $this->makeAggregateRankSql( 'onwiki', 'AVG' );
		$sqlOffWiki = $this->makeAggregateRankSql( 'offwiki', 'AVG' );
		$sqlInterest = $this->makeAggregateRankSql( 'interest', 'AVG' );
		$sqlP1Score = $this->makeAggregateRankSql( 'valid', 'SUM', 'p1score' );

		$sqlNumScorers = self::concat(
			"SELECT scholarship_id, COUNT(DISTINCT user_id) AS nscorers",
			"FROM rankings",
			"WHERE criterion <> 'valid'",
			"GROUP BY scholarship_id"
		);

		if ( $region != 'All' ) {
			$params['region'] = $region;
			$regionJoin = 'INNER JOIN iso_countries c1 ON c.region = :region';
		} else {
			$regionJoin = '';
		}

		$sql = self::concat(
			"SELECT", implode( ',', $fields ),
			"FROM scholarships s",
			"LEFT OUTER JOIN ({$sqlOnWiki}) rk_ow ON s.id = rk_ow.scholarship_id",
			"LEFT OUTER JOIN ({$sqlOffWiki}) rk_ofw ON s.id = rk_ofw.scholarship_id",
			"LEFT OUTER JOIN ({$sqlInterest}) rk_i ON s.id = rk_i.scholarship_id",
			"LEFT OUTER JOIN ({$sqlP1Score}) p1 ON s.id = p1.scholarship_id",
			"LEFT OUTER JOIN ({$sqlNumScorers}) ns ON s.id = ns.scholarship_id",
			"LEFT OUTER JOIN iso_countries c ON s.residence = c.code",
			$regionJoin,
			'GROUP BY s.id, s.fname, s.lname, s.email, s.residence',
			'HAVING p1score >= :int_phase1pass AND s.exclude = 0',
			'ORDER BY p2score DESC, s.id ASC'
		);

		$params['int_phase1pass'] = (int)$this->settings['phase1pass'];

		return $this->fetchAll( $sql, $params );
	}


	// Country administration
	public function getListOfCountries( $order = "country_name" ) {
		return $this->fetchAll( self::concat(
			"SELECT count(*) as sid, c.country_name, c.region",
			"FROM scholarships s",
			"LEFT JOIN iso_countries c ON c.code = s.residence",
			"GROUP BY c.country_name"
		) );
	}


	/*
	 * Fetch applications for different language groups
	 * Possible language groupings are:
	 * Small language community - Global North
	 * Small language community - Global South
	 * Medium language community - Global North
	 * Medium language community - Global South
	 * Large language community - Global South
	 * Large language community - Global North
	 * Multilingual community - Global North
	 * Multilingual community - Global South
	 */
	public function getListOfCommunities() {
		return $this->fetchAll( self::concat(
			"SELECT count(*) as sid, l.size, c.globalns",
			"FROM scholarships s",
			"LEFT JOIN language_communities l ON l.code = s.community",
			"LEFT JOIN iso_countries c ON c.code = s.residence",
			"GROUP BY l.size"
		) );
	}


	public function getListOfRegions() {
		return $this->fetchAll( self::concat(
			"SELECT count(*) as count, c.region",
			"FROM scholarships s",
			"LEFT JOIN iso_countries c ON c.code = s.residence",
			"GROUP BY region"
		) );
	}


	/**
	 * Create an SQL query that will compute an aggregate value for all rankings
	 * of a given criterion.
	 *
	 * @param string $criterion Ranking criterion to aggregate
	 * @param string $func Aggregate function (AVG,SUM,MAX,MIN,...)
	 * @param string $alias Aggregate column alias
	 * @return string SQL to compute the desired aggregate for all scholarships
	 */
	protected function makeAggregateRankSql(
		$criterion, $func, $alias = null ) {
		$alias = $alias ?: $criterion;
		// $criterion isn't user input but we'll be paranoid anyway
		$criterion = $this->dbh->quote( $criterion );

		return self::concat(
			"SELECT scholarship_id, {$func}(rank) AS {$alias}",
			"FROM rankings",
			"WHERE criterion = {$criterion}",
			"GROUP BY scholarship_id"
		);
	}

}
