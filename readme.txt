=== WP Jobs2Careers API Integration ===
Contributors: marieayscue
Tags: jobs2careers, j2c, api, monetize, job, board, publisher, partner
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.0.0
License: GNU General Public License v3.0

Backfill and monetize your job board using the Jobs2Careers API. Jobs2careers jobs will be displayed in list format linking offsite (without full descriptions).

== Description ==

WP Jobs2Careers API Integration allows you to display Jobs2Careers jobs on your site using their API.

Features:

* Lightweight plugin with minimum configuration needed
* Responsive design
* The plugin also adds a [jobs2careersapi] shortcode for listing Jobs2Careers search results. This is useful if you want to display results from Jobs2Careers anywhere on your site. Jobs are retrieved for the parameters you define via the shortcode. 

The following shortcode parameters are supported:

* search — Keywords you want to search for (i.e. nursing)
* type - Job type to search for. Valid options are 1, 2, and 4. full time = 1, part time = 2, gigs = 4. By default all job types are queried.
* location — Location near which you want to search. If location is empty, it will attempt to geolocate the user's city/state. 

The following location options are acceptable:
* Zip Code (i.e. 27644)
* City (i.e. Raleigh)
* State (i.e. NC or North Carolina)
* City, State (i.e. Raleigh, NC)

	
For example, to search Jobs2Careers for nursing jobs of any job type around Raleigh, NC, you would use:

[jobs2careersapi search="nursing" location="Raleigh,NC"]

Requirements:

* Jobs2Careers API Key is required. This can be obtained at(http://www.jobs2careers.com/publisher_services.php)
* Jobs2Careers may serve a first-party tracking cookie to each user viewing the job site.
* The WP Jobs2Careers API Integration plugin will connect to http://www.geoplugin.net for geolocation.

== Installation ==

1. Upload ‘WP Jobs2Careers API Integration' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the settings section, include your publisher ID and password and input keyword and location placeholder text, and also set the default page to display jobs on.

== Changelog ==

= 1.0.0 =

* Initial release

