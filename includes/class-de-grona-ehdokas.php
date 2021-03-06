<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class De_Grona_Ehdokas {

	/**
	 * The single instance of De_Grona_Ehdokas.
	 * @var 	object
	 * @access  private
	 * @since 	0.0.1
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   0.0.1
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   0.0.1
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function __construct ( $file = '', $version = PLUGIN_VERSION ) {
		$this->_version = $version;
		$this->_token = 'de_grona_ehdokas';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions
		if( is_admin() ) {
			$this->admin = new De_Grona_Ehdokas_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		add_shortcode( 'degrona15_candidate_cta_buttons', array( $this, 'get_call_to_action_buttons' ) );
	} // End __construct ()

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '' ) {

		if( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new De_Grona_Ehdokas_Post_Type( $post_type, $plural, $single, $description );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array() ) {

		if( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new De_Grona_Ehdokas_Taxonomy( $taxonomy, $plural, $single, $post_types );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   0.0.1
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( PLUGIN_TEXT_DOMAIN, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = PLUGIN_TEXT_DOMAIN;

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main De_Grona_Ehdokas Instance
	 *
	 * Ensures only one instance of De_Grona_Ehdokas is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @static
	 * @see De_Grona_Ehdokas()
	 * @return Main De_Grona_Ehdokas instance
	 */
	public static function instance ( $file = '', $version = PLUGIN_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();

		/**
		* Enable plugin data by default in activation if plugin data have not been saved ever before
		* Double check option names when updating this plugin!
		*/

		$option_name = $this->settings->base;

		$first_install = $option_name . 'degrona15_candidate_data_saved_after_install';
		$first_install_value = get_option( $first_install );
		if ( ! $first_install_value ) :

			$option_enable = $option_name . 'degrona15_candidate_enable_home_page';
			$option_default_name = $option_name . 'degrona15_candidate_name';
			$option_default_description = $option_name . 'degrona15_candidate_description';
			$option_default_add_description = $option_name . 'degrona15_candidate_additional_description';
			$option_default_candidate_number = $option_name . 'degrona15_candidate_number';

			update_option( $option_enable , 'on' );
			update_option( $option_default_name , __( 'George Hay', PLUGIN_TEXT_DOMAIN ) );
			update_option( $option_default_description , __( 'I am George Hay, parliament election candidate from Toronto.', PLUGIN_TEXT_DOMAIN ) );
			update_option( $option_default_add_description , __( 'Vote me and I will stop coal power plants!', PLUGIN_TEXT_DOMAIN ) );
			update_option( $option_default_candidate_number , '100' );

		endif;

	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   0.0.1
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	 * Get data from plugin options
	 * <code>
	 * <?php
	 * $value = get_candidate_data( 'field' );
	 * if ( $value ) { // do something }
	 * ?>
	 * </code>
	 * @param  string $field Field data
	 * @since   0.0.1
	 * @return mixed
	 */
	public function get_candidate_data ( $field ) {
		// Add prefix
		$option_name = $this->settings->base;
		// Add requested field
		$option_name .= $field;
		// Get saved field data
		$option = get_option( $option_name );
		// Return data, if data not found $option is false
		return $option;
	} // End get_candidate_data ()

	/**
	 * Generate HTML for displaying home page candidate info from "Candidate Info"-data
	 * @since   0.0.1
	 * @todo cache $data[$value] = get_option( $value ) and renew if user updates any data in candidate info page
	 * @return string
	 */
	public function get_candidate_home_page_data ( ) {

		// Add default data fields in array
		$fields = array(
			'name' => $this->settings->base . 'degrona15_candidate_name',
			'description' => $this->settings->base . 'degrona15_candidate_description',
			'additional_description' => $this->settings->base . 'degrona15_candidate_additional_description',
			'number' => $this->settings->base . 'degrona15_candidate_number',
			'image' => $this->settings->base . 'degrona15_candidate_image',
			'enable' => $this->settings->base . 'degrona15_candidate_enable_home_page'
			);

		$html = '';

		// Get transient
		$data = get_transient( 'degrona15_candidate_transient' );

		// If transient is not set, get data from db and set transient
		if ( ! $data ) :

			// Get data based on fields
			foreach ($fields as $key => $value) {
				$data[$value] = get_option( $value );
			}

			set_transient( 'degrona15_candidate_transient', $data, 0 );

		endif;

		// If user has enabled candidate info on home page, generate html
		if ( $data[ $fields['enable'] ] ) :
			$image_thumb = '';

			// If image is set, retrieve the src url
			if ( $data[ $fields['image'] ] ) {
				$image_thumb = wp_get_attachment_image_src( $data[ $fields['image'] ], 'large' );
			} else {
				$image_thumb[0] = $this->assets_url . 'img/candidate-default.png';
			}
			$html .= '<div class="de_grona_candidate_wrap">';
			$html .= '<div class="row">';
			$html .= '<section class="de_grona_candidate_info small-6 column">';
			$html .= !empty( $data[ $fields['name'] ] ) ? '<h1>' . $data[ $fields['name'] ] . '</h1>' : '';
			$html .= !empty( $data[ $fields['description'] ] ) ? '<p>' . $data[ $fields['description'] ] . '</p>' : '';
			$html .= !empty( $data[ $fields['additional_description'] ] ) ? '<p>' . $data[ $fields['additional_description'] ] . '</p>' : '';
			$html .= !empty( $data[ $fields['number'] ] ) ? '<figure class="de_grona_candidate_number"><div class="wrap"><span>' . $data[ $fields['number'] ] . '</span></div></figure>' : '';
			$html .= '</section>';
			$html .= '<section class="de_grona_candidate_image small-6 column">';
			$html .= !empty( $image_thumb ) ? '<figure class="de_grona_candidate_img"><img src="' . $image_thumb[0] . '"></figure>' : '';
			$html .= '</section>';
			$html .= '</div>';
			$html .= '<div class="row">';
			$html .= '<figure class="de_grona_logo small-12 medium-5 large-3 end column"><img src="' . $this->assets_url . 'img/vihreatdegrona-logo.jpg"></figure>';
			$html .= '</div>';
			$html .= '</div>';
		endif;

		return $html;
	} // End get_candidate_home_page_data ()

	/**
	 * Generate HTML for displaying candidate contact information from "Other Info"-data
	 * @since   0.1.0
	 * @return string
	 */
	public function get_candidate_contact_information_data ( ) {

		// Add default data fields in array
		$fields = array(
			'phone' => $this->settings->base . 'degrona15_candidate_contact_information_phone',
			'email' => $this->settings->base . 'degrona15_candidate_contact_information_email'
			);

		$html = '';

		// Get transient
		$data = get_transient( 'degrona15_candidate_contact_info_transient' );


		// If transient is not set, get data from db and set transient
		if ( ! $data ) :
			// Get data based on fields
			foreach ($fields as $key => $value) {
				$data[$value] = get_option( $value );
			}
			if ( !empty( $data ) ) {
				set_transient( 'degrona15_candidate_contact_info_transient', $data, 0 );
			}

		endif;

			$html .= !empty( $data[ $fields['phone'] ] ) ? '<p class="phone">'. __( 'Phone number: ', PLUGIN_TEXT_DOMAIN ) . $data[ $fields['phone'] ] . '</p>' : '';
			$html .= !empty( $data[ $fields['email'] ] ) ? '<p class="email">' . __( 'Email: ', PLUGIN_TEXT_DOMAIN ) . $data[ $fields['email'] ] . '</p>' : '';

		return $html;
	} // End get_candidate_contact_information_data ()

	/**
	 * Get default background image
	 * @since   0.1.0
	 * @return string
	 */
	public function get_jumbtoron_default_bg ( ) {
		return $this->assets_url . 'img/default-bg.jpg';
	}

	/**
	 * Get join the campaign and donate buttons
	 * @since   0.1.0
	 * @return string
	 */
	public function get_call_to_action_buttons ( ) {
		// Add default data fields in array
		$fields = array(
			'donate_url' => $this->settings->base . 'degrona15_candidate_donate_url',
			'donate_button_text' => $this->settings->base . 'degrona15_candidate_donate_button_text',
			'join_the_campaign_url' => $this->settings->base . 'degrona15_candidate_join_the_campaign_url',
			'join_the_campaign_button_text' => $this->settings->base . 'degrona15_candidate_join_the_campaign_button_text'
			);

		$html = '';

		// Get transient
		$data = get_transient( 'degrona15_candidate_call_to_action_buttons_transient' );


		// If transient is not set, get data from db and set transient
		if ( ! $data ) :
			// Get data based on fields
			foreach ($fields as $key => $value) {
				$data[$value] = get_option( $value );
			}
			if ( !empty( $data ) ) {
				set_transient( 'degrona15_candidate_call_to_action_buttons_transient', $data, 0 );
			}

		endif;

			if( !empty( $data[ $fields['donate_url'] ] ) ) :
				$html .= '<a href="'. esc_url( $data[ $fields['donate_url'] ] ) .'" class="degrona15_candidate donate button radius small-12">';
				$html .= '<i class="fi-euro"></i>';
				$html .= !empty( $data[ $fields['donate_button_text'] ] ) ? $data[ $fields['donate_button_text'] ] : __( 'Donate', PLUGIN_TEXT_DOMAIN );
				$html .= '</a>';
			endif;

			if( !empty( $data[ $fields['join_the_campaign_url'] ] ) ) :
				$html .= '<a href="'. esc_url( $data[ $fields['join_the_campaign_url'] ] ) .'" class="degrona15_candidate join-the-campaign button radius small-12">';
				$html .= '<i class="fi-torsos-all"></i>';
				$html .= !empty( $data[ $fields['join_the_campaign_button_text'] ] ) ? $data[ $fields['join_the_campaign_button_text'] ] : __( 'Join the campaign!', PLUGIN_TEXT_DOMAIN );
				$html .= '</a>';
			endif;
		return $html;
	}

}
