<?php

$columns = array(
	'fname' => array(
		'type' => 'varchar',
		'length' => 255,
		'null' => TRUE,
		'default' => NULL
	),
	'lname' => array(
		'type' => 'varchar',
		'length' => 255,
		'null' => TRUE,
		'default' => NULL
	),
	'email' => array(
		'type' => 'varchar',
		'length' => 255,
		'null' => TRUE,
		'default' => NULL
	),
	'telephone' => array(
		'type' => 'varchar',
		'length' => 16,
		'null' => TRUE,
		'default' => NULL
	),
	'address' => array(
		'type' => 'text',
		'length' => NULL,
		'null' => TRUE,
		'default' => NULL
	),
	'residence' => 	array(
		'type' => 'varchar',
		'length' => 255,
		'null' => TRUE,
		'default' => NULL
	),
	'nationality' => array(
		'type' => 'varchar',
		'length' => 255,
		'null' => TRUE,
		'default' => NULL
	),
	'haspassport' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'passportnum' => array(
		'type' => 'varchar',
		'length' => 128,
		'null' => TRUE,
		'default' => NULL
	),
	'airport' => array(
		'type' => 'varchar',
		'length' => 64,
		'null' => TRUE,
		'default' => NULL
	),
	'languages' => 	array(
		'type' => 'varchar',
		'length' => 64,
		'null' => TRUE,
		'default' => NULL
	),
	'dob' => array(
		'type' => 'date',
		'null' => TRUE,
		'default' => NULL
	),
	'sex' => array(
		'type' => 'enum',
		'options' => array( 'm', 'f', 'd' ),
		'null' => TRUE,
		'default' => NULL
	),
	'occupation' => array(
		'type' => 'varchar',
		'length' => 64,
		'null' => TRUE,
		'default' => NULL
	),
	'areaofstudy' => array(
		'type' => 'varchar',
		'length' => 64,
		'null' => TRUE,
		'default' => NULL
	),
	'wm05' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm06' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm07' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm08' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm09' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm10' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm11' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'wm12' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'presentation' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => TRUE,
		'default' => NULL
	),
	'presentationTopic' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'howheard' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'why' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'future' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'englishAbility' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'username' => array(
		'type' => 'varchar',
		'length' => 64,
		'null' => TRUE,
		'default' => NULL
	),
	'project' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'projectlangs' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'involvement' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'contribution' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'wantspartial' => array(
		'type' => 'tinyint',
		'null' => TRUE,
		'default' => NULL
	),
	'canpaydiff' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'sincere' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'agreestotravelconditions' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'willgetvisa' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'willpayincidentals' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'chapteragree' => array(
		'type' => 'tinyint',
		'null' => FALSE,
		'default' => 0
	),
	'wmfAgreeName' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'rank' => array(
		'type' => 'int',
		'length' => 11,
		'null ' => FALSE,
		'default' => 0
	),
	'notes' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'exclude' => array(
		'type' => 'text',
		'null' => TRUE,
		'default' => NULL
	),
	'confirmed' => array(
		'type' => 'tinyint',
		'length' => 1,
		'null' => FALSE,
		'default' => 0
	),
	'confhash' => array(
		'type' => 'varchar',
		'length' => 8,
		'null' => TRUE,
		'default' => NULL
	),
	'entered_on' => array(
		'type' => 'timestamp',
		'null' => FALSE,
		'default' => 'CURRENT_TIMESTAMP'
	),
	'ipaddr' => array(
		'type' => 'varbinary',
		'null' => TRUE,
		'default' => '127.0.0.1'
	)
);
