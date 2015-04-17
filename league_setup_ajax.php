<?php
session_start();

function record($roundParameters, $start_date) {
    	
   	
    	
    	$new_league_id = uniqid();
    	
    	$_SESSION['league_id_admin'] = $new_league_id;
    	$_SESSION['league_name'] = $roundParameters['league_name'];
    	
    	
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
	
	
	$date = new DateTime($start_date);
    	
    	$new_date = date_modify($date, '+'.$roundParameters['season_weeks'].' week');
    	
    	$end_date = date_format($new_date, 'Y-m-d');
	

	
	$league_name		= mysqli_real_escape_string( $link, $roundParameters['league_name'] );
   	$handicap_yes_no		= mysqli_real_escape_string( $link, $roundParameters['handicap_yes_no'] );
    	$season_weeks       	= mysqli_real_escape_string( $link, $roundParameters['season_weeks'] );
   	$league_frequency    	= mysqli_real_escape_string( $link, $roundParameters['league_frequency'] );
   	$holes			= mysqli_real_escape_string( $link, $roundParameters['holes'] );
	$league_admin_id		= mysqli_real_escape_string( $link, $_SESSION['id'] );
	$query_start_date	= mysqli_real_escape_string( $link, $start_date );
	$query_end_date		= mysqli_real_escape_string( $link, $end_date );
	$league_type		= mysqli_real_escape_string( $link, "Fed Ex Cup");
	$is_complete		= mysqli_real_escape_string( $link, "0");
	
	
	
    				
    	
    	
    	
    	
    	
    	$league_setup_query = "INSERT INTO `bg_app_league`"
    				."(`league_name`, `league_id`, `league_admin_id`, `league_type`, `use_handicaps`, "
    				."`weeks`, `frequency_by_weeks`, `holes`, `start_date`, `end_date`, `is_complete`) "
		  		."VALUES('$league_name', '$new_league_id', '$league_admin_id', '$league_type', '$handicap_yes_no', "
		  		."'$season_weeks', '$league_frequency', '$holes', '$query_start_date', '$query_end_date', '$is_complete')";
    				
    	$run_league_setup_query = mysqli_query($link, $league_setup_query);
 	


}


function create_date($roundParameters) {
   
   	$year  = $roundParameters['date_year'];
        $month = $roundParameters['date_month'];
        $day   = $roundParameters['date_day'];
        
        $parsed_date = new DateTime( "$year-$month-$day" );
        
        $start_date = date_format($parsed_date, 'Y-m-d');
        
        return $start_date;
   
}	








            
	        
$roundParameters = array(
    "league_name"	=> $_POST['league_name'],
    "handicap_yes_no"	=> $_POST['handicap_yes_no'],
    "season_weeks"      => $_POST['season_weeks'],
    "league_frequency"  => $_POST['league_frequency'],
    "holes"		=> $_POST['holes'],
    "date_year"		=> $_POST['date_year'],
    "date_month"	=> $_POST['date_month'],
    "date_day"		=> $_POST['date_day']
);


$start_date = create_date($roundParameters);

record( $roundParameters, $start_date );
		


?>