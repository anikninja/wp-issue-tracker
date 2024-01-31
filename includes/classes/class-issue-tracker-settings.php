<?php
/**
 * weDevs Settings API wrapper class
 *
 * @version 1.3 (27-Sep-2016)
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @link https://tareq.co Tareq Hasan
 * @example example/oop-example.php How to use the class
 */
if ( !class_exists( 'Website_Issue_Tracker_Settings' ) ):
	class Website_Issue_Tracker_Settings {
		/**
		 * Singleton instance
		 *
		 * @var Website_Issue_Tracker_Settings
		 */
		protected static $instance;

		/**
		 * Get Singleton Instance
		 *
		 * @return Website_Issue_Tracker_Settings
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function __construct() {
			$this->settings_api = new Website_Issue_Tracker_Settings_API;

			add_action( 'admin_init', array($this, 'admin_init') );
			add_action( 'admin_menu', array($this, 'admin_menu') );
		}

		function admin_init() {

			//set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			//initialize settings
			$this->settings_api->admin_init();
		}

		function admin_menu() {
			// add_options_page( 'Settings API', 'Settings API', 'delete_posts', 'settings_api_test', array($this, 'plugin_page') );
			add_submenu_page( 'edit.php?post_type=issues', 'Settings', 'Settings', 'manage_options', 'settings', [$this, 'plugin_page'] );
		}

		function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'general',
					'title' => __( 'General Settings', 'wedevs' )
				),
				array(
					'id'    => 'shortcodes',
					'title' => __( 'Shortcodes', 'wedevs' )
				),
				array(
					'id'    => 'information',
					'title' => __( 'Information', 'wedevs' )
				)
			);
			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			$settings_fields = array(
				'general' => array(
					array(
						'name'    => 'show_feedback_wp_login',
						'label'   => __( 'Show Feedback in wp-login.php', 'wedevs' ),
						//'desc'    => __( 'Dropdown description', 'wedevs' ),
						'type'    => 'select',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no'  => 'No'
						)
					),
					array(
						'name'    => 'show_bug_report_wp_login',
						'label'   => __( 'Show Bug Report in wp-login.php', 'wedevs' ),
						//'desc'    => __( 'Dropdown description', 'wedevs' ),
						'type'    => 'select',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no'  => 'No'
						)
					)
				),
				'shortcodes' => array(
					array(
						'name'              => 'feedback_shortcode',
						'label'             => __( 'Feedback Button Shortcode', 'wedevs' ),
						'desc'              => __( 'Supported attributes: <code>class</code> <code>label</code>', 'wedevs' ),
						//'placeholder'       => __( 'Text Input placeholder', 'wedevs' ),
						'type'              => 'text',
						'default'           => '[feedback]',
						'sanitize_callback' => 'sanitize_text_field'
					),
					array(
						'name'              => 'bug_shortcode',
						'label'             => __( 'Bug Report Button Shortcode', 'wedevs' ),
						'desc'              => __( 'Supported attributes: <code>class</code> <code>label</code>', 'wedevs' ),
						//'placeholder'       => __( 'Text Input placeholder', 'wedevs' ),
						'type'              => 'text',
						'default'           => '[report_a_bug]',
						'sanitize_callback' => 'sanitize_text_field'
					)
				),
				'information' => array(
					array(
						'name'        => 'html',
						'label'             => __( 'Info URL', 'wedevs' ),
						'desc'        => __( 'Website Issue Tracker Demo Page Link: <a href="#">Ckick this link</a>', 'wedevs' ),
						'type'        => 'html'
					)
				)
			);

			return $settings_fields;
		}

		function plugin_page() {
			echo '<div class="wrap">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ($pages as $page) {
					$pages_options[$page->ID] = $page->post_title;
				}
			}

			return $pages_options;
		}

	}

endif;
Website_Issue_Tracker_Settings::get_instance();
