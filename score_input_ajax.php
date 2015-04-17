<?php
session_start();


$query_round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );
echo $query_round_id;

/*
function record($holeParameters, $query_round_id) {


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
	
	
    	
   	$hole		= mysqli_real_escape_string( $link, $holeParameters['hole_id'] );
    	$par		= mysqli_real_escape_string( $link, $holeParameters['par'] );
    	$score		= mysqli_real_escape_string( $link, $holeParameters['score'] );
    	$hole_handicap	= mysqli_real_escape_string( $link, $holeParameters['hole_handicap'] );
    	
  		  	
  
	$score_input_query = "INSERT INTO `bg_app_holes`"
    				."(`hole_id`, `round_id`, `par`, `score`, `hole_handicap`) "
		  		."VALUES('$hole', '$query_round_id', '$par', '$score', '$hole_handicap')";  
  
  
  	//$run_score_input_query = mysqli_query( $link, $score_input_query );
  	
  	$number_of_holes_played_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
							    ."WHERE round_id = '$query_round_id' " );
	$number_of_holes_played = mysqli_num_rows($number_of_holes_played_query);						    
	
	$holes_to_play = find_holes_to_be_played($query_round_id);
	
	if( $number_of_holes_played >= $holes_to_play ){
		
		$continue = "finished";
		
	}else{
		
		$continue = "continue";
		
	}
	
	
	return $number_of_holes_played;
  	
    		
}



function find_current_hole_number($query_round_id) {

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
	
    	$holes_played_query = "SELECT * FROM `bg_app_holes` WHERE `round_id` = '$query_round_id'";
    	
    	$holes_played_result = mysqli_query($link, $holes_played_query);	
    	
    	$holes_played = mysqli_num_rows($holes_played_result);
    	
    	$current_hole = $holes_played + 1;
    	
    	$front_back_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$query_round_id'"; 
    	
    	
    	$run_front_back_query = mysqli_query($link, $front_back_query);
    	
    	$front_back_row = mysqli_fetch_row($run_front_back_query);
    	
    	$front_back = $front_back_row[0];
    	
    	
	if ($front_back == "back") {
		
		$current_hole = $current_hole + 9;
	}
	
	
	return $current_hole;
	 
}

$current_hole = find_current_hole_number($query_round_id);

function find_holes_to_be_played($query_round_id) {

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
	
	$front_back_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$query_round_id'"; 
    	
    	$run_front_back_query = mysqli_query($link, $front_back_query);
    	
    	$front_back_row = mysqli_fetch_row($run_front_back_query);
    	
    	$front_back = $front_back_row[0];

	if($front_back == "both"){
		
		$holes_to_be_played = 18;
	
	}else{
	
		$holes_to_be_played = 9;
		
	}

	return $holes_to_be_played;

}

$holes_to_be_played = find_holes_to_be_played($query_round_id);


function score_from_par($query_round_id){

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

	
	/* query to pull all of the hole info already submitted for the current round */
	
	$scorecard_query = "SELECT * FROM `bg_app_holes` "
			  ."WHERE `round_id` = '$query_round_id' "
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
	
	return $score_from_par;
	
	

}


$score_from_par = score_from_par($query_round_id);


function continue_input($query_round_id){

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
	
	
	return $continue;

}


$holes_recorded_query = mysqli_query( $link,
	"SELECT * FROM bg_app_holes "
	."WHERE round_id = '$query_round_id'" 
);

$holes_recorded = mysqli_num_rows($holes_recorded_query);	


if( $holes_recorded <= $holes_to_be_played ) {
	if($previous_course_info) {
		$holeParameters = array(
		    "par"       		=> $_POST['par'],
	            "score"    	 	=> $_POST['score'],
	            "hole_id"		=> $current_hole,
	            "hole_handicap"	=> $_POST['handicap']
		);
	} else {	
        
	        $holeParameters = array(
	            "par"       		=> $_POST['par'],
	            "score"    	 	=> $_POST['score'],
	            "hole_id"		=> $current_hole,
	            "hole_handicap"	=> $_POST['handicap']
	        );
	        
	}
        
        $continue = record( $holeParameters, $query_round_id );
       
       	
       	 
} 

/*

$data = array(
		"continue"	=> $continue,
		"current_hole"	=> $current_hole,
		"score_from_par"	=> $score_from_par
	);
	
echo json_encode($data);
*/

*/
?>