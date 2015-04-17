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


$id	 	= mysqli_real_escape_string( $link, $_SESSION['id'] );
$league_id 	= $_POST['leagueid'];
$accept		= $_POST['accept'];


if( $accept == 1 ){

	$run_invite_accept_query = mysqli_query( $link, "UPDATE bg_app_league_participants "
							."SET is_confirmed = '$accept' "
							."WHERE user_id = '$id' AND league_id = '$league_id'");
	
	
}else{

	$run_invite_decline_query = mysqli_query( $link, "DELETE FROM bg_app_league_participants "
						."WHERE user_id = '$id' AND league_id = '$league_id'" );

}





?>