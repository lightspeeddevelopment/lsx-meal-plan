<?php
namespace lsx_health_plan\classes;

/**
 * Contains the yoga post type
 *
 * @package lsx-health-plan
 */
class Yoga {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Yoga()
	 */
	protected static $instance = null;

	/**
	 * Holds post_type slug used as an index
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $slug = 'yoga-session';

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'lsx_health_plan_single_template', array( $this, 'enable_post_type' ), 10, 1 );
		//add_filter( 'lsx_health_plan_connections', array( $this, 'enable_connections' ), 10, 1 );
		//add_action( 'cmb2_admin_init', array( $this, 'workout_connections' ), 15 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Workout()    A single instance of this class.
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
			'name'               => esc_html__( 'Yoga Session', 'lsx-health-plan' ),
			'singular_name'      => esc_html__( 'Session', 'lsx-health-plan' ),
			'add_new'            => esc_html_x( 'Add Session', 'post type general name', 'lsx-health-plan' ),
			'add_new_item'       => esc_html__( 'Add Session', 'lsx-health-plan' ),
			'edit_item'          => esc_html__( 'Edit Session', 'lsx-health-plan' ),
			'new_item'           => esc_html__( 'New Session', 'lsx-health-plan' ),
			'all_items'          => esc_html__( 'All Session', 'lsx-health-plan' ),
			'view_item'          => esc_html__( 'View Session', 'lsx-health-plan' ),
			'search_items'       => esc_html__( 'Search', 'lsx-health-plan' ),
			'not_found'          => esc_html__( 'None found', 'lsx-health-plan' ),
			'not_found_in_trash' => esc_html__( 'None found in Trash', 'lsx-health-plan' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__( 'Yoga', 'lsx-health-plan' ),
		);
		$args   = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-universal-access',
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'custom-fields',
			),
		);
		register_post_type( 'yoga', $args );
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
	 * Enables the Bi Directional relationships
	 *
	 * @param array $connections
	 * @return void
	 */
	public function enable_connections( $connections = array() ) {
		$connections['workout']['connected_plans']  = 'connected_workouts';
		$connections['plan']['connected_workouts']  = 'connected_plans';
		$connections['workout']['connected_videos'] = 'connected_workouts';
		$connections['video']['connected_workouts'] = 'connected_videos';
		return $connections;
	}

	/**
	 * Registers the workout connections on the plan post type.
	 *
	 * @return void
	 */
	public function workout_connections() {
		$cmb = new_cmb2_box( array(
			'id'           => $this->slug . '_workout_connections_metabox',
			'title'        => __( 'Workouts', 'lsx-health-plan' ),
			'desc'         => __( 'Start typing to search for your workouts', 'lsx-health-plan' ),
			'object_types' => array( 'plan' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		) );
		$cmb->add_field( array(
			'name'       => __( 'Workouts', 'lsx-health-plan' ),
			'id'         => 'connected_workouts',
			'desc'       => __( 'Connect the workout that applies to this day plan using the field provided.', 'lsx-health-plan' ),
			'type'       => 'post_search_ajax',
			'limit'      => 15,
			'sortable'   => true,
			'query_args' => array(
				'post_type'      => array( 'workout' ),
				'post_status'    => array( 'publish' ),
				'posts_per_page' => -1,
			),
		) );
		$cmb->add_field( array(
			'name'       => __( 'Pre Workout Snack', 'lsx-health-plan' ),
			'id'         => 'pre_workout_snack',
			'type'       => 'wysiwyg',
			'show_on_cb' => 'cmb2_hide_if_no_cats',
			'options'    => array(
				'textarea_rows' => 5,
			),
		) );
		$cmb->add_field( array(
			'name'       => __( 'Post Workout Snack', 'lsx-health-plan' ),
			'id'         => 'post_workout_snack',
			'type'       => 'wysiwyg',
			'show_on_cb' => 'cmb2_hide_if_no_cats',
			'options'    => array(
				'textarea_rows' => 5,
			),
		) );
	}
}
