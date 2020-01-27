<?php
/**
 * The Template for displaying the single day plan and its connected items
 *
 * @package lsx-health-plan
 */

get_header(); ?>

<?php lsx_content_wrap_before(); ?>

<div id="primary" class="content-area <?php echo esc_attr( lsx_main_class() ); ?>">

	<?php lsx_content_before(); ?>

	<main id="main" class="site-main" role="main">

		<?php lsx_content_top(); ?>

		<?php lsx_health_plan_single_nav(); ?>

		<?php lsx_health_plan_single_tabs(); ?>

		<div class="row status-plan-buttons">
			<?php lsx_health_plan_day_button(); ?>
		</div>

		<?php lsx_content_bottom(); ?>

	</main><!-- #main -->

	<?php lsx_content_after(); ?>

</div><!-- #primary -->

<?php lsx_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php
get_footer();
