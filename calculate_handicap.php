<?php
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







function _calculate_differential($score) {

	$player_score = $score['front_score'] + $score['back_score'];

	if( $score['front_back_both'] == 'both' ){
		
		$holes = 18;
		
	}else{
	
		$holes = 9;
		
	}



        if( $holes == 9 && $score['course_rating'] > 50 ) {
        	return (
        	    2 * ($player_score - ( $score['course_rating'] / 2) ) * 113 / $score['slope_rating']
        	);
        } else if( $holes == 9 && $score['course_rating'] < 50 ) {
        	return (
        	    2 * ($player_score - $score['course_rating']) * 113 / $score['slope_rating']
        	);
        } else {
	        return (
	            ($player_score - $score['course_rating']) * 113 / $score['slope_rating']
	        );
	}
}
   

    
function _calculate_handicap( $differentials ) {
	return (
   		array_sum( $differentials ) / count( $differentials ) * .96
	);
}










function  main_handicap_calculation($link, $user_id_to_calculate_handicap){


if( $user_id_to_calculate_handicap ){
	
	$user_id = $user_id_to_calculate_handicap;
	
}elseif( $_POST['id'] ){

	$user_id = $_POST['id'];
	
}elseif( $_SESSION['id'] ){

	$user_id = $_SESSION['id'];
	
}


$scores_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds "
	             		    ."WHERE user_id = '$user_id' AND is_complete = '1' "
	            		    ."ORDER BY start_date DESC" );
	       
$scores = array();


while( $scores_row = mysqli_fetch_array( $scores_query, MYSQLI_ASSOC ) ){

	array_push( $scores, $scores_row );
	
}



$total_rounds = mysqli_num_rows( $scores_query );
 
if ( $total_rounds > 20 ) {
	$scores_used = array_slice( $scores, 0, 20 );
} elseif ( $total_rounds < 5 ) {
	$round_handicap = 0;
	return $round_handicap;
	exit;
} else {
	$scores_used = $scores;
}

	
    if( $total_rounds ) {
    
        $diff_map = array(
            1  => 1,
            2  => 1,
            3  => 1,
            4  => 1,
            5  => 1,
            6  => 1,
            7  => 2,
            8  => 2,
            9  => 3,
            10 => 3,
            11 => 4,
            12 => 4,
            13 => 5,
            14 => 5,
            15 => 6,
            16 => 6,
            17 => 7,
            18 => 8,
            19 => 9,
            20 => 10
        );



        $total_differentials = $diff_map[$total_rounds];
        
        
        if ( !$total_differentials ) { $total_differentials = 10; }

        /* TODO
         * Ed says use all rounds, best 10
         * http://www.usga.org/rule-books/handicap-system-manual/handicap-manual/
         *   "no more than 20" "ideally the best 10 of the last 20 rounds"
         */

        foreach ( $scores_used as &$score ) {
            $diff = _calculate_differential( $score );
            $score['differential'] = $diff;
        }
	
	unset( $score );
	

        $sorted_scores = $scores_used;
        
        usort( $sorted_scores, function($b, $a) {
            return $b['differential'] - $a['differential'];
        } );
	
        $diff_scores   = array_slice( $sorted_scores, 0, $total_differentials );
        
        
        $differentials = array();
        

        foreach ( $diff_scores as $diff_score ) {
            array_push( $differentials, $diff_score['differential'] );
        }

	

        $handicap = _calculate_handicap( $differentials );
        
        
    }

	$round_handicap = round($handicap, 1);
	
	return $round_handicap;

}



?>