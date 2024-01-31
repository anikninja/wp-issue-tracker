<?php
/**
 * ThemeOO Issue Tracker PostType
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

if ( ! class_exists( 'Website_Issue_Tracker_Post_Type' ) ) {
	/**
	 * Class Website_Issue_Tracker_Post_Type
	 */
	class Website_Issue_Tracker_Post_Type {

		/**
		 * Singleton instance
		 *
		 * @var Website_Issue_Tracker_Post_Type
		 */
		protected static $instance;

		/**
		 * Get Singleton Instance
		 *
		 * @return Website_Issue_Tracker_Post_Type
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Website_Issue_Tracker_Post_Type constructor.
		 */
		public function __construct() {
			add_action( 'init', [ __CLASS__, 'register_post_type' ] );
			add_action( 'init', [ __CLASS__, 'register_taxonomy' ] );
		}

		/**
		 * Register Issues Post Type.
		 *
		 * @return void
		 */
		public static function register_post_type() {
			if ( post_type_exists( 'issues' ) ) {
				return;
			}

			$labels = array(
				'name'               => _x( 'Issues', 'Issue Tracker PostType Name', 'website-issue-tracker' ),
				'singular_name'      => _x( 'Issues', 'Issue Tracker PostType Singular Name', 'website-issue-tracker' ),
				'add_new'            => _x( 'Add Issue', 'Issue Tracker Add New Button Label', 'website-issue-tracker' ),
				'add_new_item'       => __( 'Add New Issue', 'website-issue-tracker' ),
				'edit_item'          => __( 'Edit Issue', 'website-issue-tracker' ),
				'new_item'           => __( 'New Issue', 'website-issue-tracker' ),
				'view_item'          => __( 'View Issue', 'website-issue-tracker' ),
				'search_items'       => __( 'Search Issues', 'website-issue-tracker' ),
				'not_found'          => __( 'No Issues found', 'website-issue-tracker' ),
				'not_found_in_trash' => __( 'No Issues found in Trash', 'website-issue-tracker' ),
				'parent_item_colon'  => __( 'Parent Issue:', 'website-issue-tracker' ),
				'menu_name'          => _x( 'Issue Tracker', 'Issue Tracker Menu Name', 'website-issue-tracker' ),
			);

			$args = array(
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => __( 'A complete Issue Tracker for WordPress', 'website-issue-tracker' ),
				'supports'            => array( 'title', 'editor', 'author' ),
				'taxonomies'          => array( 'issue_types', 'issue_tags' ),
				'menu_icon'           => null,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 30,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
			);

			register_post_type( 'issues', apply_filters( 'wp_Website_Issue_Tracker_Post_Type_args', $args ) );
		}

		/**
		 * Register taxonomies for Issues
		 *
		 * @return void
		 */
		public static function register_taxonomy() {
			if ( ! taxonomy_exists( 'issue_types' ) ) {
				$type_labels = array(
					'name'              => __( 'Type', 'website-issue-tracker' ),
					'singular_name'     => __( 'Type', 'website-issue-tracker' ),
					'search_items'      => __( 'Search Types', 'website-issue-tracker' ),
					'all_items'         => __( 'All Types', 'website-issue-tracker' ),
					'parent_item'       => __( 'Parent Type', 'website-issue-tracker' ),
					'parent_item_colon' => __( 'Parent Type:', 'website-issue-tracker' ),
					'edit_item'         => __( 'Edit Types', 'website-issue-tracker' ),
					'update_item'       => __( 'Update Types', 'website-issue-tracker' ),
					'add_new_item'      => __( 'Add New Type', 'website-issue-tracker' ),
					'new_item_name'     => __( 'New Type Name', 'website-issue-tracker' ),
					'menu_name'         => __( 'Types', 'website-issue-tracker' ),
				);

				$cat_args = array(
					'hierarchical'      => false,
					'public'            => false,
					'show_tagcloud'     => false,
					'labels'            => $type_labels,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => false,
					'rewrite'           => false,
				);

				register_taxonomy( 'issue_types', array( 'issues' ), $cat_args );
			}

			if ( ! taxonomy_exists( 'issue_tags' ) ) {
				$tag_labels = array(
					'name'              => __( 'Tag', 'website-issue-tracker' ),
					'singular_name'     => __( 'Tag', 'website-issue-tracker' ),
					'search_items'      => __( 'Search Tags', 'website-issue-tracker' ),
					'all_items'         => __( 'All Tags', 'website-issue-tracker' ),
					'parent_item'       => __( 'Parent Tag', 'website-issue-tracker' ),
					'parent_item_colon' => __( 'Parent Tag:', 'website-issue-tracker' ),
					'edit_item'         => __( 'Edit Tags', 'website-issue-tracker' ),
					'update_item'       => __( 'Update Tags', 'website-issue-tracker' ),
					'add_new_item'      => __( 'Add New Tag', 'website-issue-tracker' ),
					'new_item_name'     => __( 'New Tag Name', 'website-issue-tracker' ),
					'menu_name'         => __( 'Tags', 'website-issue-tracker' ),
				);

				$cat_args = array(
					'hierarchical'      => false,
					'public'            => false,
					'show_tagcloud'     => false,
					'labels'            => $tag_labels,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => false,
					'rewrite'           => false,
				);

				register_taxonomy( 'issue_tags', array( 'issues' ), $cat_args );
			}
		}
	}
}
Website_Issue_Tracker_Post_Type::get_instance();

// End of file class-issue-tracker-post-type.php.
