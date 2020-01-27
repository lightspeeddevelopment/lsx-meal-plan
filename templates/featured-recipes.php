<?php
/**
 * LSX Recipes Shortcode.
 *
 * @package lsx-health-plan
 */

$args    = array(
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_type'      => 'recipe',
	'posts_per_page' => 3,
	'meta_key'       => 'recipe_featured',
	'meta_compare'   => 'EXISTS',
);
$recipes = new WP_Query( $args );
?>

<div id="lsx-recipes-shortcode" class="daily-plan-block">
	<h2 class="title-lined"><?php esc_html_e( 'Featured Recipe', 'lsx-health-plan' ); ?></h2>
	<div class="lsx-recipes-shortcode lsx-recipes-slider slick-slider slick-dotted slick-has-arrows" data-slick="{'slidesToShow': 1, 'slidesToScroll': 1}" >

		<?php
		if ( $recipes->have_posts() ) :
			while ( $recipes->have_posts() ) :
				$recipes->the_post();
				$post_id        = get_the_id();
				$featured_image = get_the_post_thumbnail( $post_id, array( 600, 300 ) );
				?>
				<div class="lsx-recipes-slot">
					<div class="row">
						<div class="col-md-5 col-sm-6">
							<h4 class="lsx-recipes-title"><?php the_title(); ?></h4>
							<?php lsx_health_plan_recipe_data(); ?>
							<a href="<?php echo esc_url( get_permalink() ); ?>" class="btn"><?php esc_html_e( 'View Recipe', 'lsx-health-plan' ); ?></a>
						</div>
						<div class="col-md-7 col-sm-6">
							<?php
							if ( ! empty( $featured_image ) ) {
								echo wp_kses_post( $featured_image );
							}
							?>
						</div>
					</div>
				</div>
		<?php endwhile; ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
