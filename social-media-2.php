<?php
// if not accessed by wordpress, you don't get permission to access the code.
defined( 'ABSPATH' ) or die( 'Access denied' );

/**
 * Plugin Name: Simple social media plugin v 0.0.2
 * Plugin URI: http://www.iqq.se
 * Description: This is a simple plugin that converts a users Facebook-page or Instagram-feed to JSON-text and saves it in the database.
 * Author: Made by Tibor Lundberg, student intern @ IQQ.
 * Version: 0.0.2 alfa
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
		wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ) . 'CSS/style.css' );
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
			__CLASS__,
			'page_add_instagram_feed'
		) );
		add_submenu_page( 'social-media-feeds', 'delete-existing-feed', 'Delete existing feed', 'administrator', 'delete-feed', array(
			__CLASS__,
			'page_delete_feed'
		) );
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
	public function page_add_instagram_feed() {
		echo "<div class='wrap'>";
		if(! empty( $_GET['code'])){
			self::DWWP_instagram_api();

		}

// information-text.

		include_once __DIR__ . "/inc/instagram-information.php";
		if ( isset( $_POST['submit_feed'] ) ) {
			//This messages pops up when the client id and client secret are saved in the database.
			echo "<pre>Saved into the DB!</pre>";
			if ( isset( $_GET['response_type'] ) ) {
				echo $_GET['response_type'];
			}

			// declares the local variables that saves the information in the database under instagram-settings.
			$clientID     = preg_replace( '/\s+/', '', $_POST['client_id'] );
			$clientSecret = preg_replace( '/\s+/', '', $_POST['client_secret'] );
			$option       = "instagram_settings";

			$array = array(
				'client_id'     => $clientID,
				'client_secret' => $clientSecret,
				'code'          => $_GET['code'],
			);

			// serializes the values.
			serialize( $array );

			update_option( $option, $array );
			require_once __DIR__ . "/inc/instagram-code.php";

			// if everything works as it should pop up a link after the user-inputted values have been saved that requests the access token from the instagram API.
			echo "<a href='" . $url . "'>fetch instagram code </a>";


		}
		?>
		<form method="post" action="">
			<?php settings_fields( 'settings-group' ); ?>
			<?php do_settings_sections( 'settings-group' ); ?>
			<?php
			$DB = get_option( 'instagram_settings' )
			?>
			<table class="form-table">


				<!--Declares and creates the client-id field. -->
				<tr valign="top">
					<th scope="row"><?php _e( 'Client ID:', 'instagram' ) ?></th>
					<td><input type="text" name="client_id"/></td>
				</tr>

				<!-- Declares and creates the Client-secret field.-->
				<tr valign="top">
					<th scope="row"><?php _e( 'Client Secret:', 'instagram' ) ?></th>
					<td><input type="text" name="client_secret"
						/></td>
				</tr>


			</table>

			<br>
			<br>

			<!-- Input that saves the values specified in the instagram page-->
			<input type="submit" class="btn btn-prime" name="submit_feed" value="<?php _e( 'Save' ) ?>">

		</form>
		<b>Database status:</b>
		<p>Database status - Instagram settings: <?php if ( get_option( 'instagram_settings' ) == true ) {
				echo 'The Instagram settings is now in the database!';
			} else {
				echo 'The instagram settings is not in the database...yet. ';
			}
			?> </p>
		<p> Database status - Instagram JSON-results: <?php if ( get_option( 'Instagram_results' ) == true ) {
				echo 'The instagram JSON-results is now in the database!';
			} else {
				echo 'The instagram JSON-results is not in the database...yet.' . '<br>' . 'If its not working, make sure the client ID and the client secret is correctly written. ';
			} ?> </p>

		<p id="igactoken"> Database status - Instagram
			access-token: <?php if ( get_option( 'instagram-access-token' ) == true ) {
				echo 'The Instagram access-token is now in the database!';
			} else {
				echo 'The instagram access-token is not in the database...yet.' . '<br>' . ' If its not working, make sure the client ID and the client secret is correctly written.';
			} ?></p>


		<?php
		echo "</div>"; //wrap ends here


	}


	// this function takes the user-stored instagram values and uses this information to send a request for an access-token.
	static function DWWP_instagram_api() {

		// if the response code from instagram is accessable, start the method

			// get the database table 'instagram settings'
			$instagram_settings = get_option( 'instagram_settings' );

			// get the returned client id.
			$get_client_id = $instagram_settings['client_id'];
			// get the returned client secret.

			$get_client_secret = $instagram_settings['client_secret'];
			// code used in the code.php file.
			$get_code = $_GET ['code'];

			// array that saves the values that are returned.
			$args = array(
				'body' => array(
					'client_id'     => $get_client_id,
					'client_secret' => $get_client_secret,
					'code'          => $_GET['code'],
					//grant type responds with the instagram scope.
					'grant_type'    => 'authorization_code',
					'redirect_uri'  => 'http://tibor.dev/wp-admin/admin.php?page=social-media-instagram-feed'
				)
			);

			// specifies where to get the access-token.
			$url = 'https://api.instagram.com/oauth/access_token';
			// takes the array and posts it in the url.
			$response = wp_remote_post( $url, $args );

			// If the instagram site responds correctly, then the access-token is saved.
			if ( wp_remote_retrieve_response_code( $response ) === 200 ) {

				$body = wp_remote_retrieve_body( $response );
				$body = json_decode( $body );
				if ( ! empty( $body->access_token ) ) {

					update_option( 'instagram-access-token', $body->access_token );

				}

			}

			// validation, if it's not responding with code 200, the rest of the information is deleted.
			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {

				delete_options( 'instagram_settings' );
				delete_options( 'instagram-access-token' );
				delete_options( 'instagram_results' );

				echo "<p>something went wrong. Please try again</p>";

			}
			echo "<p>Access-token fetched and saved in the db!</p>";
			echo self::DWWP_instagram_fetch_feed();

	}

	// Access the json-text from the web-client into a new database row.
	public function DWWP_instagram_fetch_feed(){

		$access_token             = get_option( 'instagram-access-token' );
		$get_json_text_ig         = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $access_token;
		$list_of_json_stuff       = file_get_contents( $get_json_text_ig );
		$get_json_text_ig_decoded = json_decode( $list_of_json_stuff, true );

		// loop that access each individual value in the JSON-text.
		foreach ( $get_json_text_ig_decoded as $value ) {
			foreach ( $value as $instagramInfo ) {
				$caption   = $instagramInfo ['caption']['text'];
				$date      = $instagramInfo['created_time'];
				$realdate  = date( 'd/m/Y', $date );
				$checkup   = $instagramInfo['type'];
				$img_url   = $instagramInfo['images']['standard_resolution']['url'];
				$video_url = $instagramInfo['videos']['standard_resolution']['url'];


				// Checkup that checks video or image JSON-value, and if there is a caption or not.

				if ( $checkup === 'video' ) {
					if ( $caption != null ) {

						$videoSetting[] = array(
							'url'      => $video_url,
							'realdate' => $realdate,
							'caption'  => $caption,

						);
					} else {
						$videoSetting[] = array(
							'url'      => $video_url,
							'realdate' => $realdate,
						);
					}

				} elseif ( $checkup === 'image' ) {

					if ( $caption != null ) {


						$imageSetting[] = array(
							'url'      => $img_url,
							'realdate' => $realdate,
							'caption'  => $caption
						);
					} else {
						$imageSetting[] = array(
							'url'      => $img_url,
							'realdate' => $realdate,
						);
					}
				}


			}

		} //where the loop ends.


		$instagramResults = "Instagram_results";

		// merging the image and video array.
		$igArrays = array_merge( $imageSetting, $videoSetting );

		// sorts the date.
		usort( $igArrays, function ( $a, $b ) {
			return $b['realdate'] - $a['realdate'];
		} );

		// saves the database.
		update_option( $instagramResults, $igArrays );
	}


	/**
	 * Delete feeds-page.
	 */
	public function page_delete_feed() {
		echo "<div class='wrap'>";
		include_once __DIR__ . "/inc/delete-feeds.php";

		echo "</div>";
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
		echo self::DWWP_instagram_fetch_feed();
	}


	public function my_activation() {
		if ( ! wp_next_scheduled( 'my_hourly_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'my_hourly_event' );
		}
		wp_schedule_update_checks();
	}

	public function my_deactivation() {
		wp_clear_scheduled_hook( 'my_hourly_event' );
	}

	/*INSTAGRAM LATER STUFF*/

}

social_media::init();


