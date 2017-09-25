<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'wpj2ci_publisher_id',
	'wpj2ci_publisher_password',
	'wpj2ci_keywords_placeholder',
	'wpj2ci_location_placeholder'
);

foreach ( $options as $option ) {
	delete_option( $option );
}

?>