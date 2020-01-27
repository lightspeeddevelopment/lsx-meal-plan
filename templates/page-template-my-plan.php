<?php
/**
 * WooCommerce My Account / My Plan Page
 *
 * Template Name: My Plan
 *
 * @package    lsx-health-plan
 * @subpackage template
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main" role="main">

		<?php lsx_content_top(); ?>

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php lsx_entry_before(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php lsx_entry_top(); ?>

					<div class="entry-content">
						<?php
						if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'edit-account' ) ) {
							$classes = get_body_class();
							if ( in_array( 'has-block-banner', $classes ) ) {
								$blocks = parse_blocks( get_the_content() );
								foreach ( $blocks as $block ) {
									if ( 'lsx-blocks/lsx-banner-box' === $block['blockName'] ) {
										echo wp_kses_post( $block['innerHTML'] );
									}
								}
							}

							$my_profile_string  = esc_html__( 'My Profile', 'lsx-health-plan' );
							$my_profile_tagline = esc_html__( 'Update your details below', 'lsx-health-plan' );
							echo do_shortcode( '<div id="edit-account-tab">[lsx_health_plan_my_profile_tabs]<div class="edit-account-section"><h2 class="title-lined">' . $my_profile_string . '</h2><p>' . $my_profile_tagline . '</p>[woocommerce_my_account]</div></div>' );
						} else if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'lost-password' ) ) {
							echo do_shortcode( '[woocommerce_my_account]' );
						} else {
							the_content();
						}
						?>
					</div><!-- .entry-content -->

					<?php lsx_entry_bottom(); ?>

				</article><!-- #post-## -->

				<?php lsx_entry_after(); ?>

			<?php endwhile; ?>

		<?php endif; ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_footer();
