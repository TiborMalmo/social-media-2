<?php
$facebook_app_id     = '991325357570427';
$facebook_app_secret = 'bf44bc157329b55c4914f327498a1968';
$url                 = ( ! empty( $_SERVER['HTTPS'] ) ) ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];

$fb = new Facebook\Facebook( [
'app_id'                => $facebook_app_id,
'app_secret'            => $facebook_app_secret,
'default_graph_version' => 'v2.5',
] );

if ( get_option( 'facebook-access-token' ) ) {

if ( empty( $_GET['page-id'] ) ) {
die();

} elseif ( $_GET['page-id'] ) {


// Facebook is authenticated, get required feed.


$pageID      = $_GET['page-id'];
$accessToken = get_option( 'facebook-access-token' );
$response    = $fb->get( '/' . $pageID . '/feed?fields=message,created_time,attachments,link,comments.limit(1).summary(true),likes.limit(1).summary(true)', $accessToken );

if ( 200 === $response->getHttpStatusCode() ) {
$jsonfacebook = json_decode( $response->getBody(), true );
foreach ( $jsonfacebook as $value ) {


foreach ( $value as $item ) {
$picturearray = null;
$photopath    = $item['attachments']['data'][0];
if ( isset( $photopath['subattachments'] ) ) {
$photopath = $photopath['subattachments']['data'];
} elseif ( isset( $photopath['media'] ) ) {
$photopath = $item['attachments']['data'];
} else {
$photopath = null;
}

if ( $photopath != null ) {
foreach ( $photopath as $picture ) {
$picturearray[] = $picture['media']['image']['src'];
}
} else {
$picturearray = null;
}

//prevents empty posts.
if ( $item['message'] == 'h' ) {
break;
}
$FBarray[] = array(
'message' => $item['message'],
'date'    => $item['created_time'],
'link'    => $item['link'],
'photos'  => $picturearray
);
}
}
//updates the option.
update_option( 'facebook-json-result', $FBarray );

die();
}
}

} elseif ( ! empty( $_GET['code'] ) ) {

// Get Facebook Token
$helper = $fb->getRedirectLoginHelper();
try {
$accessToken = $helper->getAccessToken();
//update_option('facebook-access-token', $accessToken);
} catch ( Facebook\Exceptions\FacebookResponseException $e ) {
// When Graph returns an error
echo '<p>Graph returned an error: ' . $e->getMessage() . '</p>';
exit;
} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
// When validation fails or other local issues
echo '<p>Facebook SDK returned an error: ' . $e->getMessage() . '</p>';
exit;
}

if ( isset( $accessToken ) && $_GET['status'] === 'approved' ) {
// Logged in!
update_option( 'facebook-access-token', $accessToken->getValue() );

echo '<p>Facebook has responded!</p>';

?>
<form action="" method="post">
	please enter the Page-ID: <input type="text" name="page-id"><br>

	<input type="submit" name="getpageid_submit">

</form>
<?php
}

} elseif ( ! get_option( 'facebook-access-token' ) ) {
	$helper      = $fb->getRedirectLoginHelper();
	$permissions = [ ]; // optional
	$loginUrl    = $helper->getLoginUrl( $url . '/wp-admin/admin.php?page=facebook-feed-login&status=approved', $permissions );

	echo '<a href="' . $loginUrl . '">Authenticate Facebook</a>';
}