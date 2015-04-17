<?php
session_start();
include "simple_functions.php";
$link = connect_to_database();

$round_id = $_SESSION['round_id'];



$holes_played_query = "SELECT * FROM `bg_app_holes` WHERE `round_id` = '$round_id'";

$holes_played_result = mysqli_query($link, $holes_played_query);	

$holes_played = mysqli_num_rows($holes_played_result);

$current_hole = $holes_played + 1;

$front_back_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$round_id'"; 


$run_front_back_query = mysqli_query($link, $front_back_query);

$front_back_row = mysqli_fetch_row($run_front_back_query);

$front_back = $front_back_row[0];


if ($front_back == "back") {
	
	$current_hole = $current_hole + 9;
}
	 

if($front_back == "both"){
	
	$holes_to_be_played = 18;

}else{

	$holes_to_be_played = 9;
	
}



if( $holes_played < $holes_to_be_played ){
	
	$par		= $_POST['par'];
	$score		= $_POST['score'];
	$hole_handicap	= $_POST['handicap'];
	
  	$run_score_input_query = mysqli_query( $link, 
  		"INSERT INTO bg_app_holes "
  		."(hole_id, round_id, par, score, hole_handicap) "
  		."VALUES('$current_hole', '$round_id', '$par', '$score', '$hole_handicap')"
  	);
	
	if( $holes_played + 1 == $holes_to_be_played ){
		
		$continue = "finished";
	
	}else{
	
		$continue = "continue";
	
	}
	
}else{

	$continue = "finished";
	
}

echo $continue;
	

?>