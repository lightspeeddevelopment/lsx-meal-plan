<?php
/**
 * The template for displaying Recipes Archive.
 *
 * @package lsx-health-plan
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<?php
	$page_id  = get_the_ID();
	$redirect = '/content-restricted/?r=' . $page_id . '&wcm_redirect_to=archive&wcm_redirect_id=' . $page_id;
?>

	<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

		<?php lsx_content_before(); ?>

		<!-- Begining restricted content -->
		<?php
		if ( current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
			?>
			<main id="main" role="main">

				<?php lsx_content_top(); ?>

				<div class="post-wrapper archive-plan">
					<div class="row">
						<?php if ( have_posts() ) : ?>
							<?php
							while ( have_posts() ) :
								the_post();
								?>

								<?php include LSX_HEALTH_PLAN_PATH . '/templates/content-archive-recipe.php'; ?>

							<?php endwhile; ?>

						<?php else : ?>

							<?php get_template_part( 'partials/content', 'none' ); ?>

						<?php endif; ?>
					</div>
					<?php lsx_paging_nav(); ?>
				</div>
				<?php lsx_content_bottom(); ?>

			</main><!-- #main -->

			<?php
		} else {
			wp_redirect( $redirect );
			exit;
		}
		?>

<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php //get_sidebar(); ?>

<?php
get_footer();
