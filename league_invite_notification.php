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



$id = $_SESSION['id'];

$leagues_to_accept_query = "SELECT * FROM `bg_app_league_participants` WHERE `user_id` = '$id' AND `is_confirmed` = '0'";

$run_leagues_to_accept_query = mysqli_query( $link, $leagues_to_accept_query );

$number_of_notifications = mysqli_num_rows( $run_leagues_to_accept_query );

if( $number_of_notifications > 0 ){

	$invite_notifications_html = <<<EOHTML
	
		<div>Accept League Invites   <span class="menu_notifications">$number_of_notifications</span></div>

EOHTML;

	echo $invite_notifications_html;


}else{


$invite_notifications_html = <<<EOHTML
	
		<div>Accept League Invites</div>

EOHTML;

	echo $invite_notifications_html;




}











?>