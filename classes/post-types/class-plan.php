<?php
namespace lsx_health_plan\classes;

/**
 * Contains the meal_plan post type
 *
 * @package lsx-health-plan
 */
class Plan {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Plan()
	 */
	protected static $instance = null;

	/**
	 * Holds post_type slug used as an index
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $slug = 'plan';

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_filter( 'lsx_health_plan_single_template', array( $this, 'enable_post_type' ), 10, 1 );
		add_action( 'cmb2_admin_init', array( $this, 'details_metaboxes' ), 5 );
		add_action( 'cmb2_admin_init', array( $this, 'plan_connections' ), 5 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Meal_Plan()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Register the post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => esc_html__( 'Plan', 'lsx-health-plan' ),
			'singular_name'      => esc_html__( 'Plans', 'lsx-health-plan' ),
			'add_new'            => esc_html_x( 'Add New', 'post type general name', 'lsx-health-plan' ),
			'add_new_item'       => esc_html__( 'Add New', 'lsx-health-plan' ),
			'edit_item'          => esc_html__( 'Edit', 'lsx-health-plan' ),
			'new_item'           => esc_html__( 'New', 'lsx-health-plan' ),
			'all_items'          => esc_html__( 'All', 'lsx-health-plan' ),
			'view_item'          => esc_html__( 'View', 'lsx-health-plan' ),
			'search_items'       => esc_html__( 'Search', 'lsx-health-plan' ),
			'not_found'          => esc_html__( 'None found', 'lsx-health-plan' ),
			'not_found_in_trash' => esc_html__( 'None found in Trash', 'lsx-health-plan' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__( 'Plans', 'lsx-health-plan' ),
		);
		$args   = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-welcome-write-blog',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => \lsx_health_plan\functions\get_option( 'plan_single_slug', 'plan' ),
			),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
			),
		);
		register_post_type( 'plan', $args );
	}

	/**
	 * Register the Week taxonomy.
	 */
	public function taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Week', 'taxonomy general name', 'lsx-health-plan' ),
			'singular_name'     => esc_html_x( 'Week', 'taxonomy singular name', 'lsx-health-plan' ),
			'search_items'      => esc_html__( 'Search', 'lsx-health-plan' ),
			'all_items'         => esc_html__( 'All', 'lsx-health-plan' ),
			'parent_item'       => esc_html__( 'Parent', 'lsx-health-plan' ),
			'parent_item_colon' => esc_html__( 'Parent:', 'lsx-health-plan' ),
			'edit_item'         => esc_html__( 'Edit', 'lsx-health-plan' ),
			'update_item'       => esc_html__( 'Update', 'lsx-health-plan' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-health-plan' ),
			'new_item_name'     => esc_html__( 'New Name', 'lsx-health-plan' ),
			'menu_name'         => esc_html__( 'Weeks', 'lsx-health-plan' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'week',
			),
		);

		register_taxonomy( 'week', array( 'plan' ), $args );
	}

	/**
	 * Adds the post type to the different arrays.
	 *
	 * @param array $post_types
	 * @return array
	 */
	public function enable_post_type( $post_types = array() ) {
		$post_types[] = $this->slug;
		return $post_types;
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function details_metaboxes() {
		$cmb = new_cmb2_box( array(
			'id'           => $this->slug . '_details_metabox',
			'title'        => __( 'Details', 'lsx-health-plan' ),
			'object_types' => array( $this->slug ), // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		) );

		$warmup_type = 'page';
		if ( false !== \lsx_health_plan\functions\get_option( 'exercise_enabled', false ) ) {
			$warmup_type = array( 'page', 'workout' );
		}
		$cmb->add_field( array(
			'name'       => __( 'Warmup', 'lsx-health-plan' ),
			'desc'       => __( 'Connect the warm up page that applies to this day plan using the field provided.', 'lsx-health-plan' ),
			'id'         => $this->slug . '_warmup',
			'type'       => 'post_search_ajax',
			// Optional :
			'limit'      => 3,  // Limit selection to X items only (default 1)
			'sortable'   => true, // Allow selected items to be sortable (default false)
			'query_args' => array(
				'post_type'      => $warmup_type,
				'post_status'    => array( 'publish' ),
				'posts_per_page' => -1,
			),
		) );
	}

	/**
	 * Registers the workout connections on the plan post type.
	 *
	 * @return void
	 */
	public function plan_connections() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_connections_metabox',
				'title'        => __( 'Plans', 'lsx-health-plan' ),
				'object_types' => array( 'workout', 'meal', 'tip', 'recipe' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Plan', 'lsx-health-plan' ),
				'id'         => 'connected_plans',
				'desc'       => __( 'Connect this to the day plan it applies to, using the field provided.', 'lsx-health-plan' ),
				'type'       => 'post_search_ajax',
				'limit'      => 15,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'plan' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
	}
}
