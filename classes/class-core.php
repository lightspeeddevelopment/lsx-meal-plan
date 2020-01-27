<?php
namespace lsx_health_plan\classes;

/**
 * This class loads the other classes and function files
 *
 * @package lsx-health-plan
 */
class Core {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Core()
	 */
	protected static $instance = null;

	/**
	 * @var object \lsx_health_plan\classes\Setup();
	 */
	public $setup;

	/**
	 * @var object \lsx_health_plan\classes\Admin();
	 */
	public $admin;

	/**
	 * @var object \lsx_health_plan\classes\Frontend();
	 */
	public $frontend;

	/**
	 * @var object \lsx_health_plan\classes\Integrations();
	 */
	public $integrations;

	/**
	 * @var object \lsx_health_plan\classes\Integrations();
	 */
	public $scpo_engine;

	/**
	 * Contructor
	 */
	public function __construct() {
		$this->load_includes();
		$this->load_classes();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Core()    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Loads the variable classes and the static classes.
	 */
	private function load_classes() {

		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-setup.php';
		$this->setup = Setup::get_instance();

		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-admin.php';
		$this->admin = Admin::get_instance();

		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-frontend.php';
		$this->frontend = Frontend::get_instance();

		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-integrations.php';
		$this->integrations = Integrations::get_instance();

		require_once LSX_HEALTH_PLAN_PATH . 'classes/class-scpo-engine.php';
		$this->scpo_engine = SCPO_Engine::get_instance();
	}

	/**
	 * Loads the plugin functions.
	 */
	private function load_includes() {
		require_once LSX_HEALTH_PLAN_PATH . '/includes/functions.php';
		require_once LSX_HEALTH_PLAN_PATH . '/includes/conditionals.php';
		require_once LSX_HEALTH_PLAN_PATH . '/includes/template-tags.php';
		require_once LSX_HEALTH_PLAN_PATH . '/includes/shortcodes.php';
	}

	/**
	 * Returns the post types currently active
	 *
	 * @return void
	 */
	public function get_post_types() {
		$post_types = apply_filters( 'lsx_health_plan_post_types', $this->post_types );
		foreach ( $post_types as $index => $post_type ) {
			$is_disabled = \cmb2_get_option( 'lsx_health_plan_options', $post_type . '_disabled', false );
			if ( true === $is_disabled || 1 === $is_disabled || 'on' === $is_disabled ) {
				unset( $post_types[ $index ] );
			}
		}
		return $post_types;
	}
}
