<?php

$submitted = false;
$lang = 'en';
$app = new Application();

if ( isset( $_GET['special'] ) ) {
	$special = true;
}

if ( isset( $_POST['submit'] ) ) {
	$app->submit( $_POST );
	if ( $app->success === true ) {
		$submitted = true;
	}
}

$defaultResponses = array(
	'fname'                    => '',
	'lname'                    => '',
	'email'                    => '',
	'telephone'                => '',
	'address'                  => '',
	'residence'                => '',
	'haspassport'              => 0,
	'nationality'              => '',
	'airport'                  => '',
	'languages'                 => '',
	'yy'                       => '',
	'sex'                      => '',
	'occupation'               => '',
	'areaofstudy'              => '',
	'wm05'                     => 0,
	'wm06'                     => 0,
	'wm07'                     => 0,
	'wm08'                     => 0,
	'wm09'                     => 0,
	'wm10'                     => 0,
	'wm11'                     => 0,
	'wm12'                     => 0,
	'wm13'                     => 0,
	'presentation'             => 0,
	'howheard'                 => '',
	'why'                      => '',
	'englishability'           => '',
	'username'                 => '',
	'project'                  => '',
	'projectlangs'             => '',
	'involvement'              => '',
	'contribution'             => '',
	'wantspartial'             => 0,
	'canpaydiff'               => 0,
	'sincere'                  => 0,
	'willgetvisa'              => 0,
	'willpayincidentals'       => 0,
	'agreestotravelconditions' => 0,
	'mm'                       => '',
	'dd'                       => '',
	'chapteragree'             => 1,
	'presentationTopic'        => '',
	'wmfAgreeName'             => '',
	'wmfagree'                 => 0
);

$twigCtx['submitted'] = $submitted;
$twigCtx['app'] = $app;
$twigCtx['mock'] = $mock;
$twigCtx['registration_open'] = time() > $open_time;
$twigCtx['registration_closed'] = time() > $close_time && !isset( $special );
$twigCtx['values'] = array_merge( $defaultResponses, $_POST );
asort( $COUNTRY_NAMES );
$twigCtx['countries'] = $COUNTRY_NAMES;

echo $twig->render( 'apply.html', $twigCtx );
