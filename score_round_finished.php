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

$front_back_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$query_round_id'"; 

$run_front_back_query = mysqli_query($link, $front_back_query);

$front_back_row = mysqli_fetch_row($run_front_back_query);

$front_back = $front_back_row[0];

if($front_back == "both"){
	
	$holes_to_play = 17;

}else{

	$holes_to_play = 8;
	
}


$number_of_holes_played_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
						    ."WHERE round_id = '$query_round_id' " );
$number_of_holes_played = mysqli_num_rows($number_of_holes_played_query);						    

if( $number_of_holes_played >= $holes_to_play ){
	
	$continue = "finished";
	
}else{
	
	$continue = "continue";
	
}


echo $continue;


?>