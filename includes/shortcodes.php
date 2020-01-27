<?php
namespace lsx_health_plan\shortcodes;

/**
 * LSX Health Plan Conditional Helpers.
 *
 * @package lsx-health-plan
 */

/**
 * Only display the my account page is the user is logged out.
 *
 * @return string
 */
function restricted_content() {
	$content = '';
	if ( ! is_user_logged_in() ) {
		ob_start();
		echo do_shortcode( '[woocommerce_my_account]' );
		$content = ob_get_clean();
	}
	return $content;
}

/**
 * Outputs the my profile tabs on the frontpage,
 *
 * @return void
 */
function my_profile_tabs() {
	ob_start();
	echo lsx_health_plan_my_profile_tabs(); // WPCS: XSS OK.
	$content = ob_get_clean();
	return $content;
}

/**
 * Outputs the my profile box on the frontpage.
 *
 * @return void
 */
function my_profile_box() {
	ob_start();
	echo lsx_health_plan_my_profile_box(); // WPCS: XSS OK.
	$content = ob_get_clean();
	return $content;
}

/**
 * Outputs the my profile box on the frontpage.
 *
 * @return void
 */
function day_plan_box( $args = array() ) {
	$defaults = array(
		'week_view'      => false,
		'show_downloads' => false,
	);
	$args     = wp_parse_args( $args, $defaults );
	ob_start();
	if ( false === $args['week_view'] ) {
		echo lsx_health_plan_day_plan_block(); // WPCS: XSS OK.
	} else {
		echo lsx_health_plan_week_plan_block( $args ); // WPCS: XSS OK.
	}
	$content = ob_get_clean();
	return $content;
}

/**
 * Outputs the my featured video box on the frontpage.
 *
 * @return void
 */
function feature_video_box() {
	ob_start();
	echo lsx_health_plan_featured_video_block(); // WPCS: XSS OK.
	$content = ob_get_clean();

	wp_enqueue_script( 'slick', LSX_HEALTH_PLAN_URL . 'assets/js/slick.min.js', array( 'jquery' ), LSX_HEALTH_PLAN_VER, true );
	wp_enqueue_script( 'lsx-health-plan-slider', LSX_HEALTH_PLAN_URL . 'assets/js/lsx-health-plan-slider.min.js', array( 'slick' ), LSX_HEALTH_PLAN_VER, true );
	return $content;
}

/**
 * Outputs the my featured recipes box on the frontpage.
 *
 * @return void
 */
function feature_recipes_box() {
	ob_start();
	echo lsx_health_plan_featured_recipes_block(); // WPCS: XSS OK.
	$content = ob_get_clean();
	wp_enqueue_script( 'slick', LSX_HEALTH_PLAN_URL . 'assets/js/slick.min.js', array( 'jquery' ), LSX_HEALTH_PLAN_VER, true );
	wp_enqueue_script( 'lsx-health-plan-slider', LSX_HEALTH_PLAN_URL . 'assets/js/lsx-health-plan-slider.min.js', array( 'slick' ), LSX_HEALTH_PLAN_VER, true );
	return $content;
}

/**
 * Outputs the my featured tips box on the frontpage.
 *
 * @return void
 */
function feature_tips_box() {
	ob_start();
	echo lsx_health_plan_featured_tips_block(); // WPCS: XSS OK.
	$content = ob_get_clean();
	wp_enqueue_script( 'slick', LSX_HEALTH_PLAN_URL . 'assets/js/slick.min.js', array( 'jquery' ), LSX_HEALTH_PLAN_VER, true );
	wp_enqueue_script( 'lsx-health-plan-slider', LSX_HEALTH_PLAN_URL . 'assets/js/lsx-health-plan-slider.min.js', array( 'slick' ), LSX_HEALTH_PLAN_VER, true );
	return $content;
}

/**
 * Prints out the WooCommerce My Account Notices.
 *
 * @return string
 */
function account_notices() {
	$content = '';
	if ( function_exists( 'wc_print_notices' ) ) {
		$content = wc_print_notices( true );
	}
	return $content;
}
