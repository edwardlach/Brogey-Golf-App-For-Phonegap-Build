<?php
session_start();
date_default_timezone_set('America/New_York');


$host="Localhost"; // Host name 
$username="brogeygo"; // Mysql username 
$password="EdandTylershotpar$100"; // Mysql password 
$db_name="brogeygo_wor1"; // Database name 
$tbl_name="bg_app_users"; // Table name 

// Connect to server and select database.
$link = mysqli_connect($host, $username, $password, $db_name);

if (mysqli_connect_error()) {

 	 die("Could not connect to database");

}



$league_id = $_POST['leagueid'];


$delete_league_info = mysqli_query( $link, "DELETE FROM bg_app_league "
					 ."WHERE league_id = '$league_id'" );
					 
$delete_league_invites = mysqli_query( $link, "DELETE FROM bg_app_league_participants "
					."WHERE league_id = '$league_id'" );



?>