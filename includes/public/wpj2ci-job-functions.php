<?php

//Shortcode, Search form and results function
function wpj2ci_search_results ( $atts, $content) {	
	
	// Initializing variables
	$wpj2ci_keywords = null; //keyword search
	$wpj2ci_location = null; //location
	$wpj2ci_jobs_per_page = 25; //how many jobs per page; TO DO: add shortcode and user input options
	$wpj2ci_type = null; //job type
	
	$wpj2ci_remote_address = $_SERVER['REMOTE_ADDR']; //user remote address; needed for geolocation
	
	//set shortcode default attributes
	$a = shortcode_atts( array(
        'search' => '',
        'location' => wpj2ci_geolocation ( $wpj2ci_remote_address ), //if no location in shortcode; geolocate user's city,state as default
		'type' => '',
    ), $atts );
	
	//TO DO: add shortcode and user options for jobs_per_page and radius;
	
	//Get shortcode values first; these will be overwritten if user inputs different values in the user search form. 
	$wpj2ci_keywords = sanitize_text_field($a['search']);
	$wpj2ci_location = sanitize_text_field($a['location']);
	$wpj2ci_type = sanitize_text_field($a['type']);
		
	//Get Admin Setting values / database options; these are used for the search form placeholder values and api url
	$wpj2ci_publisher_id = esc_attr(get_option('wpj2ci_publisher_id'));
	$wpj2ci_publisher_password = esc_attr(get_option('wpj2ci_publisher_password'));
	$wpj2ci_keywords_placeholder = esc_attr(get_option('wpj2ci_keywords_placeholder'));
	$wpj2ci_location_placeholder = esc_attr(get_option('wpj2ci_location_placeholder'));
					
	//check to see if user input keyword into the search form; if so overwrite shortcode search attribute
	if ( isset( $_GET['search'] ) ) {
		$wpj2ci_keywords = sanitize_text_field( $_GET['search'] );
	}
	
	//check to see if user input location into the search form; if so overwrite shortcode location attribute
	if ( isset( $_GET['location'] ) ) {
		//$wpj2ci_location = urlencode( stripslashes( $_GET['location'] ) );
		$wpj2ci_location = sanitize_text_field( $_GET['location'] );
	}
	
	//check to see if user input job type into the search form; if so overwrite shortcode type attribute
	if ( isset( $_GET['type'] ) ) {
		$wpj2ci_type = sanitize_text_field( $_GET['type'] );
		
		if ( 'full-time' == strtolower( $wpj2ci_type ) || '1' == $wpj2ci_type ) {
			$wpj2ci_type = 1;
		} elseif ( 'part-time' == strtolower( $wpj2ci_type ) || '2' == $wpj2ci_type ) {
			$wpj2ci_type = 2;		
		} elseif ( 'gigs' == strtolower( $wpj2ci_type )  || '4' == $wpj2ci_type ) {
			$wpj2ci_type = 4;
		} else	{
			$wpj2ci_type = '';
		}	
		
	}
	
	//Get current page no and set start / end record
	//needs to go before api request, to get correct start point
	$pageno = urlencode( stripslashes( $_GET['no'] ) );
			if (( ! isset( $_GET['no'] ) )) {
			$pageno = 1;	
			}
			
			$start = $wpj2ci_jobs_per_page *($pageno-1)+1;
			$end= $wpj2ci_jobs_per_page *($pageno);
			
			if ($start <1) {
			    $start = 1;
			    $end = $wpj2ci_jobs_per_page;
			}
			
	//Create the user front end search form
	$content .= "<form method='get' action=" . get_permalink() . "  name='wpj2ci_search_form' id='wpj2ci-search-form-display'  />";
	$content .= "<div class='wpj2ci-search-jobs row'>";
	$content .= "<div class='col-md-3' ><input type='text' placeholder='" . $wpj2ci_keywords_placeholder . "' name='search' id='wpj2ci-keywords' value='" . $wpj2ci_keywords . "' /></div>";
	$content .= "<div class='col-md-3'><input type='text' placeholder='" . $wpj2ci_location_placeholder . "' name='location'    id='wpj2ci-location' value='" . $wpj2ci_location . "' /></div>";
	$content .= "<div class='col-md-3' >";
	$content .= "<select name='type' id='wpj2ci-type'>";
	$content .= "<option value='' "  . selected( $wpj2ci_type, '', false  ) . ">Any Type</option>";		
	$content .= "<option value='full-time' "  . selected( $wpj2ci_type, '1', false  ) . ">Full Time</option>";
	$content .= "<option value='part-time' "  . selected( $wpj2ci_type, '2', false  ) . ">Part Time</option>";
	$content .= "<option value='gigs' "  . selected( $wpj2ci_type, '4', false  ) . ">Gigs</option>";	
	$content .= "</select>";	
	$content .= "</div>";
	$content .= "<div class='col-md-3'><input type='submit' value='Search'/></div>";
	$content .= "</div>";	
	$content .= "</form>";
	
	//Create the api url based on shortcode / search form values
   $apiurl  = "http://api.jobs2careers.com/api/search.php?";
   $apiurl .= "id=" . $wpj2ci_publisher_id;
   $apiurl .= "&pass=" . $wpj2ci_publisher_password;
   $apiurl .= "&q=" . $wpj2ci_keywords;
   $apiurl .= "&ip=127.0.0.1";
   $apiurl .= "&industry=";
   $apiurl .= "&l=" . $wpj2ci_location;
   $apiurl .= "&jobtype=" . $wpj2ci_type;
   $apiurl .= "&start=" . $start  ;
   $apiurl .= "&sort=";
   $apiurl .= "&link=1";
   $apiurl .= "&full_desc=";
   $apiurl .= "&limit=" . $wpj2ci_jobs_per_page;
   $apiurl .= "&format=json";
   
  //get api url content
  $request = wp_remote_get($apiurl);
  
//If there is an error with the request, bail
if( is_wp_error( $request ) ) {
	echo esc_html("Error");
	return false; // Bail early; put more error logic here so customer knows error encountered.
}

//get body of the request
$body = wp_remote_retrieve_body( $request );

//decode the json
$data = json_decode( $body );

//if jobs2careers api returns an error status message, echo it and exit; TO DO: better error display to the user
if ($data->status){ 
	echo esc_html("<br/>" . $data->message);
	return false;
}

//If jobs2careers api does not return an error status message, loop through the results and display them
if ( ! empty( $data ) ) {
	
//Display total pages found and get # of pages to create paging
$total_jobs     = absint( $data->total);
$content .=  esc_html( "Total Jobs Found: " . $total_jobs . ". "); 
$total_pages = ceil( $total_jobs / $wpj2ci_jobs_per_page );
$content .=  esc_html("Showing Jobs " . $start . " to " . $end);

//Build paging url
$pagingurl .= "&search=" . $wpj2ci_keywords; 
$pagingurl .= "&jobs_per_page=" . $wpj2ci_jobs_per_page;
$pagingurl .= "&type=" . $wpj2ci_type; 
$pagingurl .= "&location=" . urlencode($wpj2ci_location); 
	
	foreach( $data->jobs as $job ) {
		    $content .=  esc_html('<div class="row wpj2ci-listing">');
		    $content .=  esc_html('<div class="col-sm-12">');
			$content .=  esc_html('<div class="wpj2ci-listing-title"><a href="' . esc_url( $job->url ) . '" target="_blank" class="wpj2ci-listing-title">' . $job->title . '</a></div>');
			$content .=  esc_html('<div class="wpj2ci-listing-company">' . $job->company . '</div>');
			$content .=  esc_html('<div class="wpj2ci-listing-description">' . $job->description) . '[...]' . '</div>';
			$content .=  esc_html('<div class="wpj2ci-listing-date">Date Posted:');
		    $content .=  esc_html(date( 'n/d/Y', strtotime( $job->date ) ));
		    $content .=  esc_html('</div>');
			$content .=  esc_html('<div class="wpj2ci-listing-locations">Location(s):');
			$ci = 0; //current index in city array
			$c = count($job->city); //total count of the city array
			foreach ($job->city as $city){
					
					$content .=  esc_html($city);
					if ($ci++ < $c - 1) {
					$content .=  esc_html(" | "); //do not append seperator to the last item in array	
					}
					
			}
			
		   $content .=  esc_html('</div>');
		  
		   $content .=  esc_html('</div>');
		   $content .=  esc_html('</div>');
		  
	}
	
}
     		//page numbers; TO DO: better paging
			$content .=  esc_html( "Total Jobs Found: " . $total_jobs . ". " ); 
			$content .=  esc_html("Showing Jobs " . $start . " to " . $end );
			
			$content .= "<p>Select a page #: ";
			for ($i = 1; $i <= $total_pages; $i++) {
				if ($i == 1) {
				  $startpage = 1;	
				}
				else {
					 $startpage = (($i-1) * $wpj2ci_jobs_per_page + 1);
				}
			     $content .=  esc_html("<a href=?no=" . $i .  "&start=" . $startpage . $pagingurl . ">" . $i . "</a> ");
    		
			} 
			

			$content .=  esc_html("</p>");
			return $content;

}

//Geo Location; Geo location is used if shortcode attribute location is not set
function wpj2ci_geolocation ( $wpj2ci_remote_address ) {

	$wpj2ci_geo_contents = null;
	$wpj2ci_geo_url = "http://www.geoplugin.net/php.gp?ip=$wpj2ci_remote_address";
	$wpj2ci_geo = curl_init( $wpj2ci_geo_url );
	curl_setopt( $wpj2ci_geo, CURLOPT_URL, $wpj2ci_geo_url );
	curl_setopt( $wpj2ci_geo, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $wpj2ci_geo, CURLOPT_TIMEOUT, 10 );
	$wpj2ci_geolocation_data = curl_exec( $wpj2ci_geo );
	$wpj2ci_geo_header_status = curl_getinfo( $wpj2ci_geo, CURLINFO_HTTP_CODE );
	curl_close( $wpj2ci_geo );
	
	if ( 200 === $wpj2ci_geo_header_status)  {
		$wpj2ci_geolocation_data = unserialize($wpj2ci_geolocation_data );
		$wpj2ci_geolocation_county_code = $wpj2ci_geolocation_data['geoplugin_countryCode'];
		$wpj2ci_geolocation_city = $wpj2ci_geolocation_data['geoplugin_city'];
		
		// If within U.S., format is CITY, STATE. If outside the U.S., format is CITY, COUNTRY.
		if ( 'us' == strtolower( $wpj2ci_geolocation_county_code ) ) {
			$wpj2ci_geolocation_region = $wpj2ci_geolocation_data['geoplugin_regionCode'];
			//echo "city: " . $wpj2ci_geolocation_city . ', ' . $wpj2ci_geolocation_region;
			if ((!empty($wpj2ci_geolocation_city)) && (!empty($wpj2ci_geolocation_region))) {
			return $wpj2ci_geolocation_city . ', ' . $wpj2ci_geolocation_region; 
			} else {
			return 'New York, NY';
			
		    }
			
		} else {
			return 'New York, NY';
			
		}
	
	} else {
		//echo "Geo not available. Header status is " . $wpj2ci_geo_header_status;
		return 'New York, NY';
	}	

}

// load CSS
function wpj2ci_enqueue () {
	
	wp_enqueue_style( 'wpj2ci_styling', plugins_url( '../assets/css/wpj2ci-style.css', dirname( __FILE__ ) ) );	
					
	
}

?>