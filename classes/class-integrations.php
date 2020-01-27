<?php
namespace lsx_health_plan\classes;

/**
 * Contains all the classes for 3rd party Integrations
 *
 * @package lsx-health-plan
 */
class Integrations {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Integrations()
	 */
	protected static $instance = null;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \MAG_CMB2_Field_Post_Search_Ajax()
	 */
	public $cmb2_post_search_ajax = false;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Downloads()
	 */
	public $download_monitor = false;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Woocommerce()
	 */
	public $woocommerce = false;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\WP_User_Avatar()
	 */
	public $wp_user_avatar = false;

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\LSX_Search()
	 */
	public $lsx_search = false;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'cmb2_post_search_ajax' ) );
		add_action( 'init', array( $this, 'download_monitor_init' ) );
		add_action( 'init', array( $this, 'woocommerce_init' ) );
		add_action( 'init', array( $this, 'wp_user_avatar_init' ) );
		add_action( 'init', array( $this, 'lsx_search_init' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Integrations()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Includes the Post Search Ajax if it is there.
	 *
	 * @return void
	 */
	public function cmb2_post_search_ajax() {
		if ( class_exists( 'CMB2_Bootstrap_260' ) ) {
			require_once LSX_HEALTH_PLAN_PATH . 'vendor/lsx-field-post-search-ajax/cmb-field-post-search-ajax.php';
			$this->cmb2_post_search_ajax = new \MAG_CMB2_Field_Post_Search_Ajax();
		}
	}

	/**
	 * Includes the Post Search Ajax if it is there.
	 *
	 * @return void
	 */
	public function download_monitor_init() {
		if ( function_exists( 'download_monitor' ) ) {
			require_once LSX_HEALTH_PLAN_PATH . 'classes/class-downloads.php';
			$this->download_monitor = Downloads::get_instance();
		}
	}

	/**
	 * Includes the Woocommerce functions.
	 *
	 * @return void
	 */
	public function woocommerce_init() {
		if ( function_exists( 'WC' ) ) {
			require_once LSX_HEALTH_PLAN_PATH . 'classes/class-woocommerce.php';
			$this->woocommerce = Woocommerce::get_instance();
		}
	}

	/**
	 * Includes the Woocommerce functions.
	 *
	 * @return void
	 */
	public function wp_user_avatar_init() {
		if ( class_exists( 'WP_User_Avatar_Setup' ) ) {
			require_once LSX_HEALTH_PLAN_PATH . 'classes/class-wp-user-avatar.php';
			$this->wp_user_avatar = WP_User_Avatar::get_instance();
		}
	}

	/**
	 * Includes the Woocommerce functions.
	 *
	 * @return void
	 */
	public function lsx_search_init() {
		if ( class_exists( '\LSX_Search' ) ) {
			require_once LSX_HEALTH_PLAN_PATH . 'classes/class-lsx-search.php';
			$this->lsx_search = LSX_Search::get_instance();
		}
	}
}
