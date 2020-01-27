<?php
namespace lsx_health_plan\classes;

/**
 * Contains the downlaods functions post type
 *
 * @package lsx-health-plan
 */
class WP_User_Avatar {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Woocommerce()
	 */
	protected static $instance = null;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_filter( 'wpua_profile_title', array( $this, 'profile_title' ), 10, 1 );
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
	 * Changes the profile title
	 *
	 * @param  string $title
	 * @return string
	 */
	public function profile_title( $title ) {
		$title = '<h3>' . __( 'My Profile', 'lsx-health-plan' ) . '</h3>';
		$title .= '<p class="tagline">' . __( 'Please upload an image of yourself in .jpeg format. Images should be square, to best fit the cropping area, and files sizes kept below 500kb.', 'lsx-health-plan' ) . '</p>';
		return $title;
	}
}
