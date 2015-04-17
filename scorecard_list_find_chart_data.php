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

$user_id_to_calculate_handicap = $_SESSION['id'];

include "calculate_handicap.php";
$handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap); 

$query_user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );


/* query to pull all of the scorecards successfully submitted */

$scorecard_list_query = "SELECT * FROM `bg_app_rounds` "
			."WHERE `user_id` = '$query_user_id' "
			."AND `is_complete` = '1' "
			."ORDER BY `start_date` ASC";

$run_scorecard_list_query = mysqli_query( $link, $scorecard_list_query );

$scorecard_list = array();

while( $scorecard_list_row = mysqli_fetch_array( $run_scorecard_list_query, MYSQLI_ASSOC ) ){

	array_push( $scorecard_list, $scorecard_list_row );
	
}


$chart_data = array();

foreach( $scorecard_list as $row ){

	$date		= $row['start_date'];
	$front_back	= $row['front_back_both'];
	$front_score 	= $row['front_score'];
	$back_score	= $row['back_score'];
	$front_par	= $row['front_par'];
	$back_par	= $row['back_par'];
	$course_name	= $row['course_name'];
	
	if( $front_back == "both" ){
		
		$score = $front_score + $back_score;
		$par = $front_par + $back_par;
		
	}elseif( $front_back == "back" ){
		
		$score = $back_score * 2;
		$par = $back_par * 2;
	
	}else{
	
		$score = $front_score * 2;
		$par = $front_par * 2;
		
	}

	$strokes_from_par = $score - $par;
	
	if( $strokes_from_par > $handicap ){
	
		$above_below = "above";
	
	}else{
		
		$above_below = "below";
	
	}

	array_push( $chart_data, array( 
					'date'		=> $date, 
					'strokes'	=> $strokes_from_par,
					'course_name'	=> $course_name,
					'score'		=> $score,
					'par'		=> $par,
					'above_below'	=> $above_below
				)
	);
	
}


//var_dump($chart_data);
	
echo json_encode($chart_data);



?>