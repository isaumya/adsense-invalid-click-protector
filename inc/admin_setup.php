<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Requiring the persistent notice remaval code
if( ! class_exists( 'AICP_BANNED_USER_TABLE' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'banned_user_table.php';
}
if( ! class_exists( 'AICP' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '../adsense-invalid-click-protector.php';
}
if( ! class_exists( 'AICP_ADMIN' ) ) {
	class AICP_ADMIN {

		/* Let's declare some variables that we are going to use all around our code to fetch data 
		 * As we are going to this variables at variour part of our code we are using public statement instead of protected
		**/
		public $click_limit, $click_counter_cookie_exp, $ban_duration, $country_block_check, $ban_country_list;

		/**
	     * Function to load CSS & JS files at the admin side
	     * @return Nothing
	    **/
	    public function admin_scripts() {
	    	/* CSS Calls */
			wp_enqueue_style('aicp-admin-interface', AICP_DIR_URL . 'assets/css/aicp-admin-interface.css', array(), '1.0.0');
	    }

	    /**
	     * Function to call the admin dashboard widget
	     * @return Nothing
	    **/
	    public function aicp_dashboard() {
	    	wp_add_dashboard_widget( 'aicp_status_dashboard', 'AICP Blocked User Statistics', array ( $this, 'dashboard_output' ) );
	    }

	    /**
	     * Function to show up dashboard with the blocking data at the admin dashboard
	     * @return Nothing
	    **/
	    public function dashboard_output() {
	    	if ( current_user_can( 'manage_options' ) ) :
	    		global $wpdb;
	    		$aicpOBJ = new AICP();
	    		$countQueryAll = "SELECT COUNT(id) FROM " . $aicpOBJ->table_name;
	    		$countAll = $wpdb->get_var( $countQueryAll );
	    		$countQuery24 = "SELECT COUNT(id) FROM " . $aicpOBJ->table_name . " WHERE " . $aicpOBJ->table_name . ".timestamp >= DATE_SUB( NOW(), INTERVAL 24 HOUR )";
	    		$count24 = $wpdb->get_var( $countQuery24 );
	    		$countQuery6 = "SELECT COUNT(id) FROM " . $aicpOBJ->table_name . " WHERE " . $aicpOBJ->table_name . ".timestamp >= DATE_SUB( NOW(), INTERVAL 6 HOUR )";
	    		$count6 = $wpdb->get_var( $countQuery6 );
	    	?>
	    		<h4 class="aicp-head-text"><?php _e( 'Total no. of Blocked Users', 'aicp' );  ?></h4>
	    		<h1 class="aicp_total_blocked_user"><?php echo $countAll; ?></h1>
	    		<hr />
	    		<h4 class="aicp-head-text aicp_mt"><?php _e( 'Blocked Users in last 24 hrs', 'aicp' );  ?></h4>
	    		<h1 class="aicp_blocked_user"><?php echo $count24; ?></h1>
	    		<hr />
	    		<h4 class="aicp-head-text aicp_mt"><?php _e( 'Blocked Users in last 6 hrs', 'aicp' );  ?></h4>
	    		<h1 class="aicp_blocked_user"><?php echo $count6; ?></h1>
	    		<div class="aicp_show_buttons content-center">
					<a href="<?php echo get_admin_url(); ?>admin.php?page=aicp_banned_user_details" title="Check All Banned User Details" class="aicp_btn button button-small"><?php _e( 'Check All Banned User Details', 'aicp' ); ?></a>
				</div>
	    	<?php
	    	endif;
	    }

	    /**
	     * Function to create admin menu for AdSense Invalid Click Protector menu
	     * @return Nothing
	    **/
	    public function create_admin_menu() {
	    	add_menu_page( 
	    		__( 'AdSense Invalid Click Protector', 'aicp' ), 
	    		__( 'AdSense Invalid Click Protector', 'aicp' ), 
	    		'manage_options', 
	    		'aicp_settings', 
	    		'', 
	    		'dashicons-shield', 
	    		81
	    	);

	    	add_submenu_page( 
	    		'aicp_settings', 
	    		__( 'AdSense Invalid Click Protector - General Settings', 'aicp' ), 
	    		__( 'General Settings', 'aicp' ), 
	    		'manage_options', 
	    		'aicp_settings', 
	    		array( $this, 'admin_settings_page' )
	    	);

	    	add_submenu_page( 
	    		'aicp_settings', 
	    		__( 'AICP - Banned User Details', 'aicp' ), 
	    		__( 'Banned User Details', 'aicp' ), 
	    		'manage_options', 
	    		'aicp_banned_user_details', 
	    		array( $this, 'banned_user_details' )
	    	);
	    }

	    /**
	     * Function to show the admin notices for both error and welcome notice after installing the plugin
	     * @return Nothing
	    **/
	    public function show_admin_notice() {
	    	settings_errors( 'aicp_settings_options' );
			//Making sure the following welcome notice doesn't show up after closing it
	    	if( ! PAnD::is_admin_notice_active( 'aicp-donate-notice-forever' ) ) {
	    		return;
	    	}
	    	$class = 'notice notice-success is-dismissible donate_notice';
	    	$message = sprintf( 
	    		__('%1$sThank you%2$s for installing %1$sAdSense Invalid Click Protector%2$s. It took countless hours to code, design and test to make this plugin a reality. But as this is a <strong>free plugin</strong>, all of these time and effort does not generate any revenue. Also as I\'m not a very privileged person, so earning revenue matters to me for keeping my lights on and keep me motivated to do the work I love. %3$s So, if you enjoy this plugin and understand the huge effort I put into this, please consider %1$s%4$sdonating some amount%5$s (no matter how small)%2$s for keeping aliave the development of this plugin. Thank you again for using my plugin. Also if you love using this plugin, I would really appiciate if you take 2 minutes out of your busy schedule to %1$s%6$sshare your review%7$s%2$s about this plugin.', 'wp-server-stats'),
	    		'<strong>', '</strong>',
	    		'<br /> <br />',
	    		'<a href="https://goo.gl/6qrufe" target="_blank" rel="external" title="WP Server Stats - Plugin Donation">', '</a>',
	    		'<a href="https://wordpress.org/support/plugin/wp-server-stats/reviews/" target="_blank" rel="external" title="WP Server Stats - Post your Plugin Review">', '</a>'
	    		);
	    	printf( '<div data-dismissible="aicp-donate-notice-forever" class="%1$s"><p>%2$s</p></div>', $class, $message );
	    }

	    /**
	     * Function to show admin settings page
	     * @return Nothing
	    **/
	    public function admin_settings_page() {
	    	/* Now lets do the admin page design */
	    	?>
	    	<div class="wrap">
	    	<h1><?php _e( 'AdSense Invalid Click Protector Settings', 'aicp' ); ?></h1>
	    		<h3><?php _e( 'On this page you will be able to change some critical settings of AdSense Invalid Click Protector a.k.a AICP', 'aicp' ); ?></h3>
	    		<h4><?php _e( 'Please note the below form uses HTML5, so, make sure you are using any of the HTML5 compliance browsers like IE v11+, Microsoft Edge, Chrome v49+, Firefix v47+, Safari v9.1+, Opera v39+', 'aicp' ); ?></h4>
	    		<hr />
	    		<div id="aicp-main">
	    			<form action="options.php" method="post" accept-charset="utf-8">
	    				<?php
						//Populate the admin settings page using WordPress Settings API
	    				settings_fields('aicp_settings');      
	    				do_settings_sections('aicp_settings');
	    				submit_button();

	    				//print_r( get_option( 'aicp_settings_options' ) );
	    				?>
	    			</form>
	    		</div>
	    		<div id="aicp-sidebar">
	    			<h2>
	    				<?php
	    				_e('Video Demonstration About the Plugin Usage', 'aicp');
	    				?>
	    			</h2>
	    			<p text-align="justify"><?php printf( __( 'Hi there, %1$splease take a look at the detailed video demonstration below%2$s where I\'ve explained everything about how the plugin works and it\'s various settings. Before actually start using this plugin, I will highly recommend you to spend some time to watch the video for once. It will make everything clear. If you still got any question, fell free to ask then in the %3$sWordPress support Forum%4$s.', 'aicp' ), '<strong><em>', '</em></strong>', '<a href="#" rel="external nofollow" target="_blank">', '</a>'  );?></p>
	    			<hr />
	    			<h2><?php _e('Support the plugin', 'aicp'); ?></h2>
	    			<p><?php _e('Believe it or not, developing a WorPress plugin really takes quite a lot of time to develop, test and to do continuous bugfix. Moreover as I\'m sharing this plugin for free, so all those times I\'ve spent coding this plugin yeild no revenue. So, overtime it become really hard to keep spending time on this plugin. So, if you like this plugin, I will really appriciate if you consider donating some amount for this plugin. Which will help me keep spending time on this plugin and make it even better. Please donate, if you can.', 'aicp'); ?></p>
	    			<div class="content-center">
	    				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	    					<input type="hidden" name="cmd" value="_donations">
	    					<input type="hidden" name="business" value="saumya0305@gmail.com">
	    					<input type="hidden" name="lc" value="US">
	    					<input type="hidden" name="item_name" value="Plugin Donation - AdSense Invalid Click Protector">
	    					<input type="hidden" name="no_note" value="0">
	    					<input type="hidden" name="currency_code" value="USD">
	    					<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
	    					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	    					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	    				</form>
	    			</div>
	    		</div>
	    	</div>
	    	<?php
	    }

	    /**
	     * Function to register the admin settings page via WP SETTINGS API
	     * @return Nothing
	    **/
	    public function register_page_options() {
	    	// Add Section for option fields
			add_settings_section( 'aicp_section', __( 'Change the AdSense Invalid Click Protector Settings', 'aicp' ), array( $this, 'display_section' ), 'aicp_settings' ); // id, title, display cb, page

			// Add Field for the Click Limit
			add_settings_field( 'aicp_click_limit', __( 'Set the Ad Click Limit', 'aicp' ), array( $this, 'click_limit_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

			// Add Field for the Cookie Expiration of Click Counter
			add_settings_field( 'aicp_click_cookie_expiration', __( 'Click Counter Cooke Expiration Time (default: 3 hours)', 'aicp' ), array( $this, 'click_cookie_expiration' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

			// Add Field for the Ban Duration (in days)
			add_settings_field( 'aicp_ban_duration', __( 'Set the Visitor Ban Duration (default: 7 days)', 'aicp' ), array( $this, 'ban_duration_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

			// Add Field for checking if the user wanna ban any specific country
			add_settings_field( 'aicp_country_block_check', __( 'Do you want to block showing ads for some specific countries?', 'aicp' ), array( $this, 'country_block_check_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

			// Add Field for selecting countries for which you wanna ban ads
			add_settings_field( 'aicp_country_list', __( 'Banned Country List - Put ISO ALPHA-2 Country Codes (Comma Seperated)', 'aicp' ), array( $this, 'country_list_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

			// Register Settings
			register_setting( 'aicp_settings', 'aicp_settings_options', array( $this, 'validate_options' ) ); // option group, option name, sanitize cb 
	    }

	    /**
		 * Callback function for settings section
		**/
		public function display_section() { /* Leave blank */ }

		/**
		 * Callback function for showing the click limit field
		**/
		public function click_limit_field() {
			$this->fetch_data();
			echo '<input type="number" name="aicp_settings_options[click_limit]" value="' . $this->click_limit . '" />';
		}

		/**
		 * Callback function for showing the click counter cookie expiration field
		**/
		public function click_cookie_expiration() {
			$this->fetch_data();
			echo '<input type="number" name="aicp_settings_options[click_counter_cookie_exp]" value="' . $this->click_counter_cookie_exp . '" /><span>   ' . __( 'Hour/s', 'aicp' ) . '</span>';
		}

		/**
		 * Callback function for showing the ban duration field
		**/
		public function ban_duration_field() {
			$this->fetch_data();
			echo '<input type="number" name="aicp_settings_options[ban_duration]" value="' . $this->ban_duration . '" /><span>   ' . __( 'Day/s', 'aicp' ) . '</span>';
		}

		/**
		 * Callback function for showing the country ban check field
		**/
		public function country_block_check_field() {
			$this->fetch_data();
			$options = get_option( 'aicp_settings_options' );
			?>
			<input type="radio" name="aicp_settings_options[country_block_check]" value="Yes" <?php checked( empty( $options['country_block_check'] ) ? $this->country_block_check : $options['country_block_check'], 'Yes' ) ?> /> 
			<span><?php _e( 'Yes', 'aicp' ); ?></span>
			
			<input type="radio" name="aicp_settings_options[country_block_check]" value="No" <?php checked( empty( $options['country_block_check'] ) ? $this->country_block_check : $options['country_block_check'], 'No' ) ?> /> 
			<span><?php _e( 'No', 'aicp' ); ?></span>
			<?php
		}

		/**
		 * Callback function for showing the list of countries
		**/
		public function country_list_field() {
			$this->fetch_data();
			$options = get_option( 'aicp_settings_options' );
			?>
			<input type="text" name="aicp_settings_options[ban_country_list]" value="<?php echo $this->ban_country_list; ?>" />
			<span>
			<?php 
				printf( 
					__('Enter the country codes for which you don\'t wanna show your ads. %1$sProvide ISO ALPHA-2 Country Codes%2$s seperated by comma %3$s. You can find the %1$sISO ALPHA-2 Country Codes%2$s on %4$sthis website%5$s.', 'aicp'), 
					'<strong>', '</strong>',
					'<code>,</code>',
					'<a href="http://www.nationsonline.org/oneworld/country_code_list.htm" target="_blank" rel="external nofollow">', '</a>'
				);
			?>
			</span>
			<?php
		}

		/**
		 * Callback function for validating the inputes
		**/
		public function validate_options( $fields ) {
			$this->fetch_data();
			$valid_fields = array();

			$valid_fields['click_limit'] = strip_tags( stripslashes( trim( $fields['click_limit'] ) ) );

			if( $valid_fields['click_limit'] < 1 || ( is_numeric( $valid_fields['click_limit'] ) === FALSE ) ) {
				$valid_fields['click_limit'] = $this->click_limit;
				// Set the error message
				add_settings_error( 'aicp_settings_options', 'aicp_click_limit_error', __( 'The minimum number of click limit must needs to be more than or equals to 1 and the entered value must be a number', 'aicp' ), 'error' ); // $setting, $code, $message, $type
			}

			$valid_fields['click_counter_cookie_exp'] = strip_tags( stripslashes( trim( $fields['click_counter_cookie_exp'] ) ) );

			if( $valid_fields['click_counter_cookie_exp'] < 1 || ( is_numeric( $valid_fields['click_counter_cookie_exp'] ) === FALSE ) ) {
				$valid_fields['click_counter_cookie_exp'] = $this->click_counter_cookie_exp;
				// Set the error message
				add_settings_error( 'aicp_settings_options', 'aicp_click_counter_cookie_exp_error', __( 'The click counter cookie expiration time must be a number and cannot be less than 1 hour', 'aicp' ), 'error' ); // $setting, $code, $message, $type
			}

			$valid_fields['ban_duration'] = strip_tags( stripslashes( trim( $fields['ban_duration'] ) ) );

			if( $valid_fields['ban_duration'] < 1 || ( is_numeric( $valid_fields['ban_duration'] ) === FALSE ) ) {
				$valid_fields['ban_duration'] = $this->ban_duration;
				// Set the error message
				add_settings_error( 'aicp_settings_options', 'aicp_ban_ducation_error', __( 'The user ban duration must needs to be more than or equals to 1 day & the entered value must be a number', 'aicp' ), 'error' ); // $setting, $code, $message, $type
			}

			$valid_fields['country_block_check'] = strip_tags( stripslashes( trim( $fields['country_block_check'] ) ) );

			if( !( $valid_fields['country_block_check'] == 'Yes' || $valid_fields['country_block_check'] == 'No' ) ) {
				$valid_fields['country_block_check'] = $this->country_block_check;
				// Set the error message
				add_settings_error( 'aicp_settings_options', 'aicp_country_block_check_error', __( 'You are trying to pass some value that it is not supposed to get. Don\'t try nasty hacking approaches', 'aicp' ), 'error' ); // $setting, $code, $message, $type
			}

			$valid_fields['ban_country_list'] = strip_tags( stripslashes( trim( $fields['ban_country_list'] ) ) );

			//Now it's time to save the values to the server
			return apply_filters( 'validate_options', $valid_fields, $fields);
		}

		/**
		 * Callback function fetch the data from the database
		**/
		public function fetch_data() {
			$fetched_data = get_option( 'aicp_settings_options' );

			if( empty( $fetched_data ) ) {
				$this->click_limit = 3; //default click limit is 3
				$this->click_counter_cookie_exp = 3; //default click counter cookie expiration time is 3 HOURS
				$this->ban_duration = 7; //default ban duration is 7 days
				$this->country_block_check = 'No'; //default state is No
				$this->ban_country_list = ''; //default state is a blank string
			} else {
				//click_limit
				if( empty( $fetched_data['click_limit'] ) ) {
					$this->click_limit = 3; //default click limit is 3
				} else {
					$this->click_limit = $fetched_data['click_limit'];
				}
				//click_counter_cookie_exp
				if( empty( $fetched_data['click_counter_cookie_exp'] ) ) {
					$this->click_counter_cookie_exp = 3; //default click counter cookie expiration time is 3 HOURS
				} else {
					$this->click_counter_cookie_exp = $fetched_data['click_counter_cookie_exp'];
				}
				//ban_duration
				if( empty( $fetched_data['ban_duration'] ) ) {
					$this->ban_duration = 7; //default ban duration is 7 days
				} else {
					$this->ban_duration = $fetched_data['ban_duration'];
				}
				//country_block_check
				if( empty( $fetched_data['country_block_check'] ) ) {
					$this->country_block_check = 'No'; //default state is No
				} else {
					$this->country_block_check = $fetched_data['country_block_check'];
				}
				//ban_country_list
				if( empty( $fetched_data['ban_country_list'] ) ) {
					$this->ban_country_list = ''; //default state is a blank string
				} else {
					$this->ban_country_list = $fetched_data['ban_country_list'];
				}
			}
		}

		/**
		 * Function to add Settings link in the Installed Plugin List Page
		**/
		public function plugin_add_settings_link( $links ) {
			$settings_link = '<a href="' . get_admin_url() . 'admin.php?page=aicp_settings">' . __( 'Settings', 'aicp' ) . '</a>';
			array_push( $links, $settings_link );
			return $links;
		}

		/**
		 * Callback function run the hourly cleanup job to deloete all visitors which are 
		 * blocked more than 7 days
		**/
		public function do_this_hourly() {
			global $wpdb;
			$this->fetch_data();
			$aicpOBJ = new AICP();
			$query = "DELETE FROM " . $aicpOBJ->table_name . " WHERE UNIX_TIMESTAMP( " . $aicpOBJ->table_name . ".timestamp ) < UNIX_TIMESTAMP( DATE_SUB( NOW(), INTERVAL " . $this->ban_duration . " DAY ) )";
			$wpdb->query( $query );
		}

	    public function delete_notice( $state ) {
	    	if( $state === true ) {
	    		$class = 'notice notice-success';
				$message = __( 'The selected item has been successfully deleted.', 'aicp' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	    	} else {
	    		$class = 'notice notice-error';
				$message = __( 'Please select atleast one row before processing the delete option.', 'aicp' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	    	}
	    }

		/**
		 * Callback function show up the banned user details page
		**/
		public function banned_user_details() {
			/* Let's handel the bulk and single deletion process first
			 * before showing the table data
			**/
			$bannedUserTableOBJ = new AICP_BANNED_USER_TABLE();
			$aicpOBJ = new AICP();
			if( 'delete'=== $bannedUserTableOBJ->current_action() ) {
	        	global $wpdb;
	        	$fetchedID = $_REQUEST['id'];
	        	if( is_array( $fetchedID ) ) { // for bulk operation arry will return
	        		$selectedID = implode( ",", $fetchedID );
	        	} else { //for singel delete just the id will return
	        		$selectedID = $fetchedID;
	        	}
	        	if( empty( $selectedID ) ) {
	        		$this->delete_notice( false );
	        	} else {
	        		$query = "DELETE FROM " . $aicpOBJ->table_name . " WHERE " . $aicpOBJ->table_name . ".id IN (" . $selectedID . ")";
	        		$wpdb->query( $query );
					$this->delete_notice( true );
	        	}
	        }
	        /* End of handelling the deletion process */
	        /* Now it's time to show our data */
			?>
			<div class="wrap">
				<h1><?php _e( 'Banned User Details', 'aicp' ); ?></h1>
	    		<h4><?php _e( 'On this page you will be able to see the list of banned users who have exceeded the ad click limit. Here you can also manually delete any blocked IP or perform bulk deletion on the blocked IP list.', 'aicp' ); ?></h4>
	    		<hr />
				<?php
					$bannedUserTableOBJ->prepare_items(); 
				?>
				<form method="post">
			    	<input type="hidden" name="page" value="aicp_banned_user_details">
					<?php
						$bannedUserTableOBJ->search_box( 'search', 'search_id' );
						$bannedUserTableOBJ->display(); 
					?>
				</form>
			</div>
			<?php
		}
	} // end of class AICP_ADMIN
} // end of class exists check