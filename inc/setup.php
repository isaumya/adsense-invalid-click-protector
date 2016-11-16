<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $aicp_db_ver;
$aicp_db_ver = '1.0';

if( ! class_exists( 'AICP_SETUP' ) ) {
	class AICP_SETUP {
	    public static function on_activation() {
	        if ( ! current_user_can( 'activate_plugins' ) )
	            return;
	        global $wpdb;
	        global $aicp_db_ver;

			$table_name = $wpdb->prefix . 'adsense_invalid_click_protector';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id bigint NOT NULL AUTO_INCREMENT,
				ip varchar(39) NOT NULL,
				click_count int NOT NULL,
				country_name varchar(100) NOT NULL,
				country_code varchar(5) NOT NULL,
				timestamp datetime NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			// Let's execute the sql and create the table now
			dbDelta( $sql );
			//Lets save our database option
			add_option( 'aicp_db_ver', $aicp_db_ver );
			//Creating the scheduled job to delete stuffs which is more than 7 days old
			if ( ! wp_next_scheduled ( 'aicp_hourly_cleanup' ) ) {
				wp_schedule_event( time(), 'hourly', 'aicp_hourly_cleanup' );
		    }
	    }

	    public static function on_uninstall() {
	        if ( ! current_user_can( 'activate_plugins' ) )
	            return;
	        global $wpdb;
		    $table_name = $wpdb->prefix . 'adsense_invalid_click_protector';
		    $sql = 'DROP TABLE IF EXISTS ' . $table_name;
		    $wpdb->query($sql);

		    unregister_setting( 'aicp_settings', 'aicp_settings_options' );
		    
		    delete_option('aicp_db_ver');

		    wp_clear_scheduled_hook('aicp_hourly_cleanup');
	    }
	} //end of class AICP_SETUP
} //ed of checking if AICP_SETUP exists