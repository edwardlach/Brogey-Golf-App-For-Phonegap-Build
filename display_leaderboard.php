<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}



function create_leaderboard($league_id, $user_id, $historical) {


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
	
	
	if( $historical == 1 ){
	
		$league_weeks_for_leaderboard_query = mysqli_query( $link, "SELECT weeks FROM bg_app_league WHERE league_id = '$league_id'" );
		$league_weeks_for_leaderboard_row = mysqli_fetch_row( $league_weeks_for_leaderboard_query );
		$league_weeks_for_leaderboard = $league_weeks_for_leaderboard_row[0];
		
		$league_frequency_for_leaderboard_query = mysqli_query( $link, "SELECT frequency_by_weeks FROM bg_app_league WHERE league_id = '$league_id'" );
		$league_frequency_for_leaderboard_row = mysqli_fetch_row( $league_frequency_for_leaderboard_query );
		$league_frequency_for_leaderboard = $league_frequency_for_leaderboard_row[0];
	
		$leaderboard_round_number = $league_weeks_for_leaderboard / $league_frequency_for_leaderboard;
		
	
	}else{
	
	
		$league_start_date_for_leaderboard_query = mysqli_query( $link, "SELECT start_date FROM bg_app_league WHERE league_id = '$league_id'" );
		$league_start_date_for_leaderboard_row = mysqli_fetch_row( $league_start_date_for_leaderboard_query );
		$league_start_date_for_leaderboard = $league_start_date_for_leaderboard_row[0];
		
		$league_start_date_for_leaderboard_raw = new DateTime($league_start_date_for_leaderboard);
	
		$date_for_leaderboard_round_raw = new DateTime('now');
		
		$date_difference = date_diff( $date_for_leaderboard_round_raw, $league_start_date_for_leaderboard_raw );
	
		$difference_in_days = $date_difference->days;
		
		$leaderboard_round_number = floor( $difference_in_days / 7 );	

	}
	
	$leaderboard_rounds_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
							."WHERE league_id = '$league_id' "
							."AND round_number = '$leaderboard_round_number'"
							."ORDER BY total_points DESC" );
							
	$leaderboard_rounds = array();
	
	while( $leaderboard_rounds_row = mysqli_fetch_array( $leaderboard_rounds_query, MYSQLI_ASSOC ) ){
		array_push($leaderboard_rounds, $leaderboard_rounds_row );
	}		


	
	$leaderboard_html = <<<EOHTML
		<div id="leaderboard_title">Leaderboard</div>
		<table id="leaderboard_table">
			
EOHTML;
	
	$position = 1;
	
	foreach( $leaderboard_rounds as $leaderboard_round) {
	
		$brogey = $leaderboard_round['display_name'];
		$points = $leaderboard_round['total_points'];
		$round_user_id = $leaderboard_round['user_id'];
		
		
		if( $round_user_id == $user_id ){
			
			$leaderboard_html .= <<<EOHTML
		
				<tr class="leaderboard_position_row">
					<td class="leaderboard_position_card leaderboard_current_user">$position</td>
					<td class="leaderboard_name_card leaderboard_current_user">$brogey</td>
					<td class="leaderboard_points_card leaderboard_current_user">$points</td>
				</tr>
EOHTML;
		
		}else{
	
			$leaderboard_html .= <<<EOHTML
		
				<tr class="leaderboard_position_row">
					<td class="leaderboard_position_card">$position</td>
					<td class="leaderboard_name_card">$brogey</td>
					<td class="leaderboard_points_card">$points</td>
				</tr>
EOHTML;
	
		}
			
		$position += 1;
	
	}
	
	
	$leaderboard_html .= <<<EOHTML
	
		</table>
		
EOHTML;

	
	return $leaderboard_html;
	




}


?>