<?php
$prep_time    = get_post_meta( get_the_ID(), 'recipe_prep_time', true );
$cooking_time = get_post_meta( get_the_ID(), 'recipe_cooking_time', true );
$serves       = get_post_meta( get_the_ID(), 'recipe_serves', true );
$portion      = get_post_meta( get_the_ID(), 'recipe_portion', true );
?>
<table class="recipe-table">
	<tbody>
		<?php
		if ( ! empty( $prep_time ) ) {
		?>
		<tr class="prep-time">
		<td><?php esc_html_e( 'Prep time: ', 'lsx-health-plan' ); ?></td>
			<td>
			<?php
				echo wp_kses_post( $prep_time );
			?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if ( ! empty( $cooking_time ) ) {
		?>
		<tr class="cooking-time">
			<td><?php esc_html_e( 'Cooking time: ', 'lsx-health-plan' ); ?></td>
			<td>
			<?php
				echo wp_kses_post( $cooking_time );
			?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if ( ! empty( $serves ) ) {
		?>
		<tr class="serves">
			<td><?php esc_html_e( 'Serves: ', 'lsx-health-plan' ); ?></td>
			<td>
			<?php
				echo wp_kses_post( $serves );
			?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if ( ! empty( $portion ) ) {
		?>
		<tr class="portion-size">
			<td><?php esc_html_e( 'Portion size: ', 'lsx-health-plan' ); ?></td>
			<td>
			<?php
				echo wp_kses_post( $portion );
			?>
			</td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
