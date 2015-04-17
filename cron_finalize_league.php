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

$current_day = new DateTime("now");

$current_day_for_query = date_format($current_day, 'Y-m-d');

$active_leagues_query = mysqli_query( $link, "SELECT * "
			 		    ."FROM bg_app_league "
		 		    	    ."WHERE is_complete = '0'");
	
$active_leagues = array();

while( $active_leagues_row = mysqli_fetch_array( $active_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $active_leagues, $active_leagues_row );
	
}

foreach( $active_leagues as $league ){


	$end_date = $league['end_date'];
	$end_DateTime = new DateTime($end_date);
	$finalize_DateTime = date_modify($end_DateTime, '+5 day');
	$league_id = $league['league_id'];
	
	
	if( $finalize_DateTime <= $current_day ){
		
		$run_finalize_league_query = mysqli_query( $link, "UPDATE bg_app_league "
								 ."SET is_complete = '1' "
								 ."WHERE league_id = '$league_id'");							
		
	}

}










?>