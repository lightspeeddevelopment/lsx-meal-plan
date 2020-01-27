<?php
/**
 * Template used to display post content on single pages.
 *
 * @package lsx-health-plan
 */
?>

<?php lsx_entry_before(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php lsx_entry_top(); ?>

	<div class="entry-meta">
		<?php lsx_post_meta_single_bottom(); ?>
	</div><!-- .entry-meta -->

	<div id="single-recipe" class="entry-content">
		<h2 class="title-lined"><span class="recipe-prefix"><?php esc_html_e( 'Recipe:', 'lsx-health-plan' ); ?></span> <?php the_title(); ?></h2>
		<div class="row">
			<div class="col-md-6 recipe-image">
				<?php
				the_post_thumbnail( 'large', array(
					'class' => 'aligncenter',
				) );
				?>
				<div class="recipe-data">
					<?php lsx_health_plan_recipe_data(); ?>
				</div>
			</div>
			<div class="col-md-6 recipe-content">
				<?php the_content(); ?>
				<div  class="back-plan-btn">
				<?php
				if ( function_exists( 'wc_get_page_id' ) ) {
					?>
					<a class="btn" href="<?php echo wp_kses_post( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Back To My Plan', 'lsx-health-plan' ); ?></a>
					<?php
				}
				?>
				</div>
			</div>
		</div>
		<?php
		wp_link_pages(
			array(
				'before'      => '<div class="lsx-postnav-wrapper"><div class="lsx-postnav">',
				'after'       => '</div></div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="footer-meta clearfix">
		<?php if ( has_tag() || class_exists( 'LSX_Sharing' ) || ( function_exists( 'sharing_display' ) || class_exists( 'Jetpack_Likes' ) ) ) : ?>
			<div class="post-tags-wrapper">
				<?php lsx_content_post_tags(); ?>

				<?php
				if ( class_exists( 'LSX_Sharing' ) ) {
					lsx_content_sharing();
				} else {
					if ( function_exists( 'sharing_display' ) ) {
						sharing_display( '', true );
					}

					if ( class_exists( 'Jetpack_Likes' ) ) {
						$custom_likes = new Jetpack_Likes();
						echo wp_kses_post( $custom_likes->post_likes( '' ) );
					}
				}
				?>
		<?php endif ?>
	</footer><!-- .footer-meta -->

	<?php lsx_entry_bottom(); ?>

</article><!-- #post-## -->

<?php
lsx_entry_after();
