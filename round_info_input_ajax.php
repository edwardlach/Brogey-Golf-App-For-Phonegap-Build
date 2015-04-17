<?php 
session_start();
 

function record($roundParameters, $start_date) {
    	
   	
    	
    	$new_round_id = uniqid();
    	
    	$_SESSION['round_id'] = $new_round_id;
    	$_SESSION['course_name'] = $roundParameters['course_name'];
    	
    	
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
	
	$course_name		= mysqli_real_escape_string( $link, $roundParameters['course_name'] );
   	$front_back_both		= mysqli_real_escape_string( $link, $roundParameters['front_back_both'] );
    	$course_par       	= mysqli_real_escape_string( $link, $roundParameters['course_par'] );
   	$course_rating    	= mysqli_real_escape_string( $link, $roundParameters['course_rating'] );
   	$slope_rating		= mysqli_real_escape_string( $link, $roundParameters['slope_rating'] );
	$query_id		= mysqli_real_escape_string( $link, $_SESSION['id'] );
	$query_start_date	= mysqli_real_escape_string( $link, $start_date );
	$query_round_id		= mysqli_real_escape_string( $link, $new_round_id );
	
	
    	$round_id_query = "SELECT `round_id` FROM `bg_app_rounds` WHERE `course_name` = '$course_name' "
    				."AND `front_back_both` = '$front_back_both' "
    				."AND `course_par` = '$course_par' "
    				."AND `course_rating` = '$course_rating' "
    				."AND `slope_rating` = '$slope_rating' "
    				."AND `user_id` = '$query_id' "
    				."AND `start_date` = '$query_start_date' "
    				."AND `is_complete` = '0'";
    				
    	$round_input_query = "INSERT INTO `bg_app_rounds`"
    				."(`round_id`, `user_id`, `course_name`, `slope_rating`, `course_rating`, "
    				."`front_back_both`, `start_date`) "
		  		."VALUES('$query_round_id', '$query_id', '$course_name', '$slope_rating', '$course_rating', "
		  		."'$front_back_both', '$query_start_date')";
    				
    	$round_id_result = mysqli_query($link, $round_id_query);
 	
    	
    	
    	if (!$round_id_result) {
	    
		$round_input = mysqli_query($link, $round_input_query);  
	
	}
	
	
}


function create_date($roundParameters) {
   
   	$year  = $roundParameters['date_year'];
        $month = $roundParameters['date_month'];
        $day   = $roundParameters['date_day'];
        
        $parsed_date = new DateTime( "$year-$month-$day" );
        
        $start_date = date_format($parsed_date, 'Y-m-d');
        
        return $start_date;
   
}	







       
	if( $_POST['date_year'] && $_POST['date_month'] && $_POST['date_day'] ) {       
	        
	        $roundParameters = array(
	            "course_name"	=> $_POST['course_name'],
	            "front_back_both"	=> $_POST['front_back'],
	            "course_rating"     => $_POST['course_rating'],
	            "slope_rating"	=> $_POST['slope_rating'],
	            "date_year"		=> $_POST['date_year'],
	            "date_month"		=> $_POST['date_month'],
	            "date_day"		=> $_POST['date_day']
	        );
        
        
	        $start_date = create_date($roundParameters);
	        
		record( $roundParameters, $start_date );
		
		
		
	} else {
		
		$roundParameters = array(
	            "course_name"	=> $_POST['course_name'],
	            "front_back_both"	=> $_POST['front_back'],
	            "course_rating"     => $_POST['course_rating'],
	            "slope_rating"	=> $_POST['slope_rating']
	        );  
	         
	        $start_date = date('Y-m-d'); 
	                 
      		record( $roundParameters, $start_date );
      		
      		
      	}
    	










?>