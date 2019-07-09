<?php
namespace lsx_health_plan\classes;

/**
 * Contains the downlaods functions post type
 *
 * @package lsx-health-plan
 */
class Downloads {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Downloads()
	 */
	protected static $instance = null;

	/**
	 * Holds post_type slug used as an index
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $slug = 'download';

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'downloads_post_type_metaboxes' ), 5 );
		add_action( 'cmb2_admin_init', array( $this, 'download_connections' ), 5 );
		add_filter( 'lsx_health_plan_connections', array( $this, 'enable_connections' ), 10, 1 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Downloads()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Enables the Bi Directional relationships
	 *
	 * @param array $connections
	 * @return void
	 */
	public function enable_connections( $connections = array() ) {
		$connections['dlm_download']['connected_pages']    = 'connected_downloads';
		$connections['dlm_download']['connected_workouts'] = 'connected_downloads';
		$connections['dlm_download']['connected_meals']    = 'connected_downloads';
		$connections['dlm_download']['connected_recipes']  = 'connected_downloads';

		// Post Type Connections.
		$connections['workout']['connected_downloads'] = 'connected_workouts';
		$connections['meal']['connected_downloads']    = 'connected_meals';
		$connections['recipe']['connected_downloads']  = 'connected_recipes';
		$connections['page']['connected_downloads']    = 'connected_pages';
		return $connections;
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function downloads_post_type_metaboxes() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_connections_metabox',
				'title'        => __( 'Connections', 'lsx-health-plan' ),
				'object_types' => array( 'dlm_download' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Pages', 'lsx-health-plan' ),
				'id'         => 'connected_pages',
				'type'       => 'post_search_ajax',
				'limit'      => 10,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'page' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Workouts', 'lsx-health-plan' ),
				'id'         => 'connected_workouts',
				'type'       => 'post_search_ajax',
				'limit'      => 10,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'workout' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Meals', 'lsx-health-plan' ),
				'id'         => 'connected_meals',
				'type'       => 'post_search_ajax',
				'limit'      => 10,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'meal' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Recipe', 'lsx-health-plan' ),
				'id'         => 'connected_recipes',
				'type'       => 'post_search_ajax',
				'limit'      => 10,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'recipe' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
	}

	/**
	 * Registers the workout connections on the plan post type.
	 *
	 * @return void
	 */
	public function download_connections() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_metabox',
				'title'        => __( 'Downloads', 'lsx-health-plan' ),
				'object_types' => array( 'workout', 'meal', 'recipe' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Downloads', 'lsx-health-plan' ),
				'desc'       => __( "Add the pdf's connected to this day plan, using the field provided.", 'lsx-health-plan' ),
				'id'         => 'connected_downloads',
				'type'       => 'post_search_ajax',
				'limit'      => 15,
				'sortable'   => true,
				'query_args' => array(
					'post_type'      => array( 'dlm_download' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
	}
}
