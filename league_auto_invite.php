<?php
session_start();


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

	
$invite_id = $_GET['leagueid'];
$email = $_GET['email'];

	
$league_start_query = mysqli_query( $link, "SELECT start_date FROM bg_app_league "
				    	  ."WHERE league_id = '$invite_id'");
$league_start_row = mysqli_fetch_row( $league_start_query );
$league_start = $league_start_row[0];

$today = new DateTime('now');
$start_date_time = new DateTime($league_start);

if( $today < $start_date_time ){

 	$user_id_query = mysqli_query( $link, "SELECT user_id FROM bg_app_users "
 					     ."WHERE email_address = '$email'" );
	$user_id_row = mysqli_fetch_row( $user_id_query );
	$user_id = $user_id_row[0];
 	
 	$add_new_member_query = mysqli_query( $link, "INSERT INTO bg_app_league_participants"
 						    ."(league_id, user_id, is_confirmed) "
						    ."VALUES('$invite_id', '$user_id', '0')" );	
						    
}





header( 'Location: login.php' );





?>