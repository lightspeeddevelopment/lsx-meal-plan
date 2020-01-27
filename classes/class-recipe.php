<?php
namespace lsx_health_plan\classes;

/**
 * Contains the recipe post type
 *
 * @package lsx-health-plan
 */
class Recipe {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Recipe()
	 */
	protected static $instance = null;

	/**
	 * Holds post_type slug used as an index
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $slug = 'recipe';

	/**
	 * Holds post_type labels
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	public $labels = array();

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );

		// Frontend Actions and Filters.
		add_action( 'lsx_content_wrap_before', 'lsx_health_plan_recipe_archive_description', 11 );
		add_filter( 'lsx_health_plan_archive_template', array( $this, 'enable_post_type' ), 10, 1 );
		add_filter( 'lsx_health_plan_single_template', array( $this, 'enable_post_type' ), 10, 1 );
		add_filter( 'lsx_health_plan_connections', array( $this, 'enable_connections' ), 10, 1 );
		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ), 100 );
		add_filter( 'lsx_display_global_header_description', array( $this, 'disable_global_header_description' ), 100 );
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'recipes_breadcrumb_filter' ), 30, 1 );

		// Backend Actions and Filters.
		add_action( 'cmb2_admin_init', array( $this, 'featured_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'details_metaboxes' ) );
		add_action( 'cmb2_admin_init', array( $this, 'recipes_connections' ), 5 );
		add_action( 'lsx_hp_settings_page', array( $this, 'register_settings' ), 9, 1 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Recipe()    A single instance of this class.
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
		$this->labels = array(
			'name'               => esc_html__( 'Recipe', 'lsx-health-plan' ),
			'singular_name'      => esc_html__( 'Recipes', 'lsx-health-plan' ),
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
			'menu_name'          => esc_html__( 'Recipes', 'lsx-health-plan' ),
		);
		$args         = array(
			'labels'             => $this->labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-editor-ul',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => 'recipe',
			),
			'capability_type'    => 'post',
			'has_archive'        => 'recipes',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
			),
		);
		register_post_type( 'recipe', $args );
	}

	/**
	 * Register the Week taxonomy.
	 */
	public function taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Type', 'taxonomy general name', 'lsx-health-plan' ),
			'singular_name'     => esc_html_x( 'Types', 'taxonomy singular name', 'lsx-health-plan' ),
			'search_items'      => esc_html__( 'Search', 'lsx-health-plan' ),
			'all_items'         => esc_html__( 'All', 'lsx-health-plan' ),
			'parent_item'       => esc_html__( 'Parent', 'lsx-health-plan' ),
			'parent_item_colon' => esc_html__( 'Parent:', 'lsx-health-plan' ),
			'edit_item'         => esc_html__( 'Edit', 'lsx-health-plan' ),
			'update_item'       => esc_html__( 'Update', 'lsx-health-plan' ),
			'add_new_item'      => esc_html__( 'Add New', 'lsx-health-plan' ),
			'new_item_name'     => esc_html__( 'New Name', 'lsx-health-plan' ),
			'menu_name'         => esc_html__( 'Types', 'lsx-health-plan' ),
		);
		$args   = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'recipe-type',
			),
		);
		register_taxonomy( 'recipe-type', array( $this->slug ), $args );
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
		$connections['recipe']['connected_plans'] = 'connected_recipes';
		$connections['plan']['connected_recipes'] = 'connected_plans';
		return $connections;
	}

	/**
	 * Remove the "Archives:" from the post type recipes.
	 *
	 * @param string $title the term title.
	 * @return string
	 */
	public function get_the_archive_title( $title ) {
		if ( is_post_type_archive( 'recipe' ) ) {
			$title = __( 'Recipes', 'lsx-health-plan' );
		}
		if ( is_tax( 'recipe-type' ) ) {
			$queried_object = get_queried_object();
			if ( isset( $queried_object->name ) ) {
				$title = $queried_object->name . ' ' . __( 'Recipes', 'lsx-health-plan' );
			}
		}
		return $title;
	}

	/**
	 * Disables the global header description
	 *
	 * @param boolean $disable
	 * @return boolean
	 */
	public function disable_global_header_description( $disable ) {
		if ( is_tax( 'recipe-type' ) ) {
			$disable = true;
		}
		return $disable;
	}

	/**
	 * Holds the array for the breadcrumbs.
	 *
	 * @var array $crumbs
	 * @return array
	 */
	public function recipes_breadcrumb_filter( $crumbs ) {
		if ( is_tax( 'recipe-type' ) ) {
			$text = $this->labels['singular_name'];
			$url  = get_post_type_archive_link( 'recipe' );
			if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				$crumbs[1] = array(
					0 => $text,
					1 => $url,
				);
			} else {
				$crumbs[1] = array(
					'text' => $text,
					'url'  => $url,
				);
			}
		}
		return $crumbs;
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function featured_metabox() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_featured_metabox',
				'title'        => __( 'Featured', 'lsx-health-plan' ),
				'object_types' => array( $this->slug ),
				'context'      => 'side',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Featured', 'lsx-health-plan' ),
				'desc'       => __( 'Enable the checkbox to feature this recipe, featured recipes display in any page that has the recipe shortcode: [lsx_health_plan_featured_recipes_block]', 'lsx-health-plan' ),
				'id'         => $this->slug . '_featured',
				'type'       => 'checkbox',
				'show_on_cb' => 'cmb2_hide_if_no_cats',
			)
		);
	}

	/**
	 * Define the metabox and field configurations.
	 */
	public function details_metaboxes() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_details_metabox',
				'title'        => __( 'Recipe Details', 'lsx-health-plan' ),
				'object_types' => array( $this->slug ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Prep Time', 'lsx-health-plan' ),
				'id'         => $this->slug . '_prep_time',
				'desc'       => __( 'Add the preparation time for the entire meal i.e: 25 mins', 'lsx-health-plan' ),
				'type'       => 'text',
				'show_on_cb' => 'cmb2_hide_if_no_cats',
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Cooking Time', 'lsx-health-plan' ),
				'id'         => $this->slug . '_cooking_time',
				'desc'       => __( 'Add the cooking time i.e: 15 mins', 'lsx-health-plan' ),
				'type'       => 'text',
				'show_on_cb' => 'cmb2_hide_if_no_cats',
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Serves', 'lsx-health-plan' ),
				'id'         => $this->slug . '_serves',
				'desc'       => __( 'Add the recommended serving size i.e: 6', 'lsx-health-plan' ),
				'type'       => 'text',
				'show_on_cb' => 'cmb2_hide_if_no_cats',
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Portion', 'lsx-health-plan' ),
				'desc'       => __( 'Add the recommended portion size i.e: 200mg', 'lsx-health-plan' ),
				'id'         => $this->slug . '_portion',
				'type'       => 'text',
				'show_on_cb' => 'cmb2_hide_if_no_cats',
			)
		);
	}

	/**
	 * Registers the workout connections on the plan post type.
	 *
	 * @return void
	 */
	public function recipes_connections() {
		$cmb = new_cmb2_box(
			array(
				'id'           => $this->slug . '_recipes_connections_metabox',
				'title'        => __( 'Recipes', 'lsx-health-plan' ),
				'object_types' => array( 'plan' ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name'       => __( 'Recipes', 'lsx-health-plan' ),
				'desc'       => __( 'Connect the recipes that apply to this day plan using the field provided.', 'lsx-health-plan' ),
				'id'         => 'connected_recipes',
				'type'       => 'post_search_ajax',
				// Optional :
				'limit'      => 15,  // Limit selection to X items only (default 1)
				'sortable'   => true, // Allow selected items to be sortable (default false)
				'query_args' => array(
					'post_type'      => array( $this->slug ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => -1,
				),
			)
		);
	}

	/**
	 * Registers the lsx_search_settings
	 *
	 * @param object $cmb new_cmb2_box().
	 * @return void
	 */
	public function register_settings( $cmb ) {
		$cmb->add_field(
			array(
				'id'          => 'recipe_archive_settings_title',
				'type'        => 'title',
				'name'        => __( 'Recipes Archive', 'lsx-health-plan' ),
				'description' => __( 'All of the settings relating to the recipes post type archive.', 'lsx-health-plan' ),
			)
		);
		$cmb->add_field(
			array(
				'id'          => 'recipe_archive_description',
				'type'        => 'wysiwyg',
				'name'        => __( 'Archive Description', 'lsx-health-plan' ),
				'description' => __( 'This will show up on the post type archive.', 'lsx-health-plan' ),
			)
		);
		do_action( 'lsx_hp_recipe_settings_page', $cmb );
	}
}
