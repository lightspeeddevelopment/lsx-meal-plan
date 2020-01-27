<?php
/**
 * LSX Tips Shortcode.
 *
 * @package lsx-health-plan
 */
$connected_tips = get_post_meta( get_the_ID(), 'connected_tips', true );
$args           = array();

if ( empty( $connected_tips ) ) {
	// Featured Tips Global
	$connected_tips = 'tip_featured_tip';
	$args = array(
		'orderby'        => 'date',
		'order'          => 'ASC',
		'post_type'      => 'tip',
		'posts_per_page' => 3,
		'meta_key'       => $connected_tips,
	);
} else {
	$connected_tips = \lsx_health_plan\functions\check_posts_exist( $connected_tips );
	if ( ! empty( $connected_tips ) ) {
		$args = array(
			'orderby'        => 'date',
			'order'          => 'ASC',
			'post_type'      => 'tip',
			'posts_per_page' => 3,
			'post__in'       => $connected_tips,
		);
	}
}

if ( ! empty( $args ) ) {
	$tips = new WP_Query( $args );
	?>
	<div id="lsx-tips-shortcode" class="daily-plan-block content-box box-shadow">
		<div class="lsx-tips-shortcode lsx-tips-slider slick-slider slick-dotted"  >
		<?php
		if ( $tips->have_posts() ) :
			while ( $tips->have_posts() ) :
				$tips->the_post();
				$tip_id = get_the_id();

				$featured_image = get_the_post_thumbnail( $tip_id, array( 600, 300 ) );
				?>
				<div class="diet-tip-wrapper quick-tip">
					<h3 class="title-lined"><?php the_title(); ?></h3>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<div class="tipimage">
							<?php
							if ( ! empty( $featured_image ) && '' !== $featured_image ) {
								?>
								<div class="thumbnail tip-thumbnail"><?php echo wp_kses_post( $featured_image ); ?></div>
								<?php
							} else {
								?>
								<div class="thumbnail tip-thumbnail"><img src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) . '../assets/images/TipPlaceholder.png' ); ?>"></div>
								<?php
							}
							?>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<?php the_content(); ?>
						</div> 
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
		</div>
	</div>
	<?php
}
