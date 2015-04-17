<?php
session_start();
include "simple_functions.php";
$link = connect_to_database();

$round_id = $_SESSION['round_id'];



$scorecard_query = "SELECT * FROM `bg_app_holes` "
		  ."WHERE `round_id` = '$round_id' "
		  ."ORDER BY `hole_id` ASC";
		  		 
$run_scorecard_query = mysqli_query( $link, $scorecard_query );

$scorecard_results = array();


while( $scorecard_row = mysqli_fetch_array( $run_scorecard_query, MYSQLI_ASSOC ) ){
	
	array_push( $scorecard_results, $scorecard_row );

}

foreach($scorecard_results as $hole_score) {
	
	$score += ($hole_score['score']);
	
}


foreach($scorecard_results as $hole_par) {
		
	$par += ($hole_par['par']);
		
}
	
$score_from_par = $score - $par;

echo $score_from_par;
	
?>
