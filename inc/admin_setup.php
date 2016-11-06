<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Requiring the persistent notice remaval code
require_once plugin_dir_path( __FILE__ ) . '../vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';

class AICP_ADMIN {

	/* Let's declare some variables that we are going to use all around our code to fetch data 
	 * As we are going to this variables at variour part of our code we are using public statement instead of protected
	**/
	public $click_limit, $ban_duration, $country_block_check, $ban_country_list;

	/**
     * Function to load CSS & JS files at the admin side
     * @return Nothing
    **/
    public function admin_scripts() {
    	/* CSS Calls */
		wp_enqueue_style('aicp-admin-interface', AICP_BASE . 'assets/css/aicp-admin-interface.css', array(), '1.0.0');
    }

    /**
     * Function to show up dashboard with the blocking data at the admin dashboard
     * @return Nothing
    **/
    public function aicp_dashboard() {}

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
				array( __CLASS__, 'admin_settings_page' )
			);

			add_submenu_page( 
				'aicp_settings', 
				__( 'AICP - Banned User Details', 'aicp' ), 
				__( 'PHP Information', 'aicp' ), 
				'Banned User Details', 
				'aicp_banned_user_details', 
				array( __CLASS__, 'banned_user_details' )
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
    		'<a href="https://goo.gl/V41y3K" target="_blank" rel="external" title="WP Server Stats - Plugin Donation">', '</a>',
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

    				//var_dump($this);
    				?>
    			</form>
    		</div>
    		<div id="aicp-sidebar">
    			<h2>
    				<?php
    				_e('Some info about the settings options', 'aicp');
    				?>
    			</h2>
    			<ul class="user-info">
    				<li>
    					<strong class="highlight"><?php _e('Refresh Interval', 'aicp'); ?></strong>
    					<?php _e('This denotes the interval time after which the shell commands will execute again to give you the current load details. By default it is set to 200ms, but if you are seeing CPU load increase after instealling this plugin, try to increase the interval time to 1000ms, 2000ms, 3000ms or more until you see a normal CPU load. Generally it is not recommended to change the value unless you are having extremely high CPU load due to this plugin.', 'aicp'); ?>
    				</li>
    				<li>
    					<strong class="highlight"><?php _e('Status Bar & Footer Text Color', 'aicp'); ?></strong>
    					<?php _e('In case you do not like the color scheme I have used on this plugin, you can easily change those colors.', 'aicp'); ?>
    				</li>
    				<li>
    					<strong class="highlight"><?php _e('Memcached Server Host & Port', 'aicp'); ?></strong>
    					<?php _e('Memcached is a general-purpose distributed memory caching system. It is often used to speed up dynamic database-driven websites by caching data and objects in RAM to reduce the number of times an external data source must be read. But in most Shared Hosting servers Memcached will not be enabled. This generally used in personal VPS or Dedicated servers.', 'aicp'); ?>
    					<br />
    					<?php _e('So, if you are using a shared hosting server, chances are Memcached is not enabled on your server. In this case you don\'t need to change any of the Memcached settings on the left side. But if you are using a VPS or dedicated server which has Memcached enabled, make sure the Memcached Host & Port details has been provided properly on the settings. If you don\'t have these details, please contact your host and ask them about it.' , 'aicp'); ?>
    				</li>
    			</ul>
    			<hr />
    			<h2><?php _e('Support the plugin', 'aicp'); ?></h2>
    			<p><?php _e('Believe it or not, developing a WorPress plugin really takes quite a lot of time to develop, test and to do continuous bugfix. Moreover as I\'m sharing this plugin for free, so all those times I\'ve spent coding this plugin yeild no revenue. So, overtime it become really hard to keep spending time on this plugin. So, if you like this plugin, I will really appriciate if you consider donating some amount for this plugin. Which will help me keep spending time on this plugin and make it even better. Please donate, if you can.', 'aicp'); ?></p>
    			<div class="content-center">
    				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
    					<input type="hidden" name="cmd" value="_donations">
    					<input type="hidden" name="business" value="saumya0305@gmail.com">
    					<input type="hidden" name="lc" value="US">
    					<input type="hidden" name="item_name" value="Plugin Donation - WP Server Stats">
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
		add_settings_section( 'aicp_section', __( 'Change the AdSense Invalid Click Protector Settings', 'aicp' ), array( __CLASS__, 'display_section' ), 'aicp_settings' ); // id, title, display cb, page

		// Add Field for the Click Limit
		add_settings_field( 'aicp_click_limit', __( 'Set the Ad Click Limit', 'aicp' ), array( __CLASS__, 'click_limit_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

		// Add Field for the Ban Duration (in days)
		add_settings_field( 'aicp_ban_duration', __( 'Set the Visitor Ban Duration (default: 7 days)', 'aicp' ), array( __CLASS__, 'ban_duration_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

		// Add Field for checking if the user wanna ban any specific country
		add_settings_field( 'aicp_country_block_check', __( 'Do you want to block showing ads for some specific countries?', 'aicp' ), array( __CLASS__, 'country_block_check_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

		// Add Field for selecting countries for which you wanna ban ads
		add_settings_field( 'aicp_country_list', __( 'Banned Country List - Put ISO ALPHA-2 Country Codes (Comma Seperated)', 'aicp' ), array( __CLASS__, 'country_list_field' ), 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

		// Register Settings
		register_setting( 'aicp_settings', 'aicp_settings_options', array( __CLASS__, 'validate_options' ) ); // option group, option name, sanitize cb 
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
	 * Callback function for showing the ban duration field
	**/
	public function ban_duration_field() {
		$this->fetch_data();
		echo '<input type="number" name="aicp_settings_options[ban_duration]" value="' . $this->ban_duration . '" />';
	}

	/**
	 * Callback function for showing the country ban check field
	**/
	public function country_block_check_field() {
		$options = get_option( 'aicp_settings_options' );
		echo '<input type="radio" name="aicp_settings_options[country_block_check]" value="Yes" ' . checked( 'Yes' == $options['country_block_check'] ) . ' /> Yes
		<input type="radio" name="aicp_settings_options[country_block_check]" value="No" ' . checked( 'No' == $options['country_block_check'] ) . ' /> No';
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
		$valid_fields = array();

		// Validate Title Field
		$valid_fields['click_limit'] = strip_tags( stripslashes( trim( $fields['click_limit'] ) ) );

		if( $valid_fields['click_limit'] < 1 || ( is_numeric( $valid_fields['click_limit'] ) === FALSE ) ) {
			// Set the error message
			add_settings_error( 'aicp_settings_options', 'aicp_click_limit_error', __( 'The minimum number of click limit must needs to be more than or equals to 1 and the entered value must be a number', 'aicp' ), 'error' ); // $setting, $code, $message, $type
		}

		$valid_fields['ban_duration'] = strip_tags( stripslashes( trim( $fields['ban_duration'] ) ) );

		if( $valid_fields['ban_duration'] < 1 || ( is_numeric( $valid_fields['ban_duration'] ) === FALSE ) ) {
			// Set the error message
			add_settings_error( 'aicp_settings_options', 'aicp_ban_ducation_error', __( 'The user ban duration must needs to be more than or equals to 1 day & the entered value must be a number', 'aicp' ), 'error' ); // $setting, $code, $message, $type
		}

		$valid_fields['country_block_check'] = strip_tags( stripslashes( trim( $fields['country_block_check'] ) ) );

		if( !( $valid_fields['country_block_check'] == 'Yes' || $valid_fields['country_block_check'] == 'No' ) ) {
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
	 * Callback function show up the banned user details page
	**/
	public function banned_user_details() {
		?>
		<div class="wrap">
		</div>
		<?php
	}

} // end of class AICP_ADMIN