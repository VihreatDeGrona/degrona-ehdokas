<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class De_Grona_Ehdokas {

	/**
	 * The single instance of De_Grona_Ehdokas.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
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

			}

			$html .= '<div class="de_grona_candidate_wrap row">';
			$html .= '<section class="de_grona_candidate_info large-6 columns">';
			$html .= !empty( $data[ $fields['name'] ] ) ? '<h1>' . $data[ $fields['name'] ] . '</h1>' : '';
			$html .= !empty( $data[ $fields['description'] ] ) ? '<p>' . $data[ $fields['description'] ] . '</p>' : '';
			$html .= !empty( $data[ $fields['number'] ] ) ? '<figure class="de_grona_candidate_number"><div class="wrap"><span>' . $data[ $fields['number'] ] . '</span></div></figure>' : '';
			$html .= '</section>';
			$html .= '<section class="de_grona_candidate_image large-6 columns">';
			$html .= !empty( $image_thumb ) ? '<figure class="de_grona_candidate_img"><img src="' . $image_thumb[0] . '"></figure>' : '';
			$html .= '</section>';
			$html .= '</div>';
		endif;

		return $html;
	} // End get_candidate_home_page_data ()

}
