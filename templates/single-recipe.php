<?php
/**
 * The Template for displaying all single posts.
 *
 * @package lsx-health-plan
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main" role="main">

		<?php lsx_content_top(); ?>

		<?php if ( have_posts() ) : ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<?php include LSX_HEALTH_PLAN_PATH . '/templates/content-recipe.php'; ?>

			<?php endwhile; ?>

		<?php endif; ?>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

	<?php
	if ( is_singular( 'post' ) ) {
		lsx_post_nav();
	}
	?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php
get_footer();
