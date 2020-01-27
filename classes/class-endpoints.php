<?php
namespace lsx_health_plan\classes;

/**
 * Contains the endpoints
 *
 * @package lsx-health-plan
 */
class Endpoints {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Endpoints()
	 */
	protected static $instance = null;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Endpoints()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Runs on init
	 */
	public function setup() {
		$this->add_rewrite_rules();
	}

	/**
	 * Registers the rewrites.
	 */
	public function add_rewrite_rules() {
		// Here is where we add in the rewrite rules above the normal WP ones.
		add_rewrite_tag( '%endpoint%', '([^&]+)' );

		// Warm up.
		$warm_up = \lsx_health_plan\functions\get_option( 'endpoint_warm_up', false );
		if ( false === $warm_up ) {
			$warm_up = 'warm-up';
		}
		add_rewrite_rule( 'plan/([^/]+)/' . $warm_up . '/?$', 'index.php?plan=$matches[1]&endpoint=warm-up', 'top' );

		// Workout.
		if ( post_type_exists( 'workout' ) ) {
			$workout = \lsx_health_plan\functions\get_option( 'endpoint_workout', false );
			if ( false === $workout ) {
				$workout = 'workout';
			}
		}
		add_rewrite_rule( 'plan/([^/]+)/' . $workout . '/?$', 'index.php?plan=$matches[1]&endpoint=workout', 'top' );

		// Meal.
		if ( post_type_exists( 'meal' ) ) {
			$meal = \lsx_health_plan\functions\get_option( 'endpoint_meal', false );
			if ( false === $meal ) {
				$meal = 'meal';
			}
		}
		add_rewrite_rule( 'plan/([^/]+)/' . $meal . '/?$', 'index.php?plan=$matches[1]&endpoint=meal', 'top' );

		// Recipe.
		if ( post_type_exists( 'recipe' ) ) {
			$recipe = \lsx_health_plan\functions\get_option( 'endpoint_recipe', false );
			if ( false === $recipe ) {
				$recipe = 'recipes';
			}
		}
		add_rewrite_rule( 'plan/([^/]+)/' . $recipe . '/?$', 'index.php?plan=$matches[1]&endpoint=recipes', 'top' );
	}
}
