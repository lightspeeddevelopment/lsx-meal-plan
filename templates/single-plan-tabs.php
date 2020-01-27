<?php
/**
* Template used to display single planf top nav.
*
* @package lsx-health-plan
*/

?>
<div id="single-plan-nav">
	<ul class="nav nav-pills">
		<li class="<?php lsx_health_plan_nav_class( '' ); ?>"><a class="overview-tab" href="<?php the_permalink(); ?>"><?php lsx_get_svg_icon( 'eye.svg' ); ?> <?php esc_html_e( 'Overview', 'lsx-health-plan' ); ?></a></li>
		<?php
		if ( lsx_health_plan_has_warmup() ) {
			$warm_up = \lsx_health_plan\functions\get_option( 'endpoint_warm_up', false );
			if ( false === $warm_up ) {
				$warm_up = 'warm-up';
			}
			?>
				<li class="<?php lsx_health_plan_nav_class( 'warm-up' ); ?>"><a class="warm-up-tab" href="<?php the_permalink(); ?><?php echo esc_attr( $warm_up ); ?>/"><?php lsx_get_svg_icon( 'warm.svg' ); ?> <?php esc_html_e( 'Warm-up', 'lsx-health-plan' ); ?></a></li>				
			<?php
		}
		if ( lsx_health_plan_has_workout() ) {
			$workout = \lsx_health_plan\functions\get_option( 'endpoint_workout', false );
			if ( false === $workout ) {
				$workout = 'workout';
			}
			?>
				<li class="<?php lsx_health_plan_nav_class( 'workout' ); ?>"><a class="workout-tab" href="<?php the_permalink(); ?><?php echo esc_attr( $workout ); ?>/"><?php lsx_get_svg_icon( 'work.svg' ); ?> <?php esc_html_e( 'Workout', 'lsx-health-plan' ); ?></a></li>
			<?php
		}
		if ( lsx_health_plan_has_meal() ) {
			$meal = \lsx_health_plan\functions\get_option( 'endpoint_meal', false );
			if ( false === $meal ) {
				$meal = 'meal';
			}
			?>
				<li class="<?php lsx_health_plan_nav_class( 'meal' ); ?>"><a class="meal-plan-tab" href="<?php the_permalink(); ?><?php echo esc_attr( $meal ); ?>/"><?php lsx_get_svg_icon( 'meal.svg' ); ?> <?php esc_html_e( 'Meal Plan', 'lsx-health-plan' ); ?></a></li>
			<?php
		}
		if ( lsx_health_plan_has_recipe() ) {
			$recipe = \lsx_health_plan\functions\get_option( 'endpoint_recipe', false );
			if ( false === $recipe ) {
				$recipe = 'recipes';
			}
			?>
				<li class="<?php lsx_health_plan_nav_class( 'recipes' ); ?>"><a class="recipes-tab" href="<?php the_permalink(); ?><?php echo esc_attr( $recipe ); ?>/"><?php lsx_get_svg_icon( 'recipes.svg' ); ?> <?php esc_html_e( 'Recipes', 'lsx-health-plan' ); ?></a></li>
			<?php
		}
		?>
	</ul>
</div>
