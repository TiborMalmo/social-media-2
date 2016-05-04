<?php
// if not accessed by wordpress, you don't get permission to access the code.
defined( 'ABSPATH' ) or die( 'Access denied' );

/**
 * Plugin Name: Social media plugin.
 * Plugin URI: http://www.iqq.se
 * Description: A plugin for Wordpress that connects a specific feeds Facebook and Instagram page and converts it to JSON-text.
 * Author: Tibor Lundberg, intern @ IQQ.
 * Version: 0.0.2
 */
class social_media {
	static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_files' ) );
		add_action( 'init', array( __CLASS__, 'myStartSession' ), 1 );
		add_action( 'wp_logout', array( __CLASS__, 'myEndSession' ) );
		add_action( 'wp_login', array( __CLASS__, 'myEndSession' ) );
		add_action( 'admin_init', array( __CLASS__, 'redirect_facebook' ) );
		add_action( 'admin_menu', array( __CLASS__, 'create_plugin_menu' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'my_activation' ) );
		register_deactivation_hook( __FILE__, 'my_deactivation' );
		add_action( 'my_hourly_event', array( __CLASS__, 'do_this_hourly' ) );
	}

	public function admin_enqueue_files() {
		wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . '/js/app.js' );
	}
	static function style(){
		wp_enqueue_style('style', plugin_dir_url(__FILE__) . '/style.css');
	}



	public function myStartSession() {
		if ( ! session_id() ) {
			session_name( 'social-media-plugin-session' );
			session_start();
		}
	}

	public function myEndSession() {
		session_destroy();
	}

	public function create_plugin_menu() {
		//create new top-level menu with a dashicon.
		add_menu_page( 'Social-media-plugin', 'Feeds', 'administrator', 'social-media-feeds', array(
			__CLASS__,
			'feeds_page'
		), do_shortcode( 'dashicons-share' ) );
		add_submenu_page( 'social-media-feeds', 'Add new instagram feed', 'Add new instagram feed', 'administrator', 'social-media-instagram-feed', array(
			__CLASS__, 'page_add_instagram_feed') );
		add_submenu_page( 'social-media-feeds', 'delete-existing-feed', 'Delete existing feed', 'administrator', 'delete-feed', array(
			__CLASS__, 'page_delete_feed') );
		add_submenu_page( 'social-media-feeds', 'Facebook-feed-login', 'Facebook feed login', 'administrator', 'facebook-feed-login', array(
			__CLASS__,
			'page_facebook_feed'
		) );

	}

	/**
	 * The welcome menu/start page.
	 */
	public function feeds_page() {
		include_once __DIR__ . "/inc/welcome-menu.php";
	}

	/**
	 * The Instagram page.
	 */
	public function page_add_instagram_feed(){
		echo "<div class='wrap'>";
		// information-text. 
		include_once __DIR__ . "/inc/instagram-information.php";

	}

	/**
	 * Delete feeds-page.
	 */
	public function page_delete_feed(){
		echo "<div class='wrap'>";
	include_once __DIR__ . "/inc/delete-feeds.php";

	}

	/**
	 * The Facebook page.
	 */
	public function page_facebook_feed() {
		echo "<div class='wrap'>";
		/* Information text */
		include_once __DIR__ . "/inc/facebook-information.php";

		/**
		 * submits the page-id from the form. (further down).
		 */

		echo "<h2>Authenticate facebook and enter page id.</h2>";
		echo "<h4>After you authenticate your facebook, please enter the page id.</h4>";

		/**
		 * Check database if Facebook settings exists
		 */
		self::facebook_checkup();

		/**
		 * Imports the facebook API.
		 */

		self::facebook_API();

		echo "</div>";
	}

	public function facebook_checkup() {
		echo "<p>Database status:</p>";
		echo "<p>JSON-result:</p>";
		if ( get_option( 'facebook-json-result' ) == true ) {
			echo '<p>The JSON-result is now in the database!</p>';
		} else {
			echo '<p>JSON-data not inserted in the database...yet.</p>';
		}
		echo "<p>Facebook access-token:</p>";
		if ( get_option( 'facebook-access-token' ) == true ) {
			echo '<p>the access-token is now in the database!</p>';
		} else {
			echo '<p>The access-token is not in the database...yet.</p>';
		}
		echo "<p>Page-id</p>";
		if ( get_option( 'page-id' ) == true ) {
			echo '<p>the Page-ID is now in the database!</p>';
		} else {
			echo '<p>The Page-ID is not in the database..yet.</p>';
		}

	}

	static function redirect_facebook() {
		if ( isset( $_POST['getpageid_submit'] ) ) {
			update_option( 'page-id', $_POST['page-id'] );
			wp_redirect( 'http://tibor.dev/wp-admin/admin.php?page=facebook-feed-login&page-id=' . get_option( 'page-id' ) );
			exit;
		}
	}

	public function facebook_API() {
		include_once __DIR__ . '/facebook-php-sdk-v4-master/src/Facebook/autoload.php';
		include_once __DIR__ . '/inc/facebook-auth-setup.php';
	}


	/**LATER STUFF**/

	public function do_this_hourly() {
		update_option( 'test_timer', 'test' );
	}


	public function my_activation() {
		if ( ! wp_next_scheduled( 'my_hourly_event' ) ) {
			wp_schedule_event( 1462264665, 'hourly', 'my_hourly_event' );
		}
		wp_schedule_update_checks();
	}

	public function my_deactivation() {
		wp_clear_scheduled_hook( 'my_hourly_event' );
	}

	/*INSTAGRAM LATER STUFF*/

}

social_media::init();


