<?php
/**
 * ThemeOO Issue Tracker FormHandler
 *
 * @package WebsiteIssueTracker
 * @version 1.0.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

if ( ! class_exists( 'Website_Issue_Tracker_Form_Handler' ) ) {
	/**
	 * Class Website_Issue_Tracker_Form_Handler
	 */
	class Website_Issue_Tracker_Form_Handler {

		/**
		 * Singleton instance
		 *
		 * @var Website_Issue_Tracker_Form_Handler
		 */
		protected static $instance;

		/**
		 * Get Singleton Instance
		 *
		 * @return Website_Issue_Tracker_Form_Handler
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Website_Issue_Tracker_Form_Handler constructor.
		 */
		public function __construct() {
			$actions = [
				'wp_issue_tracker_report_a_bug' => 'bug_report_callback',
				'wp_issue_tracker_feedback'     => 'feedback_callback',
			];

			foreach ( $actions as $action => $handler ) {
				add_action( "admin_post_nopriv_{$action}", [ $this, $handler ], -1 );
				add_action( "admin_post_{$action}", [ $this, $handler ], -1 );
			}
		}

		public function get_feedback_fields() {
			return apply_filters( 'wp_issue_tracker_feedback_fields', [
				'feedback_reporter_name'     => [
					'label'       => __( 'Your Name', 'website-issue-tracker' ),
					'placeholder' => __( 'Your Name', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'text',
				],
				'feedback_reporter_email'    => [
					'label'       => __( 'Your Email', 'website-issue-tracker' ),
					'placeholder' => __( 'Your Email', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'email',
					'required'    => true,
				],
				'feedback' => [
					'label'       => __( 'Your Feedback', 'website-issue-tracker' ),
					'placeholder' => __( 'Your Feedback', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'textarea',
					'required'    => true,
				]
			] );
		}

		public function get_bug_report_fields() {
			return apply_filters( 'wp_issue_tracker_report_a_bug_fields', [
				'bug_reporter_name'  => [
					'label'       => __( 'Your Name', 'website-issue-tracker' ),
					'placeholder' => __( 'Your Name', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'text',
				],
				'bug_reporter_email' => [
					'label'       => __( 'Your Email', 'website-issue-tracker' ),
					'placeholder' => __( 'Your Email', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'email',
					'required'    => TRUE,
				],
				'bug_report'         => [
					'label'       => __( 'Bug Details', 'website-issue-tracker' ),
					'placeholder' => __( 'Bug Details', 'website-issue-tracker' ),
					'value'       => '',
					'type'        => 'textarea',
					'required'    => TRUE,
				]
			] );
		}

		/**
		 * bug submission form.
		 */
		function bug_report_callback() {
			if ( isset( $_POST['_issue_tracker_bug_report_nonce'] ) && wp_verify_nonce( $_POST['_issue_tracker_bug_report_nonce'], 'website-issue-tracker-report-a-bug' ) ) {
				$bug_message = ! empty ( $_POST['bug_report'] ) ? sanitize_textarea_field( $_POST['bug_report'] ) : '';

				if ( empty ( $bug_message ) ) {
					wp_issue_tracker_die( [
						'title'   => __( 'Invalid Request', 'website-issue-tracker' ),
						'message' => __( 'Please describe the issue.', 'website-issue-tracker' ),
					] );
				}

				$config = $this->get_bug_report_fields();

				$email = '';
				$email_required = isset( $config['bug_reporter_email'], $config['bug_reporter_email']['required'] ) ? $config['bug_reporter_email']['required'] : false;
				$name_required  = isset( $config['bug_reporter_name'], $config['bug_reporter_name']['required'] ) ? $config['bug_reporter_name']['required'] : false;

				if ( isset( $_POST['bug_reporter_email'] ) && is_email( $_POST['bug_reporter_email'] ) ) {
					$email = sanitize_email( $_POST['bug_reporter_email'] );
				}

				if ( empty( $email ) && $email_required ) {
					wp_issue_tracker_die( [
						'title'   => __( 'Invalid Request', 'website-issue-tracker' ),
						'message' => __( 'Empty Or Invalid Email', 'website-issue-tracker' ),
					] );
				}

				$name = '';

				if ( isset( $_POST['bug_reporter_name'] ) ) {
					$name = sanitize_text_field( $_POST['bug_reporter_name'] );
				}

				if ( empty( $name ) && $name_required ) {
					wp_issue_tracker_die( [
						'title'   => __( 'Invalid Request', 'website-issue-tracker' ),
						'message' => __( 'Your Name Is Missing', 'website-issue-tracker' ),
					] );
				}

				$page     = ( isset( $_POST['page'] ) ) ? esc_url_raw( $_POST['page'] ) : wp_issue_tracker_get_referer_redirect_url();
				$redirect = ( isset( $_POST['redirect_to'] ) ) ? esc_url( $_POST['redirect_to'] ) : $page;

				$issue = wp_issue_tracker_create_issue( $bug_message, 'bug', $email, $name, [ 'report_page' => $page ], TRUE );

				if ( is_wp_error( $issue ) ) {
					wp_die( $issue->get_error_message(), $issue->get_error_data(), [ 'back_link' => TRUE ] );
				}

				setcookie( 'website-issue-tracker_success_msg', __( 'Your Bug Report Successfully Submitted.', 'website-issue-tracker' ), time() + 30, '/' );
				wp_redirect( $redirect );
				die();
			}
			wp_die(
				esc_html__( 'Invalid Request.', 'website-issue-tracker' ),
				esc_html__( 'Unauthorized', 'website-issue-tracker' ),
				[ 'back_link' => TRUE ]
			);
		}

		/**
		 * feedback submission form.
		 */
		function feedback_callback() {
			if ( isset( $_POST['_issue_tracker_feedback_nonce'] ) && wp_verify_nonce( $_POST['_issue_tracker_feedback_nonce'], 'website-issue-tracker-feedback' ) ) {
				$feedback_message = ! empty ( $_POST['feedback'] ) ? sanitize_textarea_field( $_POST['feedback'] ) : '';

				$config = $this->get_feedback_fields();

				$email = '';
				$email_required = isset( $config['feedback_reporter_email'], $config['feedback_reporter_email']['required'] ) ? $config['feedback_reporter_email']['required'] : false;
				$name_required  = isset( $config['feedback_reporter_name'], $config['feedback_reporter_name']['required'] ) ? $config['feedback_reporter_name']['required'] : false;

				if ( isset( $_POST['feedback_reporter_email'] ) && is_email( $_POST['feedback_reporter_email'] ) ) {
					$email = sanitize_email( $_POST['feedback_reporter_email'] );
				}

				if ( empty( $email ) && $email_required ) {
					wp_issue_tracker_die( [
						'title'   => __( 'Invalid Request', 'website-issue-tracker' ),
						'message' => __( 'Empty Or Invalid Email', 'website-issue-tracker' ),
					] );
				}

				$name = '';

				if ( isset( $_POST['feedback_reporter_name'] ) ) {
					$name = sanitize_text_field( $_POST['feedback_reporter_name'] );
				}

				if ( empty( $name ) && $name_required ) {
					wp_issue_tracker_die( [
						'title'   => __( 'Invalid Request', 'website-issue-tracker' ),
						'message' => __( 'Your Name Is Missing', 'website-issue-tracker' ),
					] );
				}

				$page     = ( isset( $_POST['page'] ) ) ? esc_url_raw( $_POST['page'] ) : wp_issue_tracker_get_referer_redirect_url();
				$redirect = ( isset( $_POST['redirect_to'] ) ) ? esc_url( $_POST['redirect_to'] ) : $page;

				$issue = wp_issue_tracker_create_issue( $feedback_message, 'feedback', $email, $name, [ 'report_page' => $page ], TRUE );

				if ( is_wp_error( $issue ) ) {
					wp_die( $issue->get_error_message(), $issue->get_error_data(), [ 'back_link' => TRUE ] );
				}

				setcookie( 'website-issue-tracker_success_msg', __( 'Your Feedback Successfully Submitted.', 'website-issue-tracker' ), time() + 30, '/' );
				wp_redirect( $redirect );
				die();
			}
			wp_die(
				esc_html__( 'Invalid Request.', 'website-issue-tracker' ),
				esc_html__( 'Unauthorized', 'website-issue-tracker' ),
				[ 'back_link' => TRUE ]
			);
		}
	}
}
Website_Issue_Tracker_Form_Handler::get_instance();

// End of file class-issue-tracker-form-handler.php.
