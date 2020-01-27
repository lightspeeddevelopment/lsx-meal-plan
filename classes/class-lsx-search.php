<?php
namespace lsx_health_plan\classes;

/**
 * Contains the downlaods functions post type
 *
 * @package lsx-health-plan
 */
class LSX_Search {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Woocommerce()
	 */
	protected static $instance = null;

	/**
	 * An array of the active post types.
	 *
	 * @var array
	 */
	public $post_types = array();

	/**
	 * This holds the current facet info.
	 *
	 * @var array
	 */
	public $facet_data = array();

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'lsx_hp_recipe_settings_page', array( $this, 'register_settings' ), 9, 1 );
		add_action( 'wp', array( $this, 'init' ), 5 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Woocommerce()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Runs the recipe search setup
	 */
	public function init() {
		$enabled = \lsx_health_plan\functions\get_option( 'recipe_search_enable', false );
		if ( false !== $enabled ) {
			// LSX Search filters.
			add_filter( 'lsx_search_options', array( $this, 'lsx_search_options' ), 10, 1 );
			add_filter( 'lsx_search_enabled', array( $this, 'lsx_search_enabled' ), 10, 1 );
			add_filter( 'lsx_search_prefix', array( $this, 'lsx_search_prefix' ), 10, 1 );
		}
	}

	/**
	 * Sets the FacetWP variables.
	 */
	public function set_facetwp_vars() {
		if ( function_exists( '\FWP' ) ) {
			$facet_data = \FWP()->helper->get_facets();
		}

		$this->facet_data = array();
		if ( ! empty( $facet_data ) && is_array( $facet_data ) ) {
			foreach ( $facet_data as $facet ) {
				$this->facet_data[ $facet['name'] ] = $facet['label'];
			}
		}
	}

	/**
	 * Registers the lsx_search_settings
	 *
	 * @param object $cmb new_cmb2_box().
	 * @return void
	 */
	public function register_settings( $cmb ) {
		$this->set_facetwp_vars();
		$cmb->add_field(
			array(
				'name'    => __( 'Enable Search', 'lsx-health-plan' ),
				'id'      => 'recipe_search_enable',
				'type'    => 'checkbox',
				'value'   => 1,
				'default' => 0,
			)
		);
		$cmb->add_field(
			array(
				'name'    => __( 'Layout', 'lsx-health-plan' ),
				'id'      => 'recipe_search_layout',
				'type'    => 'select',
				'options' => array(
					''    => __( 'Follow the theme layout', 'lsx-health-plan' ),
					'1c'  => __( '1 column', 'lsx-health-plan' ),
					'2cr' => __( '2 columns / Content on right', 'lsx-health-plan' ),
					'2cl' => __( '2 columns / Content on left', 'lsx-health-plan' ),
				),
				'default' => '',
			)
		);
		/*$cmb->add_field(
			array(
				'name'    => __( 'Disable per page', 'lsx-health-plan' ),
				'id'      => 'recipe_search_disable_per_page',
				'type'    => 'checkbox',
				'value'   => 1,
				'default' => 0,
			)
		);*/
		$cmb->add_field(
			array(
				'name'        => __( 'Collapse', 'lsx-health-plan' ),
				'id'          => 'recipe_search_enable_collapse',
				'type'        => 'checkbox',
				'value'       => 1,
				'description' => __( 'Enable collapsible filters on search results', 'lsx-health-plan' ),
				'default'     => 0,
			)
		);
		$cmb->add_field(
			array(
				'name'        => __( 'Disable Sorting', 'lsx-health-plan' ),
				'id'          => 'recipe_search_disable_all_sorting',
				'type'        => 'checkbox',
				'value'       => 1,
				'description' => __( 'Disables the sort by dropdown.', 'lsx-health-plan' ),
				'default'     => 0,
			)
		);
		$cmb->add_field(
			array(
				'name'        => __( 'Disable the date option', 'lsx-health-plan' ),
				'id'          => 'recipe_search_disable_date_sorting',
				'type'        => 'checkbox',
				'value'       => 1,
				'description' => __( 'Disables the date option for the sort by dropdown.', 'lsx-health-plan' ),
				'default'     => 0,
			)
		);
		$cmb->add_field(
			array(
				'name'    => __( 'Display Result Count', 'lsx-health-plan' ),
				'id'      => 'recipe_search_display_result_count',
				'type'    => 'checkbox',
				'value'   => 1,
				'default' => 1,
			)
		);
		$cmb->add_field(
			array(
				'name'        => __( 'Display Clear Button', 'lsx-health-plan' ),
				'id'          => 'recipe_search_display_clear_button',
				'type'        => 'checkbox',
				'value'       => 1,
				'description' => __( 'This will display a clear button next to the "result" count.', 'lsx-health-plan' ),
				'default'     => 1,
			)
		);

		$cmb->add_field(
			array(
				'name'        => __( 'Facets', 'lsx-health-plan' ),
				'description' => __( 'These are the filters that will appear on your archive page.', 'lsx-health-plan' ),
				'id'          => 'recipe_search_facets',
				'type'        => 'multicheck',
				'options'     => $this->facet_data,
			)
		);
	}

	/**
	 * Enables the search if it is the recipe archive.
	 *
	 * @var boolean $enabled
	 * @return boolean
	 */
	public function lsx_search_enabled( $enabled = false ) {
		if ( is_post_type_archive( 'recipe' ) ) {
			$enabled = true;
		}
		return $enabled;
	}

	/**
	 * Enables the search if it is the recipe archive.
	 *
	 * @var string $enabled
	 * @return string
	 */
	public function lsx_search_prefix( $prefix = '' ) {
		if ( is_post_type_archive( 'recipe' ) ) {
			$prefix = 'archive';
		}
		return $prefix;
	}

	/**
	 * Adds the recipe options to the lsx search options.
	 *
	 * @param array $options
	 * @return array
	 */
	public function lsx_search_options( $options = array() ) {
		if ( is_post_type_archive( 'recipe' ) ) {

			$active_facets = \lsx_health_plan\functions\get_option( 'recipe_search_facets', array() );
			$facets = array();
			foreach ( $active_facets as $index => $facet_name ) {
				$facets[ $facet_name ] = 'on';
			}
			$options['display'] = array(
				'search_enable'                => 'on',
				//'archive_disable_per_page'     => \lsx_health_plan\functions\get_option( 'recipe_search_disable_per_page', false ),
				'archive_disable_all_sorting'  => \lsx_health_plan\functions\get_option( 'recipe_search_disable_all_sorting', false ),
				'archive_disable_date_sorting'  => \lsx_health_plan\functions\get_option( 'recipe_search_disable_date_sorting', false ),
				'archive_layout'               => \lsx_health_plan\functions\get_option( 'recipe_search_layout', '2cr' ),
				'archive_layout_map'           => 'list',
				'archive_display_result_count' => \lsx_health_plan\functions\get_option( 'recipe_search_display_result_count', 'on' ),
				'enable_collapse'              => \lsx_health_plan\functions\get_option( 'recipe_search_enable_collapse', false ),
				'archive_facets'               => $facets,
				'archive_display_clear_button' => \lsx_health_plan\functions\get_option( 'recipe_search_display_clear_button', false ),
			);
		}
		return $options;
	}
}
