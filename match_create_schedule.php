<?php
date_default_timezone_set('America/New_York');


function create_matches(){

	
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
	
	
	
		
	
	$new_today = new DateTime('now');
	
	$start_day = date_format($new_today, 'Y-m-d');
	
	$leagues_that_start_today_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE start_date = '$start_day'" );
	
	$leagues_that_start_today = array();
	
	while( $leagues_that_start_today_row = mysqli_fetch_array( $leagues_that_start_today_query, MYSQLI_ASSOC ) ){
	
		array_push( $leagues_that_start_today, $leagues_that_start_today_row );
		
	}
		
	
	if($leagues_that_start_today) {
	
		foreach($leagues_that_start_today as $league_that_starts_today) {
			
			$league_id = $league_that_starts_today['league_id'];
	
			$league_participants_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants WHERE league_id = '$league_id' AND is_confirmed = '1'");
			
			$league_participants = array();
			
			while( $league_participants_row = mysqli_fetch_array( $league_participants_query, MYSQLI_ASSOC ) ){
	
				array_push( $league_participants, $league_participants_row );
				
			}
			
					 
			$number_of_participants = mysqli_num_rows($league_participants_query);
			
			$league_rounds = $league_that_starts_today['weeks'] / $league_that_starts_today['frequency_by_weeks'];
			
			$league_round = 1;
			
			$opponents = array();
					
			foreach($league_participants as $league_participant) {
				
				array_push( $opponents, $league_participant['user_id'] );
			
			}
	
	
			while($league_round <= $league_rounds) {
			
			
				shuffle($opponents);
				
				shuffle($opponents);
				
				for($i = 0, $j = 1; $j < $number_of_participants; $i = $i + 2, $j = $j + 2) {
				
					$home_opponent = $opponents[$i];
					
					$away_opponent = $opponents[$j];
					
					$match_create_query = mysqli_query( $link, "INSERT INTO bg_app_match_schedule "
													  ."(league_id, round_number, home_id, away_id) "
													  ."VALUES('$league_id', '$league_round', '$home_opponent', '$away_opponent')" );
					
				
				}
								
				$league_round += 1;
			
			}
			
		
		}
	
		
			
	}
	

}











?>