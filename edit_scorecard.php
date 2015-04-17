<?php
session_start();
date_default_timezone_set('America/New_York');

if (isset($_POST['hole'])) {
$hole_id = $_POST['hole'];
}
if (isset($_POST['par'])) {
$par = $_POST['par'];
}
if (isset($_POST['score'])) {
$score = $_POST['score'];
}
if (isset($_POST['hole_handicap'])) {
$hole_handicap = $_POST['hole_handicap'];
}
if (isset($_SESSION['round_id'])) {
$round_id = $_SESSION['round_id'];
}




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

if( $par ){

	$scorecard_update_query = "UPDATE `bg_app_holes` "
				 ."SET `par`='$par' "
				 ."WHERE `hole_id` = '$hole_id' AND `round_id` = '$round_id'";
	
	$run_scorecard_update_query = mysqli_query( $link, $scorecard_update_query );	

}elseif( $score ){

	$scorecard_update_query = "UPDATE `bg_app_holes` "
				 ."SET `score`='$score' "
				 ."WHERE `hole_id` = '$hole_id' AND `round_id` = '$round_id'";
			 
	$run_scorecard_update_query = mysqli_query( $link, $scorecard_update_query );	

}elseif( $hole_handicap ){

	$handicap_used_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
						   ."WHERE round_id = '$round_id' "
						   ."AND hole_handicap = '$hole_handicap'" );
	
	$handicap_used = mysqli_num_rows( $handicap_used_query );

	if( $handicap_used == 1 ){
	
		echo "Handicap ".$hole_handicap." was already used.";
	
	}else{
	
		$scorecard_update_query = "UPDATE `bg_app_holes` "
					 ."SET `hole_handicap`='$hole_handicap' "
					 ."WHERE `hole_id` = '$hole_id' AND `round_id` = '$round_id'";
					 
		$run_scorecard_update_query = mysqli_query( $link, $scorecard_update_query );			 
	}

}


			

			 

?>