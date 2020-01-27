<?php
namespace lsx_health_plan\classes;

/**
 * Contains the downlaods functions post type
 *
 * @package lsx-health-plan
 */
class Woocommerce {

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object \lsx_health_plan\classes\Woocommerce()
	 */
	protected static $instance = null;

	/**
	 * Contructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 20, 1 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'only_one_in_cart' ), 99, 2 );
		add_filter( 'woocommerce_order_button_text', array( $this, 'checkout_button_text' ), 10, 1 );
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'breadcrumbs' ), 30, 1 );

		// Checkout.
		add_action( 'woocommerce_checkout_after_order_review', array( $this, 'payment_gateway_logos' ) );

		// Redirect to the Edit Account Template.
		add_filter( 'template_include', array( $this, 'account_endpoint_redirect' ), 99 );

		add_action( 'woocommerce_edit_account_form', array( $this, 'iconic_print_user_frontend_fields' ), 10 );

		add_filter( 'iconic_account_fields', array( $this, 'iconic_add_post_data_to_account_fields' ), 10, 1 );
		add_action( 'show_user_profile', array( $this, 'iconic_print_user_admin_fields' ), 30 );

		add_action( 'personal_options_update', array( $this, 'iconic_save_account_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'iconic_save_account_fields' ) );

		add_action( 'woocommerce_save_account_details', array( $this, 'iconic_save_account_fields' ) );
		add_filter( 'woocommerce_save_account_details_errors', array( $this, 'iconic_validate_user_frontend_fields' ), 10 );

		// Profile Fields.
		add_filter( 'woocommerce_form_field_text', array( $this, 'lsx_profile_photo_field_filter' ), 10, 4 );
		add_action( 'woocommerce_after_edit_account_form', array( $this, 'action_woocommerce_after_edit_account_form' ), 10, 0 );

		// Lost Password fields
		add_action( 'woocommerce_before_lost_password_form', array( $this, 'lost_password_page_title' ), 10 );

		add_action( 'wp', array( $this, 'allow_reset_password_page' ), 9 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object \lsx_health_plan\classes\Woocommerce()    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Runs on init
	 *
	 * @return void
	 */
	public function init() {
		remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );
	}

	/**
	 * Empties the cart before a product is added
	 *
	 * @param [type] $passed
	 * @param [type] $added_product_id
	 * @return void
	 */
	public function only_one_in_cart( $passed, $added_product_id ) {
		wc_empty_cart();
		return $passed;
	}

	/**
	 * Return the Place Order Text
	 *
	 * @param string $label
	 * @return void
	 */
	public function checkout_button_text( $label = '' ) {
		$label = __( 'Place order', 'lsx-health-plan' );
		return $label;
	}

	/**
	 * Add the "Blog" link to the breadcrumbs
	 * @param $crumbs
	 * @return array
	 */
	public function breadcrumbs( $crumbs ) {

		if ( is_singular( 'plan' ) ) {

			$new_crumbs    = array();
			$new_crumbs[0] = $crumbs[0];

			$new_crumbs[1] = array(
				0 => get_the_title( wc_get_page_id( 'myaccount' ) ),
				1 => get_permalink( wc_get_page_id( 'myaccount' ) ),
			);

			$endpoint = get_query_var( 'endpoint' );
			if ( '' === $endpoint || false === $endpoint ) {
				$new_crumbs[2] = array(
					0 => get_the_title(),
					1 => false,
				);
			} else {
				$new_crumbs[2] = array(
					0 => get_the_title(),
					1 => get_permalink(),
				);
				$new_crumbs[3] = array(
					0 => ucwords( $endpoint ),
					1 => false,
				);
			}

			$crumbs = $new_crumbs;
		}
		return $crumbs;
	}

	/**
	 * Redirects to the my account template.
	 *
	 * @param string $template
	 * @return string
	 */
	public function account_endpoint_redirect( $template ) {
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			if ( empty( locate_template( array( 'page-template-my-plan.php' ) ) ) && file_exists( LSX_HEALTH_PLAN_PATH . 'templates/page-template-my-plan.php' ) ) {
				$template = LSX_HEALTH_PLAN_PATH . 'templates/page-template-my-plan.php';
			}
		}
		return $template;
	}

	/**
	 * Add post values to account fields if set.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function iconic_add_post_data_to_account_fields( $fields ) {
		if ( empty( $_POST ) && wp_verify_nonce( sanitize_key( $_POST ) ) ) {
			return $fields;
		}

		foreach ( $fields as $key => $field_args ) {
			if ( isset( $_POST[ $key ] ) && empty( $_POST[ $key ] ) && wp_verify_nonce( sanitize_key( $_POST[ $key ] ) ) ) {
				$fields[ $key ]['value'] = '';
				continue;
			}

			if ( isset( $_POST[ $key ] ) ) {
				$fields[ $key ]['value'] = sanitize_key( $_POST[ $key ] );
			}
		}

		return $fields;
	}

	/**
	 * Add fields to registration form and account area.
	 */
	public function iconic_print_user_frontend_fields() {
		$fields            = $this->get_account_fields();
		$is_user_logged_in = is_user_logged_in();

		echo wp_kses_post( '<h2 class="title-lined my-stats-title">' . __( 'My Stats', 'lsx-health-plan' ) . '</h2>' );
		echo wp_kses_post( '<div class="my-stats">' );

		echo wp_kses_post( '<p class="form-row form-label">' . __( 'Start', 'lsx-health-plan' ) . '</p>' );
		echo wp_kses_post( '<p class="form-row form-label">' . __( 'Goal', 'lsx-health-plan' ) . '</p>' );
		echo wp_kses_post( '<p class="form-row form-label">' . __( 'End', 'lsx-health-plan' ) . '</p>' );

		foreach ( $fields as $key => $field_args ) {
			$value = null;
			if ( ! $this->iconic_is_field_visible( $field_args ) ) {
				continue;
			}
			if ( $is_user_logged_in ) {
				$user_id = $this->iconic_get_edit_user_id();
				$value   = $this->iconic_get_userdata( $user_id, $key );
			}
			$value = ( isset( $field_args['value'] ) && '' !== $field_args['value'] ) ? $field_args['value'] : $value;
			woocommerce_form_field( $key, $field_args, $value );
		}
		echo wp_kses_post( '</div>' );
	}

	/**
	 * Get user data.
	 *
	 * @param $user_id
	 * @param $key
	 *
	 * @return mixed|string
	 */
	public function iconic_get_userdata( $user_id, $key ) {
		if ( ! $this->iconic_is_userdata( $key ) ) {
			return get_user_meta( $user_id, $key, true );
		}

		$userdata = get_userdata( $user_id );

		if ( ! $userdata || ! isset( $userdata->{$key} ) ) {
			return '';
		}

		return $userdata->{$key};
	}

	/**
	 * Get currently editing user ID (frontend account/edit profile/edit other user).
	 *
	 * @return int
	 */
	public function iconic_get_edit_user_id() {
		return ( isset( $_GET['user_id'] ) && wp_verify_nonce( sanitize_key( $_GET['user_id'] ) ) ) ? (int) $_GET['user_id'] : get_current_user_id();
	}


	/**
	 * Save registration fields.
	 *
	 * @param int $customer_id
	 */
	public function iconic_save_account_fields( $customer_id ) {
		$nonce_value = wc_get_var( $_REQUEST['save-account-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
		if ( ! wp_verify_nonce( $nonce_value, 'save_account_details' ) ) {
			return;
		}

		$fields         = $this->get_account_fields();
		$sanitized_data = array();
		foreach ( $fields as $key => $field_args ) {
			if ( ! $this->iconic_is_field_visible( $field_args ) ) {
				continue;
			}

			$sanitize = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : 'wc_clean';
			$value    = ( isset( $_POST[ $key ] ) ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';
			if ( $this->iconic_is_userdata( $key ) ) {

				$sanitized_data[ $key ] = $value;
				continue;
			}

			if ( 'profile_photo' === $key ) {
				// This handles the image uploads.
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';

				$id = media_handle_upload( $key, 0, '' );
				if ( ! is_wp_error( $id ) ) {
					update_term_meta( $customer_id, $key . '_id', $id );
					update_term_meta( $customer_id, $key, $id );
				}
			} else {
				delete_user_meta( $customer_id, $key );
				update_user_meta( $customer_id, $key, $value );
			}
		}

		if ( ! empty( $sanitized_data ) ) {
			$sanitized_data['ID'] = $customer_id;
			wp_update_user( $sanitized_data );
		}
	}


	/**
	 * Is this field core user data.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function iconic_is_userdata( $key ) {
		$userdata = array(
			'user_pass',
			'user_login',
			'user_nicename',
			'user_url',
			'user_email',
			'display_name',
			'nickname',
			'first_name',
			'last_name',
			'description',
			'rich_editing',
			'user_registered',
			'role',
			'jabber',
			'aim',
			'yim',
			'show_admin_bar_front',
		);

		return in_array( $key, $userdata, true );
	}

	/**
	 * Is field visible.
	 *
	 * @param $field_args
	 *
	 * @return bool
	 */
	public function iconic_is_field_visible( $field_args ) {
		$visible = true;
		$action  = filter_input( INPUT_POST, 'action' );

		if ( is_admin() && ! empty( $field_args['hide_in_admin'] ) ) {
			$visible = false;
		} elseif ( ( is_account_page() || 'save_account_details' === $action ) && is_user_logged_in() && ! empty( $field_args['hide_in_account'] ) ) {
			$visible = false;
		} elseif ( ( is_account_page() || 'save_account_details' === $action ) && ! is_user_logged_in() && ! empty( $field_args['hide_in_registration'] ) ) {
			$visible = false;
		} elseif ( is_checkout() && ! empty( $field_args['hide_in_checkout'] ) ) {
			$visible = false;
		}

		return $visible;
	}

	/**
	 * Add fields to admin area.
	 */
	public function iconic_print_user_admin_fields() {
		$fields = $this->get_account_fields();
		?>
		<h2><?php esc_html_e( 'Additional Information', 'lsx-health-plan' ); ?></h2>
		<table class="form-table" id="iconic-additional-information">
			<tbody>
			<?php foreach ( $fields as $key => $field_args ) { ?>
				<?php
				if ( ! $this->iconic_is_field_visible( $field_args ) ) {
					continue;
				}

				$user_id = $this->iconic_get_edit_user_id();
				$value   = get_user_meta( $user_id, $key, true );
				?>
				<tr>
					<th>
						<label for="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $field_args['label'] ); ?></label>
					</th>
					<td>
						<?php $field_args['label'] = false; ?>
						<?php woocommerce_form_field( $key, $field_args, $value ); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Validate fields on frontend.
	 *
	 * @param WP_Error $errors
	 *
	 * @return WP_Error
	 */
	public function iconic_validate_user_frontend_fields( $errors ) {
		$fields = $this->get_account_fields();

		foreach ( $fields as $key => $field_args ) {
			if ( empty( $field_args['required'] ) ) {
				continue;
			}

			/*if ( ! isset( $_POST['register'] ) && wp_verify_nonce( sanitize_key( $_POST['register'] ) ) && ! empty( $field_args['hide_in_account'] ) ) {
				continue;
			}*/

			if ( isset( $_POST['register'] ) && wp_verify_nonce( sanitize_key( $_POST['register'] ) ) && ! empty( $field_args['hide_in_registration'] ) ) {
				continue;
			}

			if ( empty( $_POST[ $key ] ) ) {
				/* translators: %s: field */
				$message = sprintf( __( '%s is a required field.', 'lsx-health-plan' ), '<strong>' . $field_args['label'] . '</strong>' );
				$errors->add( $key, $message );
			}
		}

		return $errors;
	}

	/**
	 * Changes the text into a file upload.
	 * @param $field
	 * @param $key
	 * @param $args
	 * @param $value
	 *
	 * @return mixed
	 */
	public function lsx_profile_photo_field_filter( $field, $key, $args, $value ) {
		if ( 'profile_photo' === $args['id'] ) {

			if ( $args['required'] ) {
				$args['class'][] = 'validate-required';
				$required        = ' <abbr class="required" title="' . esc_attr__( 'required', 'lsx-health-plan' ) . '">*</abbr>';
			} else {
				$required = '';
			}

			if ( is_string( $args['label_class'] ) ) {
				$args['label_class'] = array( $args['label_class'] );
			}

			if ( is_null( $value ) ) {
				$value = $args['default'];
			}

			// Custom attribute handling.
			$custom_attributes         = array();
			$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

			if ( $args['maxlength'] ) {
				$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
			}

			if ( ! empty( $args['autocomplete'] ) ) {
				$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
			}

			if ( true === $args['autofocus'] ) {
				$args['custom_attributes']['autofocus'] = 'autofocus';
			}

			if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
				foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			if ( ! empty( $args['validate'] ) ) {
				foreach ( $args['validate'] as $validate ) {
					$args['class'][] = 'validate-' . $validate;
				}
			}

			$field_html      = '';
			$field           = '';
			$label_id        = $args['id'];
			$sort            = $args['priority'] ? $args['priority'] : '';
			$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';
			$args['class'][] = 'validate-required';
			$required        = ' <abbr class="required" title="' . esc_attr__( 'required', 'lsx-health-plan' ) . '">*</abbr>';

			$field .= '<input accept="image/*" type="file" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="" ' . implode( ' ', $custom_attributes ) . ' />';

			if ( '' !== $value && $value !== $args['default'] ) {
				$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '_id" id="' . esc_attr( $args['id'] ) . '_id" placeholder="' . esc_attr( $args['placeholder'] ) . '_id"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
			}

			$field .= '<input type="hidden" name="MAX_FILE_SIZE" value="500000" />';

			if ( $args['label'] && 'checkbox' !== $args['type'] ) {
				$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
			}

			$field_html .= $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
			}

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id    = esc_attr( $args['id'] ) . '_field';
			$field           = sprintf( $field_container, $container_class, $container_id, $field_html );
		}
		return $field;
	}


	public function action_woocommerce_after_edit_account_form() {
		echo do_shortcode( '[avatar_upload /]' );
	}

	/**
	 * Get additional account fields.
	 *
	 * @return array
	 */
	public function get_account_fields() {
		return apply_filters( 'iconic_account_fields', array(
			'weight_start'  => array(
				'type'                 => 'text',
				'label'                => __( 'Weight:', 'lsx-health-plan' ),
				'placeholder'          => __( 'kg', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'weight_goal'   => array(
				'type'                 => 'text',
				'label'                => __( 'Weight:', 'lsx-health-plan' ),
				'placeholder'          => __( 'kg', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'weight_end'    => array(
				'type'                 => 'text',
				'label'                => __( 'Weight:', 'lsx-health-plan' ),
				'placeholder'          => __( 'kg', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'waist_start'   => array(
				'type'                 => 'text',
				'label'                => __( 'Waist:', 'lsx-health-plan' ),
				'placeholder'          => __( 'cm', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'waist_goal'    => array(
				'type'                 => 'text',
				'label'                => __( 'Waist:', 'lsx-health-plan' ),
				'placeholder'          => __( 'cm', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'waist_end'     => array(
				'type'                 => 'text',
				'label'                => __( 'Waist:', 'lsx-health-plan' ),
				'placeholder'          => __( 'cm', 'lsx-health-plan' ),
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => true,
			),
			'fitness_start' => array(
				'type'                 => 'text',
				'label'                => __( 'Fitness Test Score:', 'lsx-health-plan' ),
				'placeholder'          => '#',
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => false,
			),
			'fitness_goal'  => array(
				'type'                 => 'text',
				'label'                => __( 'Fitness Test Score:', 'lsx-health-plan' ),
				'placeholder'          => '#',
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => false,
			),
			'fitness_end'   => array(
				'type'                 => 'text',
				'label'                => __( 'Fitness Test Score:', 'lsx-health-plan' ),
				'placeholder'          => '#',
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'required'             => false,
			),
		) );
	}

	/**
	 * Removes the content restriction class to allow the password page to show.
	 *
	 * @return void
	 */
	public function allow_reset_password_page() {
		if ( ! is_user_logged_in() && function_exists( 'wc_memberships' ) && is_wc_endpoint_url( 'lost-password' ) ) {

			$members_instance           = wc_memberships();
			$restriction_instance       = $members_instance->get_restrictions_instance();
			$post_restrictions_instance = $restriction_instance->get_posts_restrictions_instance();
			remove_action( 'wp', array( $post_restrictions_instance, 'handle_restriction_modes' ), 10, 1 );
			add_action( 'body_class', array( $this, 'remove_body_classes' ) );
		}
	}

	/**
	 * Remove the gutenberg classes from the lost password page.
	 *
	 * @param array $classes
	 * @return void
	 */
	public function remove_body_classes( $classes = array() ) {
		if ( ! empty( $classes ) ) {
			foreach ( $classes as $class_key => $class_value ) {
				if ( 'gutenberg-compatible-template' === $class_value || 'using-gutenberg' === $class_value ) {
					unset( $classes[ $class_key ] );
				}
			}
		}
		return $classes;
	}

	public function lost_password_page_title() {
		?>
		<h1 class="lost-your-password-title"><?php esc_html_e( 'Lost your password?', 'lsx-health-plan' ); ?></h1>
		<?php
	}

	/**
	 * Add Lets Enrypt and PayFast logos to cart.
	**/
	public function payment_gateway_logos() {
		$encript_image = LSX_HEALTH_PLAN_URL . 'assets/images/le-logo.svg';
		$payfast_image   = LSX_HEALTH_PLAN_URL . 'assets/images/secure-payments.png';
		?>
		<div class="row text-center vertical-align">
			<div class="col-md-6 col-sm-6 col-xs-6">
				<img src="<?php echo esc_url( $encript_image ); ?>" alt="lets_encrypt"/>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-6">
				<img src="<?php echo esc_url( $payfast_image ); ?>" alt="payfast"/>
			</div>
		</div>
		<?php
	}
}
