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







function calculate_weekly_points($link) {


	$new_today = new DateTime('now');
	
	$today = date_format($new_today, 'Y-m-d');

	$leagues_to_potentially_calculate_points_query = mysqli_query( $link, "SELECT * FROM bg_app_league "
			 								     ."WHERE start_date < '$today' "
			 								     ."AND is_complete = '0'" );
			
	
	$leagues_to_potentially_calculate_points = array();
	
	while( $leagues_to_potentially_calculate_points_row = mysqli_fetch_array( $leagues_to_potentially_calculate_points_query, MYSQLI_ASSOC ) ){
		array_push($leagues_to_potentially_calculate_points, $leagues_to_potentially_calculate_points_row );
	}
	
	
	
	$leagues_to_calculate_points = array();
	
	foreach( $leagues_to_potentially_calculate_points as $league_to_potentially_calculate_points ) {
	
		$start_date = new DateTime( $league_to_potentially_calculate_points['start_date'] );
		
		$date_difference = date_diff( $new_today, $start_date );
		
		$difference_in_days = $date_difference->days;
		
		$frequency_in_days = $league_to_potentially_calculate_points['frequency_by_weeks'] * 7;
		
		$number = $difference_in_days / $frequency_in_days;
		
		if(floor($number) == $number && $number != 0){
			
			$league_round_number = $number;
			
			array_push( $leagues_to_calculate_points, 
				array( $league_to_potentially_calculate_points['league_id'], $league_round_number)
			);
		
		}else{
		
			$league_round_number = floor($number);
			
			$verify_update_league_id = $league_to_potentially_calculate_points['league_id'];
			
			
			if( $league_round_number != 0 ){
			
				$was_league_updated_query = mysqli_query( $link, "SELECT * "
									     ."FROM bg_app_league_points "
									     ."WHERE round_number = '$league_round_number' "
					 				     ."AND league_id = '$verify_update_league_id'" );
					 				     
				$result_rows = mysqli_num_rows($was_league_updated_query);
				
				
		
				if( $result_rows == 0 ){
					
					array_push( $leagues_to_calculate_points, 
						array( $verify_update_league_id, $league_round_number)
					);
				
				}
				
			}
			
		}
	
	}
	
		
	if($leagues_to_calculate_points) {
	
		foreach($leagues_to_calculate_points as $league_to_calculate_points) {
		
			$league_id = $league_to_calculate_points[0];
			$round_number = $league_to_calculate_points[1];
			
			$submitted_rounds_query = mysqli_query( $link, "SELECT * FROM bg_app_match "
								      ."WHERE league_id = '$league_id' "
								      ."AND league_round_used = '$round_number' "
								      ."ORDER BY score_from_par DESC" );
			
			$submitted_rounds = array();
	
			while( $submitted_rounds_row = mysqli_fetch_array( $submitted_rounds_query, MYSQLI_ASSOC ) ){
				array_push($submitted_rounds, $submitted_rounds_row );
			}
			
			
			$participants_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants "
								  ."WHERE league_id = '$league_id' "
								  ."AND is_confirmed = '1'" );
								  
			$participants = mysqli_num_rows( $participants_query );
			
			$position = $participants;
			
			$points_awarded = 0;
			
			$particpants_without_a_round_query = mysqli_query( $link, 
			  "SELECT bg_app_league_participants.* "
		         ."FROM bg_app_league_participants "
		         ."WHERE bg_app_league_participants.league_id = '$league_id' "
		         ."AND bg_app_league_participants.is_confirmed = '1' "
		         ."AND NOT EXISTS "
		         ."(SELECT bg_app_match.* FROM bg_app_match "
		         ."WHERE bg_app_league_participants.user_id = bg_app_match.user_id "
		         ."AND bg_app_match.league_id = '$league_id' "
	         	 ."AND bg_app_match.league_round_used = '$round_number')" );

			$particpants_without_a_round_count = mysqli_num_rows( $particpants_without_a_round_query );
			
			if( !$particpants_without_a_round_count == 0 ) {	
			
				$particpants_without_a_round = array();
	
				while( $particpants_without_a_round_row = mysqli_fetch_array( $particpants_without_a_round_query, MYSQLI_ASSOC ) ){
					array_push($particpants_without_a_round, $particpants_without_a_round_row );
				}
				
				foreach( $particpants_without_a_round as $particpant_without_a_round ) {
				
					$no_round_user_id = $particpant_without_a_round['user_id'];
					
					$display_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users "
										          ."WHERE user_id = '$no_round_user_id'" );
					$display_name_row = mysqli_fetch_row( $display_name_query );
					$display_name = $display_name_row[0];					       
					
					$previous_round_number = $round_number - 1;
					
					if( $previous_round_number == 0 ){
						
						$previous_total_points = 0;
					
					}else{
					
						$previous_total_points_query = mysqli_query( $link, "SELECT total_points FROM bg_app_league_points "
											               ."WHERE league_id = '$league_id' "
											               ."AND user_id = '$no_round_user_id' "
											               ."AND round_number = '$previous_round_number'" );
						$previous_total_points_row = mysqli_fetch_row( $previous_total_points_query );
						$previous_total_points = $previous_total_points_row[0];  
					
					}
					
					
					$round_matchup_query = mysqli_query( $link, "SELECT * FROM bg_app_match_schedule "
										      ."WHERE league_id = '$league_id' "
										      ."AND round_number = '$round_number' "
										      ."AND (home_id = '$no_round_user_id' OR away_id = '$no_round_user_id')" );
					
					$round_matchup = array();
	
					while( $round_matchup_row = mysqli_fetch_array( $round_matchup_query, MYSQLI_ASSOC ) ){
						array_push($round_matchup, $round_matchup_row );
					}
					
					foreach( $round_matchup as $round_match ){
					
						if( $round_match['home_id'] == $no_round_user_id ){
						
							$opponent = $round_match['away_id'];
						
						}else{
						
							$opponent = $round_match['home_id'];
						
						}
					
					
					
					}
					
					$opponent_point_query = mysqli_query( $link, "SELECT train_game_points FROM bg_app_match "
										      ."WHERE league_id = '$league_id' "
										      ."AND league_round_used = '$round_number' "
										      ."AND user_id = '$opponent'" );
					$opponent_point_row = mysqli_fetch_row( $opponent_point_query );
					$opponent_point = $opponent_point_row[0];
					
			
					if( !$opponent_point ){
						$opponent_point = 0;
					}
					
					
					$update_score_for_users_without_round = mysqli_query( $link, 
					     "INSERT INTO bg_app_league_points "
		                            ."(league_id, "
		                            ."round_number, "
		                            ."user_id, "
		                            ."position, "
		                            ."points_from_leaderboard, "
		                            ."display_name, "
		                            ."match_win, "
		                            ."points_from_match, "
		                            ."total_points, "
		                            ."opponent, "
		                            ."opponents_match_points) "
		                            ."VALUES('$league_id', "
		                            ."'$round_number', "
		                            ."'$no_round_user_id', "
		                            ."'$position', "
		                            ."'0', "
		                            ."'$display_name', "
		                            ."'3', "
		                            ."'0', "
		                            ."'$previous_total_points', "
		                            ."'$opponent', "
		                            ."'$opponent_point')" );
					
					
					$position = $position - 1;
				
				}
			
			}
			
			foreach( $submitted_rounds as $submitted_round ) {
			
				if($position == 1) {
				
					if($points_awarded == 0){
					
						$points_awarded = 50;
					
					}
					
					$points_awarded = $points_awarded * 2;
					
				} elseif($position == 2) {
					
					$points_awarded = $points_awarded + 30;
					
				} elseif($position < $participants/2) {
					
					$points_awarded = $points_awarded + 10;
					
				} else {
				
					$points_awarded = $points_awarded + 5;
					
				}	
				
				
				
				
				$points_league_id	= $submitted_round['league_id'];
				$points_round_number	= $submitted_round['league_round_used'];
				$points_user_id		= $submitted_round['user_id'];
				$points_score_from_par	= $submitted_round['score_from_par'];
				
				$points_display_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users "
										         ."WHERE user_id = '$points_user_id'" );
				$points_display_name_row = mysqli_fetch_row( $points_display_name_query );
				$points_display_name = $points_display_name_row[0];
				
			
				$update_score_for_users = mysqli_query( $link, "INSERT INTO bg_app_league_points "
						                            ."(league_id, "
						                            ."round_number, "
						                            ."user_id, "
						                            ."position, "
						                            ."score_from_par, "
						                            ."match_win, "
						                            ."points_from_match, "
						                            ."points_from_leaderboard, "
						                            ."total_points, "
						                            ."display_name) "
						                            ."VALUES( "
						                            ."'$points_league_id', "
						                            ."'$points_round_number', "
						                            ."'$points_user_id', "
						                            ."'$position', "
						                            ."'$points_score_from_par', "
						                            ."'0', "
						                            ."'0', "
						                            ."'$points_awarded', "
						                            ."'0', "
						                            ."'$points_display_name')" );
				
				$position = $position - 1;	
			
			}			
			
			for($i = -100; $i <= 100; $i += 1) {
			
				$finalize_points_league_id	= $league_id;
				$finalize_points_round_number	= $round_number;
				
			
				$points_to_finalize_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
										        ."WHERE league_id = '$finalize_points_league_id' "
										        ."AND round_number = '$finalize_points_round_number' "
										        ."AND score_from_par = '$i' " );
				
				$finalize_count = mysqli_num_rows( $points_to_finalize_query );
				
					
				if( !$finalize_count == 0 ) {	
				
					$points_to_finalize = array();
	
					while( $points_to_finalize_row = mysqli_fetch_array( $points_to_finalize_query, MYSQLI_ASSOC ) ){
						array_push($points_to_finalize, $points_to_finalize_row );
					}	
					
					foreach($points_to_finalize as $current_points) {
					
						$point_pot = $point_pot + $current_points['points_from_leaderboard'];
					
					}
					
					$final_points = round($point_pot / count($points_to_finalize));
					
					
					
					$update_final_points = mysqli_query( $link, "UPDATE bg_app_league_points "
									."SET points_from_leaderboard = '$final_points' "
									."WHERE league_id = '$finalize_points_league_id' "
									."AND round_number = '$finalize_points_round_number' "
									."AND score_from_par = '$i' "
									."AND match_win != '3'" );
				
					$point_pot = 0;
					$final_points = 0;
					
				}
			
			
			
			
			
			}
			
			

			$match_play_info_query = mysqli_query( $link, "SELECT * FROM bg_app_match "
							 ."WHERE league_id = '$league_id' "
							 ."AND league_round_used = '$round_number'" );
							 
	
			$match_play_info = array();
	
			while( $match_play_info_row = mysqli_fetch_array( $match_play_info_query, MYSQLI_ASSOC ) ){
				array_push($match_play_info, $match_play_info_row );
			}	
			
		
			foreach($match_play_info as $matchup) {
			
				$opponent_id = $matchup['opponent'];
				$user_id = $matchup['user_id'];
				
				$opponents_points_query = mysqli_query( $link, "SELECT train_game_points FROM bg_app_match "
										      ."WHERE league_id = '$league_id' "
										      ."AND league_round_used = '$round_number' "
										      ."AND user_id = '$opponent_id'" );
				$opponents_points_row = mysqli_fetch_row( $opponents_points_query );
				$opponents_points = $opponents_points_row[0];
				
				
				$match_points = $matchup['train_game_points'];
				
				if( !$opponents_points ){
					$opponents_points = 0;
				}
				
				$previous_round_number = $round_number - 1;
					
				if( $previous_round_number == 0 ){
					
					$previous_total_points = 0;
				
				}else{
				
					$previous_total_points_query = mysqli_query( $link, "SELECT total_points FROM bg_app_league_points "
										               ."WHERE league_id = '$league_id' "
										               ."AND user_id = '$user_id' "
										               ."AND round_number = '$previous_round_number'" );
					$previous_total_points_row = mysqli_fetch_row( $previous_total_points_query );
					$previous_total_points = $previous_total_points_row[0];  
				
				}
				
				$points_from_overall_leaderboard_query = mysqli_query( $link, "SELECT points_from_leaderboard "
											                     ."FROM bg_app_league_points "
											                     ."WHERE league_id = '$league_id' "
											                     ."AND user_id = '$user_id' "
										                     ."AND round_number = '$round_number'" );
				$points_from_overall_leaderboard_row = mysqli_fetch_row( $points_from_overall_leaderboard_query );
				$points_from_overall_leaderboard = $points_from_overall_leaderboard_row[0];
			
				if($match_points > $opponents_points) {
					
					$new_total_points = $previous_total_points + $points_from_overall_leaderboard + 50; 
					
					$update_matchup_result = mysqli_query( $link, "UPDATE bg_app_league_points "
									 ."SET match_win = '1', "
									 ."points_from_match = '50', "
									 ."total_points = '$new_total_points', "
									 ."opponent = '$opponent_id', "
									 ."opponents_match_points = '$opponents_points', "
									 ."match_points = '$match_points' "
									 ."WHERE league_id = '$league_id' "
									 ."AND round_number = '$round_number' "
									 ."AND user_id = '$user_id'" );
					
					
				} 
				
				if($match_points == $opponents_points) {
					
					$new_total_points = $previous_total_points + $points_from_overall_leaderboard + 25; 
					
					$update_matchup_result = mysqli_query( $link, "UPDATE bg_app_league_points "
									 ."SET match_win = '2', "
									 ."points_from_match = '25', "
									 ."total_points = '$new_total_points', "
									 ."opponent = '$opponent_id', "
									 ."opponents_match_points = '$opponents_points', "
									 ."match_points = '$match_points' "
									 ."WHERE league_id = '$league_id' "
									 ."AND round_number = '$round_number' "
									 ."AND user_id = '$user_id'" );
	
				}
				
				if($match_points < $opponents_points) {
				
					$new_total_points = $previous_total_points + $points_from_overall_leaderboard; 
					
					$update_matchup_result = mysqli_query( $link, "UPDATE bg_app_league_points "
									 ."SET match_win = '0', "
									 ."points_from_match = '0', "
									 ."total_points = '$new_total_points', "
									 ."opponent = '$opponent_id', "
									 ."opponents_match_points = '$opponents_points', "
									 ."match_points = '$match_points' "
									 ."WHERE league_id = '$league_id' "
									 ."AND round_number = '$round_number' "
									 ."AND user_id = '$user_id'" );
					
				}
			
		
			}
			
	
		}
	
	


	}





}










?>