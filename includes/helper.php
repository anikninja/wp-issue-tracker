<?php
/**
 * Helper Functions
 *
 * @package WebsiteIssueTracker
 * @version 1.0.0
 *
 */

// Don't call the file directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

/**
 * Modal Button Helper
 *
 * @param array|string $args Button Attributes.
 * @param string        $el   button el.
 *
 * @return string
 */
function wp_issue_tracker_modal_button( $args = '', $el = 'button' ) {

	$el   = strtolower( $el );
	$args = wp_parse_args( $args, [ 'class' => '', 'target' => '', 'label' => '' ] );

	if ( empty( $args['label'] ) || empty( $args['target'] ) || ! in_array( $el, [ 'a', 'button']  ) ) {
		return '';
	}

	$attrs   = '';
	$label   = $args['label'];
	$classes = is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'];

	if ( 'a' === $el ) {
		$attrs .= ' href="#"';
	}

	$attrs .= ' class="issue-tracker-button open-issue-tracker-modal ' . esc_attr( $classes ) . '"';
	$attrs .= ' data-modal="#' . esc_attr( $args['target'] ) . '"';

	unset( $args['class'], $args['label'], $args['target'] );

	if ( ! empty( $args ) ) {
		foreach( $args as $param => $val ) {
			if ( is_array( $val ) ) {
				$val = implode( ' ', $val );
			}

			$attrs .= ' ' . $param . '="' . esc_attr( $val ) . '"';
		}
	}

	return sprintf( '<%1$s %2$s>%3$s</%1$s>', $el, $attrs, $label );
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 * Thanks To WooCommerce.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 */
function wp_issue_tracker_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	$template = wp_issue_tracker_locate_template( $template_name, $template_path, $default_path );


	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wp_issue_tracker_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'website-issue-tracker' ), '<code>' . $filter_template . '</code>' ), '2.1' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // phpcs:ignore
	}

	do_action( 'wp_issue_tracker_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	/** @noinspection PhpIncludeInspection */
	include $action_args['located'];

	do_action( 'wp_issue_tracker_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Locate a template and return the path for inclusion.
 * Thanks To WooCommerce.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 * @return string
 */
function wp_issue_tracker_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = Website_Issue_Tracker()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = Website_Issue_Tracker()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$cs_template = str_replace( '_', '-', $template_name );
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $cs_template,
			$cs_template,
		)
	);

	if ( empty( $template ) ) {
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);
	}

	// Get default template/.
	if ( ! $template ) {
		if ( empty( $cs_template ) ) {
			$template = $default_path . $template_name;
		} else {
			$template = $default_path . $cs_template;
		}
	}

	// Return what we found.
	return apply_filters( 'wp_issue_tracker_locate_template', $template, $template_name, $template_path );
}

/**
 * Outputs a checkout/address form field.
 * Thanks To WooCommerce.
 *
 * @param string $key Key.
 * @param mixed  $args Arguments.
 * @param string $value (default: null).
 * @return string|void
 */
function wp_issue_tracker_form_field( $key, $args, $value = null ) {
	$defaults = array(
		'type'              => 'text',
		'label'             => '',
		'description'       => '',
		'placeholder'       => '',
		'maxlength'         => false,
		'required'          => false,
		'autocomplete'      => false,
		'id'                => $key,
		'class'             => array(),
		'label_class'       => array(),
		'input_class'       => array(),
		'return'            => false,
		'options'           => array(),
		'custom_attributes' => array(),
		'validate'          => array(),
		'default'           => '',
		'autofocus'         => '',
		'priority'          => '',
	);

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_issue_tracker_form_field_args', $args, $key, $value );

	if ( $args['required'] ) {
		$args['class'][] = 'validate-required';
		$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
	} else {
		$required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
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

	if ( $args['description'] ) {
		$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
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

	$field           = '';
	$label_id        = $args['id'];
	$sort            = $args['priority'] ? $args['priority'] : '';
	$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

	switch ( $args['type'] ) {
		case 'textarea':
			$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

			break;
		case 'checkbox':
			$field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';

			break;
		case 'text':
		case 'password':
		case 'datetime':
		case 'datetime-local':
		case 'date':
		case 'month':
		case 'time':
		case 'week':
		case 'number':
		case 'email':
		case 'url':
		case 'tel':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'hidden':
			$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-hidden ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

			break;
		case 'select':
			$field   = '';
			$options = '';

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					if ( '' === $option_key ) {
						// If we have a blank option, select2 needs a placeholder.
						if ( empty( $args['placeholder'] ) ) {
							$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
						}
						$custom_attributes[] = 'data-allow_clear="true"';
					}
					$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_html( $option_text ) . '</option>';
				}

				$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
							' . $options . '
						</select>';
			}

			break;
		case 'radio':
			$label_id .= '_' . current( array_keys( $args['options'] ) );

			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_text ) {
					$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
					$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . esc_html( $option_text ) . '</label>';
				}
			}

			break;
	}

	if ( ! empty( $field ) ) {
		$field_html = '';

		if ( $args['label'] && 'checkbox' !== $args['type'] ) {
			$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
		}

		$field_html .= '<span class="woocommerce-input-wrapper">' . $field;

		if ( $args['description'] ) {
			$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
		}

		$field_html .= '</span>';

		$container_class = esc_attr( implode( ' ', $args['class'] ) );
		$container_id    = esc_attr( $args['id'] ) . '_field';
		$field           = sprintf( $field_container, $container_class, $container_id, $field_html );
	}

	/**
	 * Filter by type.
	 */
	$field = apply_filters( 'wp_issue_tracker_form_field_' . $args['type'], $field, $key, $args, $value );

	/**
	 * General filter on form fields.
	 *
	 * @since 3.4.0
	 */
	$field = apply_filters( 'wp_issue_tracker_form_field', $field, $key, $args, $value );

	if ( $args['return'] ) {
		return $field;
	} else {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field;
	}
}

function wp_issue_tracker_get_referer_redirect_url( $args_to_remove = [] ) {
	$referrer = wp_get_referer();
	if ( $referrer ) {
		$referrer = wp_unslash( $referrer );
	} else {
		$referrer = site_url();
	}

	$args_to_remove = array_merge( [ '_wp_http_referer', '_wpnonce', '_ajax_nonce', 'nonce' ], $args_to_remove );
	$args_to_remove = array_filter( $args_to_remove );
	$args_to_remove = array_unique( $args_to_remove );

	return remove_query_arg( $args_to_remove, $referrer );
}

function wp_issue_tracker_get_issue_tracker_count() {
	$count = get_option( '_wp_issue_tracker_issue_tracker_counter', false );

	if ( false === $count ) {
		$count = wp_count_posts( 'issue_tracker' );
		$count = max( 1, ( $count->private + $count->publish ) );
		update_option( '_wp_issue_tracker_issue_tracker_counter', $count, false );
	}

	return $count;
}

function wp_issue_tracker_update_issue_tracker_count( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return false;
	}

	return update_option( '_wp_issue_tracker_issue_tracker_counter', $count, false );
}

function wp_issue_tracker_get_types() {
	return apply_filters( 'wp_issue_tracker_types', [
		'bug'      => __( 'Bug', 'website-issue-tracker' ),
		'feedback' => __( 'Feedback', 'website-issue-tracker' )
	] );
}

function wp_issue_tracker_create_issue( $content, $type = '', $reporter_email = '', $reporter_name = '', $extra = [], $wp_error = false ) {
	$content = wp_kses_post( $content );
	if ( empty( $content ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'empty-content', __( 'Cannot create issue without content.', 'website-issue-tracker' ) );
		}
		return false;
	}

	$types = wp_issue_tracker_get_types();
	$type = in_array( $type, array_keys( $types ), true ) ? $type : '';

	$meta = [
		'_reporter_name'       => $reporter_name,
		'_reporter_email'      => $reporter_email,
		'_reporter_user_email' => '',
	];

	if ( ! is_array( $extra ) && ! empty( $extra ) ) {
		$meta['extra'] = $extra;
	} else {
		unset( $extra['_reporter_email'], $extra['_reporter_user_email'], $extra['_reporter_name'] );
		$meta = wp_parse_args( $extra, $meta );
	}

	if ( is_user_logged_in() ) {
		global $current_user;
		$meta['_reporter_user_email'] = $current_user->user_email;
		if ( ! $meta['_reporter_email'] ) {
			$meta['_reporter_email'] = $current_user->user_email;
		}
	}

	$counter = wp_issue_tracker_get_issue_tracker_count();
	$issue_types = array( 'issue_types' => $type );
	$args = [
		'post_title'   => 'Issue #' . $counter++,
		'post_content' => $content,
		'post_status'  => 'pending',
		'post_type'    => 'issues',
		'tax_input'    => $issue_types,
		'meta_input'   => $meta,
	];

	$post_id = wp_insert_post( $args, true );
	$status = wp_set_object_terms( $post_id, $issue_types, 'issue_types' );

	do_action( 'wp_issue_tracker_save_issue', $post_id );

	if ( is_wp_error( $post_id ) ) {
		if ( $wp_error ) {
			return $post_id;
		}
		return false;
	}

	wp_issue_tracker_update_issue_tracker_count( $counter );

	return $post_id;
}

function wp_issue_tracker_die( $args = [] ) {
	$args = wp_parse_args( $args, [ 'title' => '', 'message' => '', 'wp_die' => '', 'back' => true ] );
	$args['wp_die']['back_link'] = ! ! $args['back'];
	wp_die(
		wp_kses_post( $args['message' ] ),
		esc_html( wp_strip_all_tags( $args['title'], true ) ),
		$args['wp_die']
	);
}

// End for file helper.php.
