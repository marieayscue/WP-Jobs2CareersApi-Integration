<?php

function wpj2ci_admin_menu() {
	//Create the admin menue
   add_submenu_page( "options-general.php",  // add Jobs2Careers API options to Settings main menu
                  "Jobs2Careers API Settings",  // Page title
                  "Jobs2Careers API",            // Menu title
                  "manage_options",       // Minimum capability (manage_options is an easy way to target administrators)
                  "jobs2careersapi",            // Menu slug
                  "wpj2ci_admin_settings"     // Callback that prints the admin form markup
               );
}

// Print the admin form markup for the page
function wpj2ci_admin_settings() {
	//If the current user does not have sufficient permissions, tell them so
   if ( !current_user_can( "manage_options" ) )  {
      wp_die( __( "You do not have sufficient permissions to access this page." ) );
   }
   
 ?>
 
 <?php 
  //Display status message
	  if ( isset($_GET['status'])) { 
	?>
		<div id="message" class="updated notice is-dismissible">
			<p><?php echo $_GET['status'] ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
	<?php
	}
	?>
 
<form method="post" action="<?php echo admin_url( 'admin-post.php'); ?>">

   <input type="hidden" name="action" value="update_jobs2careersapi_settings" />

  <p><?php _e( '<h3>Jobs2Careers Publisher Settings</h3>' ); ?><p>
   <p>
   <label class="" for="wpj2ci_publisher_id"><?php _e( 'Publisher Id:' ); ?></label>
   <input class="" type="text" name="wpj2ci_publisher_id" value="<?php echo esc_attr(get_option('wpj2ci_publisher_id')); ?>" />
   </p>

   <p>
   <label class="" for="wpj2ci_publisher_password"><?php _e( 'Publisher Password:' ); ?></label>
   <input class="" type="text" name="wpj2ci_publisher_password" value="<?php echo esc_attr(get_option('wpj2ci_publisher_password')); ?>" />
   </p>

   <p><?php _e( '<h3>Display Settings</h3>' ); ?><p>
  
   <p>
   <label class="" for="wpj2ci_keywords_placeholder"><?php _e( 'Placeholder for keyword(s):' ); ?></label>
   <input class="" type="text" name="wpj2ci_keywords_placeholder" value="<?php echo esc_attr(get_option('wpj2ci_keywords_placeholder')); ?>" />
   </p>
   
   <p>
   <label class="" for="wpj2ci_location_placeholder"><?php _e( 'Placeholder for location:' ); ?></label>
   <input class="" type="text" name="wpj2ci_location_placeholder" value="<?php echo esc_attr(get_option('wpj2ci_location_placeholder')); ?>" />
   </p>
   
   <p class="description">Placeholder text will be displayed if field is blank. The placeholder is not a value, rather, an example of what users should enter (i.e. <strong>Enter Keyword(s)</strong>).</p>
   
   <input class="button button-primary" type="submit" value="Save Settings" />

</form>
<?php

}

//Error check and save admin options
add_action( 'admin_post_update_jobs2careersapi_settings', 'wpj2ci_admin_settings_save' );

function wpj2ci_admin_settings_save() {

   // Get the options that were sent
   $wpj2ci_publisher_id = sanitize_text_field( $_POST['wpj2ci_publisher_id'] );
   $wpj2ci_publisher_password = sanitize_text_field( $_POST['wpj2ci_publisher_password'] );
   $wpj2ci_keywords_placeholder = sanitize_text_field( $_POST['wpj2ci_keywords_placeholder'] );
   $wpj2ci_location_placeholder = sanitize_text_field( $_POST['wpj2ci_location_placeholder'] );
   $wpj2ci_default_page = sanitize_text_field( $_POST['wpj2ci_default_page'] );
   
   // Admin form validation
   if (empty($wpj2ci_publisher_id)) {
	  $message = "<br/>Publisher ID is required."; 
   }
   if (empty($wpj2ci_publisher_password)) {
	  $message .= "<br/>Publisher Password is required."; 
   }
   
   if (!$message) {
	  $message = "Settings Saved Successfully!"; 
	  $status = "success";
   }

//only update admin values if no submission errors exist
   if ($status == 'success') {
   // Update the values
   update_option("wpj2ci_publisher_id", $wpj2ci_publisher_id, TRUE );
   update_option("wpj2ci_publisher_password", $wpj2ci_publisher_password, TRUE);
   update_option("wpj2ci_keywords_placeholder", $wpj2ci_keywords_placeholder, TRUE );
   update_option("wpj2ci_location_placeholder", $wpj2ci_location_placeholder, TRUE);
   update_option("wpj2ci_default_page", $wpj2ci_default_page, TRUE);
   
  
   }

   // Redirect back to settings page
   // The ?page=jobs2careersapi corresponds to the "slug" 
   // set in the fourth parameter of add_submenu_page() above.
   //$redirect_url = get_bloginfo("url") . "/wp-admin/options-general.php?page=jobs2careersapi&status=success";
   $redirect_url = get_bloginfo("url") . "/wp-admin/options-general.php?page=jobs2careersapi&status=" . $message;
   header("Location: ".$redirect_url);
   exit;
}






?>