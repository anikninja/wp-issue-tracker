<?php
/**
 * ThemeOO Issue Tracker Shortcode
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

if ( ! class_exists( 'Website_Issue_Tracker_Shortcode' ) ) {
	/**
	 * Class Website_Issue_Tracker_Shortcode
	 */
	class Website_Issue_Tracker_Shortcode {

		/**
		 * Singleton instance
		 *
		 * @var Website_Issue_Tracker_Shortcode
		 */
		protected static $instance;

		/**
		 * Get Singleton Instance
		 *
		 * @return Website_Issue_Tracker_Shortcode
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Website_Issue_Tracker_Shortcode constructor.
		 */
		public function __construct() {
			$shortcodes = array(
				'report_a_bug' => __CLASS__ . '::report_bug_button',
				'feedback'     => __CLASS__ . '::feedback_button',
			);
			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
			}
		}

		public static function report_bug_button( $args ) {
			$atts = shortcode_atts(
				[
					'label' => __( 'Report a Bug', '' ),
					'class' => '',
				],
				$args, apply_filters( 'report_a_bug_shortcode_tag', 'report_a_bug' ) );
			$atts['target'] = 'issue-tracker-bug-report';
			$atts['class']  = trim( 'report-a-bug ' . $atts['class'] );
			return wp_issue_tracker_modal_button( $atts, 'a' );
		}

		public static function feedback_button( $args ) {
			$atts = shortcode_atts(
				[
					'label' => __( 'Feedback', '' ),
					'class' => '',
				],
				$args, apply_filters( 'feedback_shortcode_tag', 'feedback' ) );
			$atts['target'] = 'issue-tracker-feedback';
			$atts['class']  = trim( 'feedback ' . $atts['class'] );
			return wp_issue_tracker_modal_button( $atts, 'a' );
		}

	}
}
Website_Issue_Tracker_Shortcode::get_instance();

// End of file class-issue-tracker-shortcode.php.
