<?php
/**
 * Created by PhpStorm.
 * User: Tibor
 * Date: 2016-05-04
 * Time: 09:30
 */
?>
<h2> Edit existing feed </h2>

			<br>
			<h4 id="line"> Delete instagram feed?</h4>

			<form action="" method="post">
			<input type="hidden" name="igfeed" value="submit" />
			<input id="" type="submit" name="submit" value="Delete Instagram feed">
			</form>
<?php

// deletes the instagram values that are saved in the database.
if(isset($_POST['igfeed'])) {
	delete_option( 'instagram_settings' );
	delete_option( 'instagram-access-token' );
	delete_option( 'Instagram_results' );
	echo "the values has now been deleted!";
}
// if there are no more values in the database, a message will be outputted on the screen.
if ( get_option( 'instagram_settings' ) == false || get_option( 'instagram-access-token' ) == false || get_option( 'Instagram_results' ) == false ) {
	?>
	<h4>    <?php echo 'there are no more Instagram values in the database that can be deleted!'; ?> </h4> <?php
}


?>
<br><br>
<hr>
<h4 id="line">Delete facebook-JSON-feed? </h4>
<form action="" method="post">
	<input type="hidden" name="fbfeed" value="submit" />

	<input id="" type="submit" name="submit" value="Delete Facebook feed">
</form>

<?php
// deletes the facebookfeed from the database.
if(isset($_POST['fbfeed'])){
	delete_option('facebook-access-token');
	delete_option('page-id');
	delete_option('facebook-json-result');
	echo 'The facebook values has now been deleted!';
}
if(get_option('facebook-access-token') == false || get_option('page-id') == false || get_option('facebook-json-result') == false){
	?><h4>	<?php echo  'there are no more facebook values in the database that can be deleted!'; ?> </h4> <?php
}


?>

