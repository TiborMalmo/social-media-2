<?php
/**
 * Created by PhpStorm.
 * User: Tibor
 * Date: 2016-05-04
 * Time: 09:20
 */

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

