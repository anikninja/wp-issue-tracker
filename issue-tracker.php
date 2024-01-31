<?php
/**
 * Plugin Name: Website Issue Tracker
 * Plugin URI: https://themeoo.com/plugins/website-issue-tracker
 * Description: The best website issue tracker and feedback tool for WordPress.
 * Version: 1.0.0
 * Author: ThemeOO
 * Author URI: https://themeoo.com/
 * License: GPLv2 or later
 * Text Domain: website-issue-tracker
 * Domain Path: /languages/
 * Requires at least: 5.0
 * Requires PHP: 5.6
 *
 * @package WebsiteIssueTracker
 * @version 1.0.0
 */

/**
 * Copyright (c) 2020 ThemeOO
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// Don't call the file directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

if ( ! defined( 'ISSUE_TRACKER_VERSION' ) ) {
	define( 'ISSUE_TRACKER_VERSION', '1.0.0' );
}

if ( ! defined( 'ISSUE_TRACKER_PATH' ) ) {
	/** @define "ISSUE_TRACKER_PATH" "./" */
	define( 'ISSUE_TRACKER_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ISSUE_TRACKER_URL' ) ) {
	define( 'ISSUE_TRACKER_URL', plugin_dir_url( __FILE__ )  );
}

if ( ! class_exists( 'Website_Issue_Tracker' ) ) {
	/**
	 * Class Website_Issue_Tracker
	 */
	class Website_Issue_Tracker {

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * The plugin url
		 *
		 * @var string
		 */
		public $plugin_url;

		/**
		 * The plugin path
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * The theme directory path
		 *
		 * @var string
		 */
		public $theme_dir_path;

		/**
		 * Singleton class instance.
		 *
		 * @var Website_Issue_Tracker
		 */
		protected static $instance;

		/**
		 * Create & return singleton instance of this class.
		 *
		 * @return Website_Issue_Tracker
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Website_Issue_Tracker constructor.
		 *
		 * @return void
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Initialize the plugin
		 *
		 * @return void
		 */
		private function init() {
			add_action( 'plugins_loaded', [ $this, 'file_includes' ] );

			// Localize our plugin.
			add_action( 'init', [ $this, 'localization_setup' ] );

			// Loads frontend scripts and styles.
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			register_activation_hook( __FILE__, [ $this, 'activate' ] );

			add_action( 'wp_footer', [ $this, 'load_modals' ] );
			//add_action('widgets_init', [ $this, 'register_widgets' ] );
		}

		function load_modals() {
			wp_issue_tracker_get_template( 'report-a-bug.php' );
			wp_issue_tracker_get_template( 'feedback.php' );
		}

		/**
		 * Register Widgets.
		 *
		 * @return void
		 */
		public function register_widgets() {
		}

		/**
		 * The plugin activation function
		 *
		 * @return void
		 */
		public function activate() {
			flush_rewrite_rules();
		}

		/**
		 * Load the required files
		 *
		 * @return void
		 */
		public function file_includes() {
			require_once ISSUE_TRACKER_PATH . 'includes/helper.php';
			require_once ISSUE_TRACKER_PATH . 'includes/classes/class-issue-tracker-form-handler.php';
			require_once ISSUE_TRACKER_PATH . 'includes/classes/class-issue-tracker-post-type.php';
			require_once ISSUE_TRACKER_PATH . 'includes/classes/class-issue-tracker-shortcode.php';
			require_once ISSUE_TRACKER_PATH . 'includes/classes/class-issue-tracker-settings-api.php';
			require_once ISSUE_TRACKER_PATH . 'includes/classes/class-issue-tracker-settings.php';

		}

		/**
		 * Initialize plugin for localization
		 *
		 * @uses load_plugin_textdomain()
		 */
		public function localization_setup() {
			load_plugin_textdomain( 'website-issue-tracker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Enqueue admin scripts
		 *
		 * Allows plugin assets to be loaded.
		 *
		 * @uses wp_enqueue_script()
		 * @uses wp_enqueue_style
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'website-issue-tracker', $this->plugin_url( 'assets/js/scripts.js' ), [ 'jquery' ], $this->version, true );
			wp_enqueue_style( 'website-issue-tracker', $this->plugin_url( 'assets/css/styles.css' ), [], $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @param string $path path to get.
		 *
		 * @return string
		 */
		public function plugin_url( $path = null ) {
			if ( ! $this->plugin_url ) {
				$this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
			}
			if ( ! $path ) {
				return $this->plugin_url;
			}
			$path = ltrim( $path, '/\\' );
			$path = rtrim( $path, '/\\' );

			return $this->plugin_url . '/' . $path;
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			if ( ! $this->plugin_path ) {
				$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			}
			return $this->plugin_path;
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'wp_issue_tracker_template_path', 'issue-tracker/');
		}

		/**
		 *  Create a custom post type
		 */
		function custom_post_type() {
			register_post_type( 'issue', ['public' => 'true'] );
		}
	}
	// End of Class Issue_Tracker
}

/**
 * Initialize the plugin
 *
 * @return Website_Issue_Tracker
 */
function Website_Issue_Tracker() {
	return Website_Issue_Tracker::get_instance();
}

// Kick it off.
Website_Issue_Tracker();
// End of file issue-tracker.php.
