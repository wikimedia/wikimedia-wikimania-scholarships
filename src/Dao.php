<?php

/**
 * Data access object.
 */
class Dao extends AbstractDao {

	/**
	 * @varint $userid
	 */
	protected $userid;

	public function __construct() {
		parent::__construct();

		// FIXME: use this everywhere ofr nowhere
		if ( isset( $_SESSION['user_id'] ) ) {
			$this->userid = $_SESSION['user_id'];
		}
	}

	private static function buildWhere( $where ) {
		$sql = '';
		for ( $i = 0; $i < count( $where ); $i++ ) {
			if ( $i == 0 ) {
				$sql = "WHERE " . $where[$i];
			} else {
				$sql = $sql . " AND " . $where[$i];
			}
		}
		return $sql;
	}

	private static function buildSelect( $fields ) {
		return 'SELECT ' . implode( ',', $fields ) . ' ';
	}

	private static function buildFrom( $tables ) {
		$fromtables = array();
		foreach ( $tables as $k => $v ) {
			$fromtables[] = "{$v} as {$k}";
		}
		return ' FROM '  . implode( ',', $fromtables ) . ' ';
	}

	/**
	 * Save a new application.
	 *
	 * @param array $answers Application data
	 * @return int|bool Application id or false if an exception was generated
	 */
	public function saveApplication ( $answers ) {
		$cols = array_keys( $answers );
		$parms = array_map( function ($elm) { return ":{$elm}"; }, $cols );
		$sql = 'INSERT INTO scholarships (' . implode( ',', $cols ) .
			') VALUES (' . implode( ',', $parms ) . ')';
		return $this->insert( $sql, $answers );
	}

	public function gridData( $params ) {
		$where = array();
		$apps = isset( $params['apps'] ) ? $params['apps'] : 'unreviewed';
		$myid = $this->userid ?: 0;
		$items = ( isset( $params['items'] ) && is_int( $params['items'] ) ) ? $params['items'] : 50;

		$p = isset( $params['offset'] ) ? (int)$params['offset'] : 0;
		$offset = " OFFSET " . ( $p * $items );

		if ( $params['items'] == 'all' ) {
			$limit = " ";
			$offset = " ";
		} else {
			$limit = " LIMIT $items ";
		}

		if ( $params['phase'] == 1 ) {
			switch( $apps ) {
				case 'unreviewed':
					array_push( $where, ' p1count IS NULL ' );
					break;
				case 'myapps':
					array_push( $where, ' mycount IS NULL ' );
					break;
				default:
					break;
			}
		} else if ( $params['phase'] == 2 ) {
			switch( $apps ) {
				case 'unreviewed':
					array_push( $where, ' nscorers IS NULL ' );
					break;
				case 'myapps':
					array_push( $where, ' mycount IS NULL ' );
					break;
				default:
					break;
			}
		}

		if ( $params['phase'] == 1 ) {
			$tables = array( 's' => 'scholarships' );
			$fields = array( 's.id',
				's.fname',
				's.lname',
				's.email',
				's.residence',
				's.exclude',
				's.sex',
				//FIXME hard coded year
				'(2013 - year(s.dob)) as age',
				'(s.canpaydiff*s.wantspartial) as partial',
				'c.country_name',
				'coalesce(p1score,0) as p1score',
				'p1count',
				'mycount'
			);

			$sql = self::buildSelect( $fields ) . self::buildFrom( $tables ) .
				"LEFT OUTER JOIN (select *, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
				LEFT OUTER JOIN (select scholarship_id, count(rank) as p1count from rankings where criterion = 'valid' group by scholarship_id) r3 on s.id = r3.scholarship_id
				LEFT OUTER JOIN (select scholarship_id, count(rank) as mycount from rankings where criterion = 'valid' AND user_id = $myid group by scholarship_id) r4 on s.id = r4.scholarship_id
				LEFT OUTER JOIN countries c on s.residence = c.id "
				. $this->buildWhere( $where ) . "
				GROUP BY s.id, s.fname, s.lname, s.email, s.residence
				HAVING p1score >= -2 and p1score <= 999 and s.exclude = 0 $limit $offset;";
		} else {
			$tables = array( 's' => 'scholarships' );
			$fields = array( 's.id',
				's.fname',
				's.lname',
				's.email',
				's.residence',
				's.exclude',
				's.sex',
				//FIXME hard coded year
				'(2013 - year(s.dob)) as age',
				'(s.canpaydiff*s.wantspartial) as partial',
				'c.country_name',
				'coalesce(p1score,0) as p1score',
				'coalesce(p2score,0) as p2score',
				'coalesce(nscorers,0) as nscorers' );
			array_push( $where, ' p1score >= 3 ' );
			$sql = self::buildSelect( $fields ) . self::buildFrom( $tables ) . "
				left outer join (select scholarship_id, sum(rank) as p2score from rankings where criterion in ('onwiki','offwiki', 'future', 'englistAbility') group by scholarship_id) r on s.id = r.scholarship_id
				left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
				left outer join (select scholarship_id, count(distinct user_id) as nscorers from rankings where criterion in ('onwiki','offwiki', 'future', 'program', 'englistAbility') group by scholarship_id) r3 on s.id = r3.scholarship_id
				left outer join countries c on s.residence = c.id
				LEFT OUTER JOIN (select scholarship_id, count(rank) as mycount from rankings where criterion IN ('onwiki', 'offwiki', 'future', 'englistAbility') AND user_id = $myid group by scholarship_id) r4 on s.id = r4.scholarship_id "
				. $this->buildWhere( $where ) . "
				group by s.id, s.fname, s.lname, s.email, s.residence
				order by s.id $limit $offset;";
		}

		return $this->fetchAll( $sql );
	}

	public function export() {
		$tables = array( 's' => 'scholarships' );
		$fields = array( 's.id',
			'c.country_name', // residence
			'ow.onwiki',
			'ofw.offwiki',
			'f.future',
			'ct.numranks',
			's.fname',
			's.lname',
			's.dob',
			's.sex',
			's.email',
			's.telephone',
			's.address',
			'c2.country_name', // nationality
			's.haspassport',
			's.airport',
			's.languages',
			's.occupation',
			's.areaofstudy',
			's.wm05',
			's.wm06',
			's.wm07',
			's.wm08',
			's.wm09',
			's.wm10',
			's.wm11',
			's.wm12',
			's.howheard',
			's.why',
			's.future',
			's.involvement',
			's.contribution',
			'ea.englishAbility',
			's.username',
			's.project',
			's.projectlangs',
			's.wantspartial',
			's.canpaydiff',
			's.sincere',
			's.agreestotravelconditions',
			's.willgetvisa',
			's.willpayincidentals',
			's.notes'
		);
		$sql = self::buildSelect( $fields ) . self::buildFrom( $tables ) . "
			LEFT JOIN (select scholarship_id, avg(rank) as onwiki from rankings where criterion IN ('onwiki') group by scholarship_id) ow ON (ow.scholarship_id = s.id)
			LEFT JOIN (select scholarship_id, avg(rank) as offwiki from rankings where criterion IN ('offwiki') group by scholarship_id) ofw ON (ofw.scholarship_id = s.id)
			LEFT JOIN (select scholarship_id, avg(rank) as future from rankings where criterion IN ('future') group by scholarship_id) f ON (f.scholarship_id = s.id)
			LEFT JOIN (select scholarship_id, avg(rank) as englishAbility from rankings where criterion IN ('englishAbility') group by scholarship_id) ea ON (ea.scholarship_id = s.id)
			LEFT JOIN (select scholarship_id, count(rank) as numranks from rankings where criterion IN ('future') group by scholarship_id) ct ON (ct.scholarship_id  = s.id)
			LEFT OUTER JOIN countries c ON s.residence = c.id
			LEFT OUTER JOIN countries c2 ON s.nationality = c2.id
			order by s.id limit 20";
		return $this->fetchAll( $sql );
	}

	public function myUnreviewed( $myid, $phase ) {
		$where = array();
		$sql = "SELECT s.id FROM scholarships s
			LEFT OUTER JOIN (select scholarship_id, count(rank) as mycount from rankings where criterion IN ('onwiki', 'offwiki', 'future', 'program', 'englistAbility') AND user_id = $myid group by scholarship_id) r4 on s.id = r4.scholarship_id
			WHERE mycount IS NULL;";

		$res = $this->fetchAll( $sql );
		return array_map( function ($row) { return $row['id']; }, $res );
	}

	public function search( $params ) {
		$myid = isset( $_SESSION['user_id'] ) ? $_SESSION['user_id'] : 0;
		$first = isset( $params['first'] ) ? mysql_real_escape_string( $params['first'] ) : null;
		$last = isset( $params['last'] ) ? mysql_real_escape_string( $params['last'] ) : null;
		$citizen = isset( $params['citizen'] ) ? mysql_real_escape_string( $params['citizen'] ) : null;
		$residence = isset( $params['residence'] ) ? mysql_real_escape_string( $params['residence'] ) : null;
		$items = isset( $params['items'] ) ? mysql_real_escape_string( $params['items'] ) : 50;
		$region = isset( $params['region'] ) ? mysql_real_escape_string( $params['region'] ) : null;

		$p = isset( $params['offset'] ) ? $params['offset'] : 0;
		$p = intval( $p );
		$offset = " OFFSET " . ( $p * $items );

		$limit = " LIMIT $items ";
		$where = array();
		if ( $last != null ) {
			array_push( $where, " s.lname = '" . $last . "' " );
		}
		if ( $first != null ) {
			array_push( $where, " s.fname = '" . $first . "' " );
		}
		if ( $residence != null ) {
			array_push( $where, " c.country_name = '" . $residence . "' " );
		}
		if ( $region != null ) {
			array_push( $where, " c.region = '" . $region . "' " );
		}
		//FIXME hard coded year
		$sql = "
			SELECT s.id, s.fname, s.lname, s.email, s.residence, s.exclude,  s.sex, (2013 - year(s.dob)) as age, (s.canpaydiff*s.wantspartial) as partial, c.country_name, c.region, coalesce(p1score,0) as p1score, p1count, mycount
			FROM scholarships s
			LEFT OUTER JOIN (select *, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
			LEFT OUTER JOIN (select scholarship_id, count(rank) as p1count from rankings where criterion = 'valid' group by scholarship_id) r3 on s.id = r3.scholarship_id
			LEFT OUTER JOIN (select scholarship_id, count(rank) as mycount from rankings where criterion = 'valid' AND user_id = $myid group by scholarship_id) r4 on s.id = r4.scholarship_id
			LEFT OUTER JOIN countries c on s.residence = c.id " .
			$this->buildWhere( $where ) . "
			GROUP BY s.id, s.fname, s.lname, s.email, s.residence
			HAVING p1score >= -2 and p1score <= 999 and s.exclude = 0 $limit $offset";
		return $this->fetchAll( $sql );
	}


	public function GetScholarship( $id ) {
		return $this->fetch( 'select *, s.id, s.residence as acountry, c.country_name, r.country_name as residence_name from scholarships s
			left outer join countries c on s.nationality = c.id
			left outer join countries r on s.residence = r.id
			where s.id = ?', array( $id ) );
	}

	public function getNext( $userid, $id, $phase ) {
		$nextid = $this->getNextId( $userid, $id, $phase );
		if ( $nextid != false ) {
			return $this->GetScholarship( $i );
		}
		return false;
	}

	public function skipApp( $userid, $id, $phase ) {
		$j = 0;
		$myapps = $this->myUnreviewed( $userid, $phase );
		for ( $i = $id; $i < max( $myapps ); $i++ ) {
			if ( in_array( $i, $myapps ) ) {
				if ( $j == 1 ) {
					return $i;
				}
				$j++;
			}
		}
		return false;
	}

	public function prevApp( $userid, $id, $phase ) {
		$j = 0;
		$myapps = $this->myUnreviewed( $userid, $phase );
		for ( $i = $id; $i > min( $myapps ); $i-- ) {
			if ( in_array( $i, $myapps ) ) {
				if ( $j == 1 ) {
					return $i;
				}
				$j++;
			}
		}
		return false;
	}

	public function getNextId( $userid, $id, $phase ) {
		$myapps = $this->myUnreviewed( $userid, $phase );
		for ( $i = $id; $i < max( $myapps ); $i++ ) {
			if ( in_array( $i, $myapps ) ) {
				return $i;
			}
		}
		return false;
	}

	public function GetCountAllUnrankedPhase1( $id ) {
		return $this->fetch( "select COUNT(*), coalesce(p1self,0) as p1self, coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p1self from rankings where criterion = 'valid' and user_id = ? group by scholarship_id) r on s.id = r.scholarship_id
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
			where p1self is null and s.rank >=0 and ((p1score < 3 and p1score > -3)) and s.exclude = 0;", array( $id ) );
	}

	public function GetCountAllUnrankedPhase2( $id ) {
		return  $this->fetch( "select COUNT(*), p2self, coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p2self from rankings where criterion in ('offwiki', 'onwiki', 'future', 'englishAbility') and user_id = ? group by scholarship_id) r on s.id = r.scholarship_id
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r3 on s.id = r3.scholarship_id
			where p2self is null and p1score >= 3 and s.exclude = 0;", array( $id ) );
	}

	public function GetCountAllPhase1() {
		return $this->fetch( "select COUNT(*) from scholarships s where s.exclude = 0;" );
	}

	public function GetCountAllPhase2() {
		return  $this->fetch( "select COUNT(*), coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r3 on s.id = r3.scholarship_id
			where p1score >= 3 and s.exclude = 0;" );
	}

	public function InsertOrUpdateRanking( $user_id, $scholarship_id, $criterion, $rank ) {
		$this->update(
			'INSERT INTO rankings (user_id, scholarship_id, criterion, rank) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rank = ?',
			array( $user_id, $scholarship_id, $criterion, $rank, $rank )
		);
	}

	public function getReviewers( $id, $phase ) {
		$where = array( "r.scholarship_id = ?" );
		if ( $phase == 1 ) {
			array_push( $where, "r.criterion IN ('valid')" );
		} else {
			array_push( $where, "r.criterion IN ('future', 'onwiki', 'offwiki', 'englishAbility')" );
		}
		$sql = "select distinct(u.username) as username from rankings r inner join users u on r.user_id = u.id " . $this->buildWhere( $where ) . " order by u.username";
		return $this->fetchAll( $sql, array( $id ) );
	}

	public function myRankings( $id, $userid, $phase ) {
		if ( $phase == 1 ) {
			$sql = "select r.scholarship_id, u.username, r.rank, r.criterion from rankings r inner join users u on r.user_id = u.id where r.criterion = 'valid' and u.id = ? AND r.scholarship_id = ?";

		} else if ( $phase == 2 ) {
			$sql = "select r.scholarship_id, u.username, r.rank, r.criterion from rankings r inner join users u on r.user_id = u.id where r.criterion IN ('onwiki', 'future', 'offwiki', 'program', 'englishAbility') and u.id = ? and r.scholarship_id = ? order by r.criterion, u.username, r.rank";
		} else {
			return false;
		}
		return $this->fetchAll( $sql, array( $userid, $id ) );
	}

	public function allRankings( $id, $phase ) {
		if ( $phase == 1 ) {
			$sql = 'select r.scholarship_id, u.username, r.rank, r.criterion from rankings r inner join users u on r.user_id = u.id where r.criterion = "valid" and r.scholarship_id = ?';
		} else if ( $phase == 2 ) {
			$sql = "select r.scholarship_id, u.username, r.rank, r.criterion from rankings r inner join users u on r.user_id = u.id where r.criterion IN ('onwiki', 'future', 'offwiki', 'program', 'englishAbility') and r.scholarship_id = ? order by r.criterion, u.username, r.rank";
		} else {
			return false;
		}
		return $this->fetchAll( $sql, array( $id ) );
	}

	public function getRankingOfUser( $user_id, $scholarship_id, $criterion ) {
		$sql = 'select rank from rankings where user_id = ? and scholarship_id = ? and criterion = ?';
		$ret = $this->fetch( $sql, array( $user_id, $scholarship_id, $criterion ) );
		return ( count( $ret ) > 0 ) ? $ret['rank'] : 0;
	}

	public function GetPhase2Rankings( $id ) {
		return $this->fetchAll( 'select r.scholarship_id, u.username, r.rank, r.criterion from rankings r inner join users u on r.user_id = u.id where r.scholarship_id = ? and r.criterion in ("onwiki","offwiki","future","englishAbility")', array( $id ) );
	}

	public function UpdateNotes( $id, $notes ) {
		$this->update(
			'update scholarships set notes = ? where id = ?',
			array( $notes, $id )
		);
	}

	public function UpdateField( $field, $id, $value ) {
		// FIXME: whitelist field
		$query = "update scholarships set {$field} = ? where id  = ?";
		$this->update( $query, array( $value, $id ) );
	}

	// Phase List

	public function GetPhase1EarlyRejects() {
		return$this->fetchAll( "select s.id, s.fname, s.lname, s.email, s.exclude, coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
			group by s.id, s.fname, s.lname, s.email
			having p1score < 3 and s.exclude = 0" );
	}

	public function GetPhase1Success() {
		return $this->fetchAll( "select s.id, s.fname, s.lname, s.email, s.exclude, coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
			group by s.id, s.fname, s.lname, s.email
			having p1score >= 3 and s.exclude = 0" );
	}

	public function GetRegionListNoCount() {
		return $this->fetchAll( "SELECT DISTINCT region FROM countries" );
	}

	public function GetP2List( $partial, $region ) {
		//FIXME hard coded year
		$sql = "select s.id, s.fname, s.lname, s.email, s.residence, s.exclude, s.sex, 2013-year(s.dob) as age, (s.canpaydiff*s.wantspartial) as partial, c.country_name, coalesce(p1score,0) as p1score, coalesce(nscorers,0) as nscorers, r.onwiki as onwiki, r2.offwiki as offwiki, r3.future as future, r6.englishAbility as englishAbility, 0.5*r.onwiki + 0.15*r2.offwiki + 0.25*r3.future + 0.1*r6.englishAbility as p2score from scholarships s
			left outer join (select scholarship_id, avg(rank) as onwiki from rankings where criterion = 'onwiki' group by scholarship_id) r on s.id = r.scholarship_id
			left outer join (select scholarship_id, avg(rank) as offwiki from rankings where criterion = 'offwiki' group by scholarship_id) r2 on s.id = r2.scholarship_id
			left outer join (select scholarship_id, avg(rank) as future from rankings where criterion = 'future' group by scholarship_id) r3 on s.id = r3.scholarship_id
			left outer join (select scholarship_id, avg(rank) as englishAbility from rankings where criterion = 'englishAbility' group by scholarship_id) r6 on s.id = r6.scholarship_id
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r4 on s.id = r4.scholarship_id
			left outer join (select scholarship_id, count(distinct user_id) as nscorers from rankings where criterion in ('onwiki','offwiki', 'future', 'englishAbility') group by scholarship_id) r5 on s.id = r5.scholarship_id
			left outer join countries c on s.residence = c.id ";

		$params = array();

		if ( $region != 'All' ) {
			$params[] = $region;
			$sql .= 'inner join countries c1 on c.region = ? ';
		}

		if ( $partial == 2 ) {
			$sql .= "group by s.id, s.fname, s.lname, s.email, s.residence
				having p1score >= 3 and s.exclude = 0 order by p2score desc";

		} else {
			$params[] = $partial;
			$sql .= 'group by s.id, s.fname, s.lname, s.email, s.residence
				having p1score >= 3 and s.exclude = 0 and partial = ? order by p2score desc';
		}

		return $this->fetchAll( $sql, $params );
	}

	// Final scoring

	public function GetFinalScoring( $partial ) {
		//FIXME hard coded year
		return $this->fetchAll( "select s.id, s.fname, s.lname, s.email, s.residence, s.exclude, s.sex, 2013-year(s.dob) as age, (s.canpaydiff*s.wantspartial) as partial, c.country_name, coalesce(p1score,0) as p1score, coalesce(nscorers,0) as nscorers, r.onwiki as onwiki, r2.offwiki as offwiki, r3.future as future, r6.englishAbility as englishAbility, 0.5*r.onwiki + 0.15*r2.offwiki + 0.25*r3.future + 0.1*r6.englishAbility as p2score from scholarships s
			left outer join (select scholarship_id, avg(rank) as onwiki from rankings where criterion = 'onwiki' group by scholarship_id) r on s.id = r.scholarship_id
			left outer join (select scholarship_id, avg(rank) as offwiki from rankings where criterion = 'offwiki' group by scholarship_id) r2 on s.id = r2.scholarship_id
			left outer join (select scholarship_id, avg(rank) as future from rankings where criterion = 'future' group by scholarship_id) r3 on s.id = r3.scholarship_id
			left outer join (select scholarship_id, avg(rank) as englishAbility from rankings where criterion = 'englishAbility' group by scholarship_id) r6 on s.id = r6.scholarship_id
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r4 on s.id = r4.scholarship_id
			left outer join (select scholarship_id, count(distinct user_id) as nscorers from rankings where criterion in ('onwiki','offwiki', 'future', 'englishAbility') group by scholarship_id) r5 on s.id = r5.scholarship_id
			left outer join countries c on s.residence = c.id
			group by s.id, s.fname, s.lname, s.email, s.residence
			having p1score >= 3 and s.exclude = 0 and partial = ?
			order by p2score desc", array( $partial ) );
	}

	// Country administration

	public function GetListofCountries( $order = "country_name" ) {
		return $this->fetchAll( "select c.id, c.country_name, c.region, c.country_rank, s.sid from countries c left join (select count(id) as sid, residence as attendees from scholarships where rank = 1 and exclude = 0 group by residence) s on c.id = s.attendees order by ?;", array( $order ) );
	}

	public function UpdateCountryRank( $id, $newrank ) {
		$this->update( "update countries set country_rank = ? where id = ?", array( $newrank, $id ) );
	}

	public function GetCountryInfo( $country_id ) {
		return $this->fetch( "select * from countries where id = ?", array( $country_id ) );
	}

	public function GetListofRegions() {
		return $this->fetchAll( "select count(*) as count, c.region from scholarships s LEFT JOIN countries c on c.id = s.residence group by region;" );
	}

	public function GetPhase1EarlyRejectsTemp() {
		$res = $this->fetchAll( "select s.id, s.fname, s.lname, s.email, s.exclude, coalesce(p1score,0) as p1score from scholarships s
			left outer join (select scholarship_id, sum(rank) as p1score from rankings where criterion = 'valid' group by scholarship_id) r2 on s.id = r2.scholarship_id
			group by s.id, s.fname, s.lname, s.email
			having p1score < 3 and s.exclude = 0 and s.id>305" );
	}
}
