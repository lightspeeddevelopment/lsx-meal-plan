<?php
namespace lsx_health_plan\classes;

/**
 * LSX Health Plan Admin Class.
 *
 * @package lsx-health-plan
 */
class Setup {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Setup()
	 */
	protected static $instance = null;

	/**
	 * @var object \lsx_health_plan\classes\Post_Type();
	 */
	public $post_types;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'wp_head', array( $this, 'load_shortcodes' ) );
		$this->load_classes();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Setup()    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Adds text domain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'lsx-health-plan', false, basename( LSX_HEALTH_PLAN_PATH ) . '/languages' );
	}

	/**
	 * Registers our shortcodes.
	 *
	 * @return void
	 */
	public function load_classes() {
		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-post-type.php';
		$this->post_types = Post_Type::get_instance();
	}

	/**
	 * Registers our shortcodes.
	 *
	 * @return void
	 */
	public function load_shortcodes() {
		add_shortcode( 'lsx_health_plan_restricted_content', '\lsx_health_plan\shortcodes\restricted_content' );
		add_shortcode( 'lsx_health_plan_my_profile_tabs', '\lsx_health_plan\shortcodes\my_profile_tabs' );
		add_shortcode( 'lsx_health_plan_my_profile_block', '\lsx_health_plan\shortcodes\my_profile_box' );
		add_shortcode( 'lsx_health_plan_day_plan_block', '\lsx_health_plan\shortcodes\day_plan_box' );
		add_shortcode( 'lsx_health_plan_account_notices', '\lsx_health_plan\shortcodes\account_notices' );

		if ( post_type_exists( 'video' ) ) {
			add_shortcode( 'lsx_health_plan_featured_video_block', '\lsx_health_plan\shortcodes\feature_video_box' );
		}
		if ( post_type_exists( 'recipe' ) ) {
			add_shortcode( 'lsx_health_plan_featured_recipes_block', '\lsx_health_plan\shortcodes\feature_recipes_box' );
		}
		if ( post_type_exists( 'tip' ) ) {
			add_shortcode( 'lsx_health_plan_featured_tips_block', '\lsx_health_plan\shortcodes\feature_tips_box' );
		}
	}
}
