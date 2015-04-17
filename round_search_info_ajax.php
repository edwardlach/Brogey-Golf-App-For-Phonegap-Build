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



function course_search($link) {

	$search = $_POST['search'];
	$query_search = "%".$search."%";
	
	
	$user_id = $_SESSION['id'];
	
	
	$search_for_courses = mysqli_query( $link, "SELECT * FROM bg_app_rounds "
						  ."WHERE course_name LIKE '$query_search' "
						  ."AND user_id = '$user_id' "
						  ."LIMIT 0, 3" );
						  
	$course_results = array();
	
	if( $search_for_courses ){
	
		while( $course_results_row = mysqli_fetch_array( $search_for_courses, MYSQLI_ASSOC ) ){
		
			array_push( $course_results, $course_results_row );
			
		}
	
	}
	
		
	
	foreach( $course_results as $result ){
	
		$course_name = $result['course_name'];
		$round_id = $result['round_id'];
		$front_back_both = $result['front_back_both'];
		$slope_rating = $result['slope_rating'];
		$course_rating = $result['course_rating'];
		
		if( $front_back_both == "front" ){
		
			$holes = "Front 9";
		
		}elseif( $front_back_both == "back" ){
		
			$holes = "Back 9";
			
		}elseif( $front_back_both == "both" ){
		
			$holes = "All 18";
		
		}
		
		
	
		$course_selection_html .= <<<EOHTML
			<div class="course_result_table_wrap">
			<table  class="course_result_list">
				<tbody>
					<tr id="$round_id" class="course_result">
						<td class="course_result_name">$course_name</br>$holes</td>
						<td class="course_result_numbers">Slope: $slope_rating</br>Course: $course_rating</td>
					</tr>
				</tbody>
			</table>
			</div>
		
EOHTML;
	
	
	}

	echo $course_selection_html;

}



function populate_round_info($link) {


	$round_id = $_POST['round_id'];

	
	$search_for_round = mysqli_query( $link, "SELECT * FROM bg_app_rounds "
						."WHERE round_id = '$round_id'" );
						  
	$round_info = array();

	while( $round_info_row = mysqli_fetch_array( $search_for_round, MYSQLI_ASSOC ) ){
	
		array_push( $round_info, $round_info_row );
		
	}
	
	
	$round_data = array();
	
	foreach( $round_info as $round ){
	
		array_push( $round_data, array( 
						'course_name'		=> $round['course_name'], 
						'slope_rating'		=> $round['slope_rating'],
						'course_rating'		=> $round['course_rating'],
						'front_back_both'	=> $round['front_back_both']
					)
		);
	
	}
	
	




	echo json_encode($round_data);

}





$search_populate = $_POST['search_populate'];


if( $search_populate == "search" ){

	course_search($link);

}elseif( $search_populate == "populate" ){

	populate_round_info($link);

}





?>