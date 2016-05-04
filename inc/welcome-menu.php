<!-- This is the start page from the social-media feed. This section doesn't include any functionality more than html-code.-->
<?php include_once __DIR__ . "/CSS/style.css"; ?>
<h2 id="welcomeline"> Welcome to social media-feeds plugin. </h2>

<hr>

<div id = "welcomemenu">

	<h3 id ="feed-welcome-line"> Instagram feeds.</h3>
	<p> This plugin connects a specific instagram account, and converts it to JSON-text. </p>

	<h4> Add new Instagram Feed.</h4>
	<p> To convert Instagram feeds to JSON-text, you need to have access to your instagrams client ID and client secret.
		Simply copy and paste this information to the respective fields on the Instagram submenu-page.
		You can name the feed anything you want, the JSON-information and data about access-tokens will be saved in the database. </p>

	<h4> Edit a instagram-feed.</h4>
	<p> If the feed has expired or you need to change the feed that are specified in the database,
		you can simply navigate to the edit-feed section and delete it.</p>

</div>


<div id= "welcomemenu">
	<h3 id ="feed-welcome-line"> Facebook feeds. </h3>
	<p> Like the instagram JSON-feed, the facebook feed connects the user to a specific page that they are an administrator of.</p>

	<h4> Add new Facebook feed.</h4>
	<p> To convert the facebook page-feed, first of all, you need to authenticate your facebook profile.
		if the profile is successfully authenticated, a text-field should pop up where it asks you to enter the page-ID.
		The Page-id can be found at the bottom in the about-section of the page you administrate, and is usually a number.

	<h4> Edit a Facebook-feed</h4>
	<p> If you wish to delete the facebook-feed, you simply navigate to the edit-feed section and press the corresponding button.</p>



</div>