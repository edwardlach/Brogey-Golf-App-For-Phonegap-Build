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


$round_id = $_POST['roundid'];


$delete_round_info = mysqli_query( $link, "DELETE FROM bg_app_rounds "
					 ."WHERE round_id = '$round_id'" );
					 
$delete_hole_info = mysqli_query( $link, "DELETE FROM bg_app_holes "
					."WHERE round_id = '$round_id'" );




?>