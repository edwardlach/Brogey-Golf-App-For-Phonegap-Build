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

$query_round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );

$hole_handicap = $_POST['handicap'];


$handicap_used_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
					   ."WHERE round_id = '$query_round_id' "
					   ."AND hole_handicap = '$hole_handicap'" );



$handicap_used = mysqli_num_rows( $handicap_used_query );				  


echo $handicap_used;








?>