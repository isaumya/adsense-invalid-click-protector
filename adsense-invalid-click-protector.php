<?php
/*
Plugin Name: AdSense Invalic Click Protector
Plugin URI: https://www.isaumya.com/portfolio-item/wp-server-stats/
Description: A WordPress plugin to protect your AdSense ads from unusual click bombings and invalid clicks
Author: Saumya Majumder
Author URI: https://www.isaumya.com/
Version: 0.1
Text Domain: aicp
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
Copyright 2012-2016 by Saumya Majumder 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Define Constants */
define( 'AICP_BASE', plugin_basename(__FILE__) );

/* Let's handle the plugin setup */
register_activation_hook(   __FILE__, array( 'AICP_SETUP', 'on_activation' ) );
register_deactivation_hook( __FILE__, array( 'AICP_SETUP', 'on_deactivation' ) );
register_uninstall_hook(    __FILE__, array( 'AICP_SETUP', 'on_uninstall' ) );

add_action( 'plugins_loaded', array( 'AICP', 'get_instance' ) );
/* Main Class for AICP aka AdSense Invalic Click Protector */
class AICP {
	/*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/
  
    /** Refers to a single instance of this class. */
    private static $instance = null;
     
    /* Saved options */
    public $table_name;
  
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
  
    /**
     * Creates or returns an instance of this class.
     *
     * @return  AICP A single instance of this class.
     */
    public static function get_instance() {
  
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
  
        return self::$instance;
    } // end get_instance;
  
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() {
    	//Set the Table name
    	$this->table_name = $wpdb->prefix . 'adsense_invalid_click_protector';

    	// Let's load the setup file under /inc/ folder
    	add_action( current_filter(), array( $this, 'load_files' ), 30 );

    	// Let's load the styles and scripts now
    	add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
    }
  
    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/
      
    /**
     * Function to fetch visitor's IP address
     * @return Visitor's IP address
    **/
    public function visitor_ip() {
    	foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
	        if ( array_key_exists( $key, $_SERVER ) === true ) {
	            foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
	                $ip = trim( $ip ); // just to be safe

	                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
	                    return $ip;
	                }
	            }
	        }
	    }
    }

    /**
     * Function to fetch visitor's country
     * @return Visitor's Country Name & Country Code  (returns Array)
    **/
    public function visitor_country( $ip ) {
    	$locQuery = @unserialize( file_get_contents( 'http://ip-api.com/php/'.$ip ) );
    	if( $locQuery && $locQuery['status'] == 'success' ) {
			$visitor_country['name'] = $locQuery['country'];
			$visitor_country['code'] = $locQuery['countryCode'];
			return $visitor_country;
		} else {
			return false; //some problem happened with the IP API Call
		}
    }

    /**
     * Function to load the setup file
     * @return Nothing
    **/
    public function load_files() {
    	foreach ( glob( plugin_dir_path( __FILE__ ).'inc/*.php' ) as $file ) {
            include_once $file;
    	}
    }

    /**
     * Function to load styles & scripts
     * @return Nothing
    **/
    public function load_scripts() {
    	wp_enqueue_script( 'aicp', plugins_url( '/assets/js/aicp.js' , __FILE__ ) , array( 'jquery' ) );
    	$country_data = $this->visitor_country( $this->visitor_ip() );
    	wp_localize_script( 
    		'aicp', //id
    		'AICP', // The name using which data will be fetched at the JS side
    		array( 
	    		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	    		'nonce' => wp_create_nonce( "aicp_nonce" ),
	    		'ip' => $this->visitor_ip(),
	    		'countryName' => $country_data['name'],
	    		'countryCode' => $country_data['code']
    		) // all data that are being passed to the js file
    	);
    }

    /**
     * Function to process the data via the jQuery AJAX call
     * @return Nothing
    **/
    public function process_data() {
    	global $wpdb;
    	check_ajax_referer( 'aicp_nonce', 'nonce' );

    	//Let's grab the data from the AJAX POST request
    	$ip = sanitize_text_field( $_POST['ip'] );
    	$countryName = sanitize_text_field( $_POST['countryName'] );
    	$countryCode = sanitize_text_field( $_POST['countryCode'] );
    	$clickCount = sanitize_text_field( $_POST['click_count'] );

    	//Now it's time to insert the data into the database
    	$wpdb->insert( 
			$this->table_name, 
			array( 
				'ip' => $ip,
				'click_count' => $clickCount,
				'country_name' => $countryName,
				'country_code' => $countryCode,
				'timestamp' => current_time( 'mysql' )
			) 
		);
    }
} // AICP Class Ends

/**
 * Global PHP Function to check if the current visitor is
 * blocked from seeing the ads or not.
 * @return true (if the visitor IP is blocked) / false (otherwise)
**/

function aicp_can_see_ads() {
	global $wpdb;
	$aicpOBJ = new AICP();
	$visitorIP = $aicpOBJ->visitor_ip();

	// Checking if the visitor's IP is in our block list
	$match = $wpdb->get_var( "SELECT COUNT(id) FROM $aicpOBJ->table_name WHERE ip  = $visitorIP " );

	if( $match > 0 ) {
		return false; // No visitor cannot see ads as he is in our block list
	} else {
		return true; // Yes, he can
	}
}