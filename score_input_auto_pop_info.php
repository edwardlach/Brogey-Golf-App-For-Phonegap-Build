<?php
session_start();
include "simple_functions.php";
$link = connect_to_database();

$round_id = $_SESSION['round_id'];

$user_id = $_SESSION['id'];
	
$current_round_info_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds "
						."WHERE round_id = '$round_id'" );
						
$current_round_info = array();
	

while( $current_round_info_row = mysqli_fetch_array( $current_round_info_query, MYSQLI_ASSOC ) ){
	
	array_push( $current_round_info, $current_round_info_row );

}

foreach( $current_round_info as $info ){

	$course_name = $info['course_name'];
	$slope_rating = $info['slope_rating'];
	$hole = $current_hole;
	
	if( $hole <= 9 ){
		
		$front_back = "front";
	
	}else{
		
		$front_back = "back";
		
	}
		
	
	
	$auto_pop_round_query = mysqli_query( $link, "SELECT * FROM  bg_app_rounds "
						   ."WHERE course_name = '$course_name' "
						   ."AND user_id = '$user_id' "
						   ."AND round_id != '$round_id' "
						   ."AND (front_back_both = '$front_back' "
						   ."OR front_back_both = 'both') " 
						   ."LIMIT 0, 1" );
	$auto_pop_round = array();
	

	while( $auto_pop_round_row = mysqli_fetch_array( $auto_pop_round_query, MYSQLI_ASSOC ) ){
		
		array_push( $auto_pop_round, $auto_pop_round_row );
	
	}					   
						   
	foreach( $auto_pop_round as $auto ){
		
		$auto_round = $auto['round_id'];
		
		$auto_pop_info_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
							   ."WHERE round_id = '$auto_round' "
							   ."AND hole_id = '$hole'" );
							   
		$auto_pop_info = array();
	

		while( $auto_pop_info_row = mysqli_fetch_array( $auto_pop_info_query, MYSQLI_ASSOC ) ){
			
			array_push( $auto_pop_info, $auto_pop_info_row );
		
		}	
								 
		foreach( $auto_pop_info as $pop_info ){
			
			$handicap = $pop_info['hole_handicap'];
			$par = $pop_info['par'];
			$pop_info_array = [$handicap, $par];	
	
		}
	
	}
	



}




if( !$pop_info_array ){

	$pop_info_array = [1, 4];

}


echo json_encode($pop_info_array);


?>