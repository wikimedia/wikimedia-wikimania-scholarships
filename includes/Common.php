<?php

if ( !function_exists( 'load_class' ) ) {

	function &load_class( $class, $directory ) {

		static $_classes = array();

		if ( isset( $_classes[$class] ) ) {
			return $_classes[$class];
		}

		$name = false;
	}
}
