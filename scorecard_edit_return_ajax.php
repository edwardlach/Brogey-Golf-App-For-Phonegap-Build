<?php
session_start();

include "simple_functions.php";
$link = connect_to_database();

$round_id = $_SESSION['round_id'];
$hole_id = $_POST['hole'];


$hole_info_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
					."WHERE hole_id = '$hole_id' "
					."AND round_id = '$round_id'" );
$hole_info = array();


while( $hole_info_row = mysqli_fetch_array( $hole_info_query, MYSQLI_ASSOC ) ){
	
	array_push( $hole_info, $hole_info_row );

}

$scorecard_data = array();

foreach( $hole_info as $info ){

	$hole		= $info['hole_id'];
	$par		= $info['par'];
	$score		= $info['score'];
	$handicap	= $info['hole_handicap'];
	
	array_push( $scorecard_data, array( 
			'hole'		=> $hole, 
			'par'		=> $par,
			'score'		=> $score,
			'handicap'	=> $handicap
		)
	);
	
}


echo json_encode($scorecard_data);					



?>