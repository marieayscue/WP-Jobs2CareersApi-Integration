<?php

/**
Plugin Name: WP Jobs2Careers API Integration
Plugin URI: https://github.com/marieayscue/WP-Jobs2Careers-Api
Description: Backfill your job site using the Jobs2Careers API
Version: 1.0.0
Author: Marie Ayscue
Author URI: http://AnnMarieAyscue.com/
License: GPLv2 or later
Text Domain: wpj2ci
*/

defined( 'ABSPATH' ) or die( 'No scripts please!' );


/** TO DO: use transients to keep track of updates **/

/******************
* Files to include
*******************/
if ( is_admin() ) {
include( 'includes/admin/wpj2ci-admin-functions.php' ); //admin page form markup and save function
}
include( 'includes/public/wpj2ci-job-functions.php' ); //job display functions

/******************
* Create Shortcode
*******************/
add_shortcode( "jobs2careersapi", "wpj2ci_search_results" ); //see includes/public/wpj2ci-job-functions.php

/******************
* Hooks
*******************/

// Register the admin menu.
add_action( "admin_menu", "wpj2ci_admin_menu" );
// Save admin settings
add_action( 'admin_post_update_jobs2careersapi_settings', 'jobs2careersapi_handle_save' );
//CSS
add_action( 'wp_enqueue_scripts', 'wpj2ci_enqueue' );

/******************
* Initialize DB
*******************/

//Set default admin values on plugin activation
register_activation_hook( __FILE__, 'wpj2ci_set_default_db_values' );

function wpj2ci_set_default_db_values () {

	$o = array(
        'wpj2ci_publisher_id'            => '',
        'wpj2ci_publisher_password'      => '',
        'wpj2ci_keywords_placeholder'    => 'Enter keyword(s)',
        'wpj2ci_location_placeholder'    => 'Enter location',
		'wpj2ci_default_page'    => '',
        
    );

    foreach ( $o as $k => $v )
    {
        update_option($k, $v);
    }

}



/*//If the plugin has been activated display an admin notice that publisher id, password, and default page are required.
if( function_exists( 'wpj2ci_jobs2careers_shortcode' ) ) {
  add_action( 'admin_notices', 'wpj2ci_setup_admin' );
}

function wpj2ci_setup_admin() {
  if ((!$wpj2ci_publisher_id) || (!$wpj2ci_publisher_password) || (!$wpj2ci_default_page)) {
  echo "<div class='update-nag notice'>";
      echo "<p>Please ensure that you have input your Jobs2Careers Publisher ID, Publisher Password, and Selected A Default Display Page.</p>";
  echo "</div>";
  }
}*/

