<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class De_Grona_Ehdokas_Settings {

	/**
	 * The single instance of De_Grona_Ehdokas_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	0.0.1
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	0.0.1
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   0.0.1
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpt_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_menu_page( __( 'Candidate \'15 Settings', PLUGIN_TEXT_DOMAIN ) , __( 'Candidate \'15', PLUGIN_TEXT_DOMAIN ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

       // We're including the farbtastic script & styles here because they're needed for the colour picker
       wp_enqueue_style( 'farbtastic' );
       wp_enqueue_script( 'farbtastic' );
       // We're including the WP media scripts here because they're needed for the image upload field
       wp_enqueue_media();

       wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), PLUGIN_VERSION );
       wp_enqueue_script( $this->parent->_token . '-settings-js' );

	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', PLUGIN_TEXT_DOMAIN ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['candidate_info'] = array(
			'title'					=> __( 'Candidate Info', PLUGIN_TEXT_DOMAIN ),
			'description'			=> __( 'Basic information about yourself. This information is displayed on the site home page.', PLUGIN_TEXT_DOMAIN ),
			'fields'				=> array(
				array(
					'id' 			=> 'degrona15_candidate_name',
					'label'			=> __( 'Your name' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your name.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> __( 'George Hay', PLUGIN_TEXT_DOMAIN ),
					'placeholder'	=> __( 'George Hay', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_description',
					'label'			=> __( 'Short description' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add short description about yourself.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'textarea',
					'default'		=> __( 'I am George Hay, parliament election candidate from Toronto.', PLUGIN_TEXT_DOMAIN ),
					'placeholder'	=> __( 'I am George Hay, parliament election candidate from Toronto.', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_additional_description',
					'label'			=> __( 'Additional description' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add additional description.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'textarea',
					'default'		=> __( 'Vote me and I will stop coal power plants!', PLUGIN_TEXT_DOMAIN ),
					'placeholder'	=> __( 'Vote me and I will stop coal power plants!', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_number',
					'label'			=> __( 'Your candidate number' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your candidate number. Leave blank if you don\'t know it at the moment.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'number',
					'default'		=> '100',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'degrona15_candidate_image',
					'label'			=> __( 'Your image' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add you candidate image.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'degrona15_candidate_enable_home_page',
					'label'			=> __( 'Show candidate info on home page?', PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Check if you want to show your candidate details on home page.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 					=> 'degrona15_candidate_data_saved_after_install',
					'label'				=> '',
					'description'	=> '',
					'type'				=> 'hidden',
					'default'			=> '1',
					'placeholder'	=> ''
				)
				// array(
				// 	'id' 			=> 'single_checkbox',
				// 	'label'			=> __( 'An Option', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'checkbox',
				// 	'default'		=> ''
				// ),
				// array(
				// 	'id' 			=> 'password_field',
				// 	'label'			=> __( 'A Password' , PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'This is a standard password field.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'password',
				// 	'default'		=> '',
				// 	'placeholder'	=> __( 'Placeholder text', PLUGIN_TEXT_DOMAIN )
				// ),
				// array(
				// 	'id' 			=> 'select_box',
				// 	'label'			=> __( 'A Select Box', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'A standard select box.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'select',
				// 	'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
				// 	'default'		=> 'wordpress'
				// ),
				// array(
				// 	'id' 			=> 'radio_buttons',
				// 	'label'			=> __( 'Some Options', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'A standard set of radio buttons.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'radio',
				// 	'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
				// 	'default'		=> 'batman'
				// ),
				// array(
				// 	'id' 			=> 'multiple_checkboxes',
				// 	'label'			=> __( 'Some Items', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'You can select multiple items and they will be stored as an array.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'checkbox_multi',
				// 	'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
				// 	'default'		=> array( 'circle', 'triangle' )
				// )
			)
		);

		$settings['social_media'] = array(
			'title'					=> __( 'Social Media Info', PLUGIN_TEXT_DOMAIN ),
			'description'			=> __( 'Add your social media username and page information here and your site will use them automatically.', PLUGIN_TEXT_DOMAIN ),
			'fields'				=> array(
				array(
					'id' 			=> 'degrona15_candidate_facebook_page_id',
					'label'			=> __( 'Your Facebook-page ID' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your Facebook-page ID.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '00123456789', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_twitter_username',
					'label'			=> __( 'Your Twitter username' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your Twitter username.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'username', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_instagram_username',
					'label'			=> __( 'Your Instagram username' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your Instagram username.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'username', PLUGIN_TEXT_DOMAIN )
				),
				// array(
				// 	'id' 			=> 'colour_picker',
				// 	'label'			=> __( 'Pick a colour', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'color',
				// 	'default'		=> '#21759B'
				// ),
				// array(
				// 	'id' 			=> 'an_image',
				// 	'label'			=> __( 'An Image' , PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'image',
				// 	'default'		=> '',
				// 	'placeholder'	=> ''
				// ),
				// array(
				// 	'id' 			=> 'multi_select_box',
				// 	'label'			=> __( 'A Multi-Select Box', PLUGIN_TEXT_DOMAIN ),
				// 	'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', PLUGIN_TEXT_DOMAIN ),
				// 	'type'			=> 'select_multi',
				// 	'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
				// 	'default'		=> array( 'linux' )
				// )
			)
		);

		$settings['other_info'] = array(
			'title'					=> __( 'Other Info', PLUGIN_TEXT_DOMAIN ),
			'description'			=> __( 'Other settings. This information is displayed on the site.', PLUGIN_TEXT_DOMAIN ),
			'fields'				=> array(
				array(
					'id' 			=> 'degrona15_candidate_contact_information_phone',
					'label'			=> __( 'Your phone number' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your phone number.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'number',
					'default'		=> __( "0123456789"),
					'placeholder'	=> __( "0123456789", PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_contact_information_email',
					'label'			=> __( 'Your email' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your email.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'email',
					'default'		=> __( "george@hay.com", PLUGIN_TEXT_DOMAIN ),
					'placeholder'	=> __( "george@hay.com", PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_site_jumbotron',
					'label'			=> __( 'Home page background image' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add you home page background image.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'degrona15_candidate_join_the_campaign_url',
					'label'			=> __( 'Join the campaign page url' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your "Join the campaign url".', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://www.yousite.com/join-the-campaign', PLUGIN_TEXT_DOMAIN )
				),
				array(
					'id' 			=> 'degrona15_candidate_donate_url',
					'label'			=> __( 'Donate page url' , PLUGIN_TEXT_DOMAIN ),
					'description'	=> __( 'Add your donate page url.', PLUGIN_TEXT_DOMAIN ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://www.donateme.com/myname', PLUGIN_TEXT_DOMAIN )
				)
			)
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach( $this->settings as $section => $data ) {

				if( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	private function clear_cached() {

		// If wp_super_cache is used, clear cache for front_page so changes are displayed immediately
		if ( function_exists ( 'wp_cache_post_change' ) ) {
			$front_page_id = get_option( 'page_on_front' );
			$GLOBALS["super_cache_enabled"] = 1;
			wp_cache_post_change( $front_page_id );
		}

		delete_transient( 'degrona15_candidate_transient' );
		delete_transient( 'degrona15_candidate_contact_info_transient' );
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Delete degrona15_candidate_transients if user update data
		if ( isset( $_GET['settings-updated'] ) && $_GET['page'] == 'de_grona_ehdokas_settings' )  {
			$this->clear_cached();
		}
		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Candidate \'15 Settings' , PLUGIN_TEXT_DOMAIN ) . '</h2>' . "\n";

			$tab = '';
			if( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;

				foreach( $this->settings as $section => $data ) {
					// Set tab class
					$class = 'nav-tab';
					if( ! isset( $_GET['tab'] ) ) {
						if( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , PLUGIN_TEXT_DOMAIN ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;

		// Debug
		// echo '<pre>';
		// print_r($this->settings);
		// echo '</pre>';
	}

	/**
	 * Main De_Grona_Ehdokas_Settings Instance
	 *
	 * Ensures only one instance of De_Grona_Ehdokas_Settings is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @static
	 * @see De_Grona_Ehdokas()
	 * @return Main De_Grona_Ehdokas_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
