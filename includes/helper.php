<?php

function YearsOld($date) {
        $start = new DateTime($date);
        $diff = $start->diff( new DateTime( strftime( '%Y-%m-%d' ) ) );
        return $diff->format('%Y');
}

function Sex($sex) {
        global $wgLang;
        switch ($sex) {
                case 'm':
                        return $wgLang->message('form-gender-male');
                case 'f':
                        return $wgLang->message('form-gender-female');
                case 't':
                        return $wgLang->message('form-gender-transgender');
                default:
                        return $wgLang->message('form-gender-unspecified');
        }
}

function GetScholarshipId() {
        $ret = '';

        if ($_POST['last_id'] > 0)
        $ret = $_POST['last_id'];
        else if ($_GET['id'] > 0)
        $ret = $_GET['id'];

        return $ret;
}

function RankDropdownList($criterion,$scholarship_id) {
        global $user_id;
        $dal = new DataAccessLayer();
        $rank = $dal->getRankingOfUser($user_id, $scholarship_id, $criterion);
        $ret = sprintf('<select id="%s" name="%s">', $criterion, $criterion);

	$numopt = 4;	
	if ( in_array( $criterion, array( 'valid', 'program' ) ) ) {
		$numopt = 1;
	}

        for ($i = $numopt; $i >= 0; $i--) {
	        $ret .= sprintf('<option value="%d"%s>%d</option>',
        		$i, $i == $rank ? ' selected="selected"' : '', $i);
	}

        $ret .= '</select>';
        return $ret;
}

?>
