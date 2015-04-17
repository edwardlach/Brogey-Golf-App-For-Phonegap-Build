<?php


include "calculate_handicap.php";




  function auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link) {
  
  
  	$league_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
  	$league_info = array();
	while( $league_info_row = mysqli_fetch_array( $league_info_query, MYSQLI_ASSOC ) ){
		array_push($league_info, $league_info_row );
	}		
  			
  			
  	
  	foreach($league_info as $league) {
  	
  		$use_handicaps = $league['use_handicaps'];
  		
  	}
  	
  	
  	if( $league_holes == 9 ) {
  		
  		if( $front_back == "front" ) {
  		
		  	$score_info_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds WHERE round_id = '$round_id'" );
		  	$score_info = array();
			while( $score_info_row = mysqli_fetch_array( $score_info_query, MYSQLI_ASSOC ) ){
				array_push($score_info, $score_info_row );
			}				 
		  	
		  	foreach($score_info as $score) {
		  		
		  		$total	= $score['front_score'];
		  		$par	= $score['front_par'];
		  		
		  	}
		  
		  } else if( $front_back == "back" ) {
		  	
		  	$score_info_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds WHERE round_id = '$round_id'" );
		  	$score_info = array();
			while( $score_info_row = mysqli_fetch_array( $score_info_query, MYSQLI_ASSOC ) ){
				array_push($score_info, $score_info_row );
			}				 
		  	
		  	foreach($score_info as $score) {
		  		
		  		$total	= $score['back_score'];
		  		$par	= $score['back_par'];
		  		
		  	}
		  	
		  	
		  } else {
		  	
		  	echo "Front/Back error Yo!";
		  }
	} else {
	
		$score_info_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds WHERE round_id = '$round_id'" );
	  	$score_info = array();
		while( $score_info_row = mysqli_fetch_array( $score_info_query, MYSQLI_ASSOC ) ){
			array_push($score_info, $score_info_row );
		}				 
	  	
	  	foreach($score_info as $score) {
	  		
	  		$front_score_for_total	= $score['front_score'];
	  		$front_par_for_total	= $score['front_par'];
	  		$back_score_for_total	= $score['back_score'];
	  		$back_par_for_total	= $score['back_par'];
	  		$total			= $front_score_for_total + $back_score_for_total;
	  		$par			= $front_par_for_total + $back_par_for_total;
	  		
	  	}
	
	}
  			
  	
  	if( $use_handicaps == 1) {
  		
  		$score_from_par = $total - $match_handicap - $par;
  	
  		return $score_from_par;
  		
  	} else {
  		
  		$score_from_par = $total - $par;
  		
  		return $score_from_par;
  		
  	}
 
  	
  		
  }
  
  
  
  
  function auto_calculate_match_play($link, $league_id, $brogey_id, $round_number) {
  	
  	$league_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
  	$league_info = array();
	while( $league_info_row = mysqli_fetch_array( $league_info_query, MYSQLI_ASSOC ) ){
		array_push($league_info, $league_info_row );
	}		
  	
	$rounds_chosen = choose_best_round($league_id, $link, $round_number);
	  	
  	foreach($league_info as $league) {
  	
  		$use_handicaps = $league['use_handicaps'];
  		$frequency_by_weeks = $league['frequency_by_weeks'];
  		$start_date = $league['start_date'];
  		$league_holes = $league['holes'];
  		
  	}
  	
  	/* uses league start date and current date to find current league round */
  	/*
  	$match_play_today = new DateTime('now');
  	
  	$match_play_start_date = new DateTime( $start_date );
		
	$match_play_date_difference = date_diff( $match_play_today, $match_play_start_date );
	
	$match_play_difference_in_days = $match_play_date_difference->days;
	
	$match_play_frequency_in_days = $frequency_by_weeks * 7;
	
	$number = $match_play_difference_in_days / $match_play_frequency_in_days;
  	
  	$round = ceil($number);
  	*/
  	$user_vs_opponent_query = mysqli_query( $link, "SELECT * FROM bg_app_match_schedule "
  						      ."WHERE league_id = '$league_id' "
  						      ."AND round_number = '$round_number'" );
  	$user_vs_opponent = array();
  	while( $user_vs_opponent_row = mysqli_fetch_array( $user_vs_opponent_query, MYSQLI_ASSOC ) ){
  		array_push($user_vs_opponent, $user_vs_opponent_row);
  	}
  	
  	/* Finds opponent for user and round in question */
  	foreach( $user_vs_opponent as $vs_info ){
  		
  		$home_id = $vs_info['home_id'];
  		$away_id = $vs_info['away_id'];
  		
  		if( $home_id == $brogey_id ){
  			
  			$opponent_id = $away_id;
  		
  		}elseif( $away_id == $brogey_id ){
  			
  			$opponent_id = $home_id;
  		
  		}
  	
  	}
  	
  	
  	
  	/* Finds best round chosen for user and their opponent */
  	foreach( $rounds_chosen as $round_chosen ){
  	
  		$round_user_id = $round_chosen['user_id'];
  		
  		if( $round_user_id == $brogey_id ){
  			
  			$match_play_round_id	= $round_chosen['round_id'];
  			$match_play_handicap	= $round_chosen['match_handicap'];
  			$handicap_for_record	= $match_play_handicap;
  			$match_play_front_back	= $round_chosen['front_back_both'];
  	
  		}elseif( $round_user_id == $opponent_id ){
  			
  			$opponent_match_play_round_id	= $round_chosen['round_id'];
  			$opponent_match_play_handicap	= $round_chosen['match_handicap'];
  			$op_handicap_for_record		= $opponent_match_play_handicap;
  			$opponent_match_play_front_back	= $round_chosen['front_back_both'];
  			
  		}
  	
  	}
 	
 	
 	/* Find hole info for user */
 	
 	if( $match_play_front_back == "front" ){
 	
 		$user_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$match_play_round_id' "
 							."AND hole_id <= 9 "
 							."ORDER BY hole_handicap ASC" );
 	
 	}elseif( $match_play_front_back == "back" ){
 		
 		$user_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$match_play_round_id' "
 							."AND hole_id >= 10 "
 							."ORDER BY hole_handicap ASC" );
 	
 	}else{
 	
 		$user_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$match_play_round_id' "
 							."ORDER BY hole_handicap ASC" );
 	
 	}
 	
 	if( $user_holes_query ){
 	
	 	$user_holes = array();
	  	while( $user_holes_row = mysqli_fetch_array( $user_holes_query, MYSQLI_ASSOC ) ){
	  		array_push($user_holes, $user_holes_row);
	  	}
 	
 	}
 	
 	/* Find hole info for opponent */
 	
 	if( $opponent_match_play_front_back == "front" ){
 	
 		$opponent_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$opponent_match_play_round_id' "
 							."AND hole_id <= 9 "
 							."ORDER BY hole_handicap ASC" );
 	
 	}elseif( $opponent_match_play_front_back == "back" ){
 		
 		$opponent_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$opponent_match_play_round_id' "
 							."AND hole_id >= 10 "
 							."ORDER BY hole_handicap ASC" );
 	
 	}else{
 	
 		$opponent_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
 							."WHERE round_id = '$opponent_match_play_round_id' "
 							."ORDER BY hole_handicap ASC" );
 	
 	}
 	
 	if( $opponent_holes_query ){
 	
	 	$opponent_holes = array();
	  	while( $opponent_holes_row = mysqli_fetch_array( $opponent_holes_query, MYSQLI_ASSOC ) ){
	  		array_push($opponent_holes, $opponent_holes_row);
	  	}
 	
 	}
 	
 	$match_play_info = array();
 	
	for( $i = 0; $i <= $league_holes - 1; $i++ ){
		
		$holes_left = $league_holes - $i;	
			
		if( $match_play_handicap >= 1 ){
			
			$adjust = ceil( $match_play_handicap/$holes_left );
			$match_play_handicap = $match_play_handicap - $adjust;
		
		}else{
			
			$adjust = 0; 
		
		}
		
		if( $opponent_match_play_handicap >= 1 ){
			
			$opponent_adjust = ceil( $opponent_match_play_handicap/$holes_left );
			$opponent_match_play_handicap = $opponent_match_play_handicap - $opponent_adjust;
		
		}else{
			
			$opponent_adjust = 0;
		
		}
		
		
		$user_course_hole	= $user_holes[$i]['hole_id'];
		$user_score		= $user_holes[$i]['score'] - $adjust;
		
		$opponent_course_hole	= $opponent_holes[$i]['hole_id'];
		$opponent_score		= $opponent_holes[$i]['score'] - $opponent_adjust;
		
		if( !$opponent_score ){
		
			$points_won = 1;
			
		}else{
		
			if( $user_score < $opponent_score ){
				
				$points_won = 1;
			
			}elseif( $user_score == $opponent_score ){
				
				$points_won = .5;
			
			}else{
				
				$points_won = 0;
			
			}
		
		}
		
		
		$match_hole_id = $i + 1;
		
		array_push( $match_play_info, 
			array(
				'match_hole_id'		=> $match_hole_id,
				'league_id'		=> $league_id,
				'user_id'		=> $brogey_id,
				'match_handicap'	=> $handicap_for_record,
				'round_id'		=> $match_play_round_id,
				'front_back_both'	=> $match_play_front_back,
				'course_hole' 		=> $user_course_hole,
				'score'	 		=> $user_score,
				'opponent'		=> $opponent_id,
				'opponent_handicap'	=> $op_handicap_for_record,
				'opponent_round_id'	=> $opponent_match_play_round_id,
				'opponent_front_back'	=> $opponent_match_play_front_back,
				'opponent_course_hole'	=> $opponent_course_hole,
				'opponent_score'	=> $opponent_score,
				'points_won'		=> $points_won
			)
		);	
	
	
	}
	
	return $match_play_info;
  		
  	
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
    
  
  function auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link) {
  	
  	$league_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
  	$league_info = array();
	while( $league_info_row = mysqli_fetch_array( $league_info_query, MYSQLI_ASSOC ) ){
		array_push($league_info, $league_info_row );
	}		
  	

  	
  	foreach($league_info as $league) {
  	
  		$use_handicaps = $league['use_handicaps'];
  		
  	}
  	
  	
  	$slope_rating_query = mysqli_query( $link, "SELECT slope_rating FROM bg_app_rounds WHERE round_id = '$round_id'" );
	$slope_rating_row = mysqli_fetch_row( $slope_rating_query );
	$slope_rating = $slope_rating_row[0];
  	
  	
  	$holes_info_query = mysqli_query( $link, "SELECT * FROM bg_app_holes WHERE round_id = '$round_id' ORDER BY hole_id ASC" );
  	$holes_info = array();
	while( $holes_info_row = mysqli_fetch_array( $holes_info_query, MYSQLI_ASSOC ) ){
		array_push( $holes_info, $holes_info_row );
	}		
  	
	
	$hole_handicap = 1;
	
	$hole_count = count($holes_info);
	
	if( $league_holes == 9 && $front_back == "back" && $hole_count == 18 ) {
		
		$starting_hole = 9;
		
	} else {
		
		$starting_hole = 0;
		
	}
	
	
	$holes_used = array_slice( $holes_info, $starting_hole, $league_holes );


	if($use_handicaps == 1) {
		
			
		//try array slicing and pushing instead of while looping, create a new array of updated scores
		
		$hole_handicap = 1;	
			
		while($match_handicap > 0 ) {
			
			foreach($holes_used as &$hole_used) {
			
				if( $hole_used['hole_handicap'] == $hole_handicap){
				
					$hole_used['score'] = $hole_used['score'] - 1;
					$match_handicap = $match_handicap - 1;
								
				} 
			
			}
			
			unset( $hole_used );
			
			$hole_handicap = $hole_handicap + 1;
			
			if( $hole_handicap > 18 ) {
				$hole_handicap = 1;
			}
			
			
				
			
			
		}
	
	}	
		  
	
	

	
	
	$stroke_differences = array();
    
	
	foreach( $holes_used as $hole_used ) {
		
		$stroke_difference = $hole_used['score'] - $hole_used['par'];
		
		array_push( $stroke_differences, $stroke_difference );
		
	}	
	

	for( $i = 0, $j = $i - 1, $round_holes = count($stroke_differences), $on_train = array(), $train_game_points = array(); $i < $round_holes; $i++, $j++ ) {
		
		
		if( $on_train[$j] == true ) {
			
		
			if ( $stroke_differences[$i] == 1 ) {
				$points = 0;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
				
			} else if( $stroke_differences[$i] == 0 ) {
				$points = 1;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			} else if ( $stroke_differences[$i] == -1 ) {
				$points = 2;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			} else if ( $stroke_differences[$i] == -2 ) {
				$points = 3;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			} else if ( $stroke_differences[$i] == -3 ) {
				$points = 4;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			} else if ( $stroke_differences[$i] == -4 ) {
				$points = 5;
				$on_off = true;
				array_push( $train_game_points, $points );	
				array_push( $on_train, $on_off );					
			} else {
				$points = 0;
				$on_off = false;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			}			
		
		} else {
	
			if ( $stroke_differences[$i] <=0 ) {
				$points = 0;
				$on_off = true;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			} else {
				$points = 0;
				$on_off = false;
				array_push( $train_game_points, $points );
				array_push( $on_train, $on_off );
			}
		}
	}
	
	foreach($train_game_points as $train_game_point) {
		
		$train_game_point_total = $train_game_point_total + $train_game_point;
	
	}
	
	return $train_game_point_total;
		
  } 






  function auto_calculate_match_handicap($handicap, $active_user_handicap, $round_id, $league_holes, $link) {
  
  
  	if( $active_user_handicap ) {
  	
  		$handicap = $active_user_handicap;
  		
  	}
  	
  	$slope_query = mysqli_query( $link, "SELECT slope_rating FROM bg_app_rounds WHERE round_id = '$round_id'" );
	$slope_row = mysqli_fetch_row( $slope_query );
	$slope = $slope_row[0];
	
  	
  	if( $league_holes == 9 ) {
  		
  		$handicap = $handicap/2;
  	
  	}
  	
  	$match_handicap = $handicap * $slope / 113;
  	
  	$match_handicap = round($match_handicap);

  	return $match_handicap;
  	
  }


  function choose_best_round($league_id, $link, $round_number){
  	
  
  	$matches_chosen = array();
		

	$league_holes_query = mysqli_query( $link, "SELECT holes FROM bg_app_league WHERE league_id = '$league_id'" );		
	$league_holes_row = mysqli_fetch_row( $league_holes_query );
	$league_holes = $league_holes_row[0];		
	
	$league_frequency_query = mysqli_query( $link, "SELECT frequency_by_weeks FROM bg_app_league WHERE league_id = '$league_id'" );
	$league_frequency_row = mysqli_fetch_row( $league_frequency_query );
	$league_frequency = $league_frequency_row[0];		
	
	$league_start_date_query = mysqli_query( $link, "SELECT start_date FROM bg_app_league WHERE league_id = '$league_id'" );
	$league_start_date_row = mysqli_fetch_row( $league_start_date_query );
	$league_start_date = $league_start_date_row[0];		
	

	
	$league_start_date_raw = new DateTime($league_start_date);
		
	$today_date_time = new DateTime('now');
		
	$date_difference = date_diff( $today_date_time, $league_start_date_raw );

	$difference_in_days = $date_difference->days;
	
	$weeks_from_start = $difference_in_days / 7;
	
	$round_raw = $weeks_from_start / $league_frequency;
	
	/*
	if( floor($round_raw) == $round_raw ){
	
		$round = $round_raw;
	
	}else{
	
		$round = ceil($round_raw);
	
	}
	*/
	
	$round = $round_number;
	
	$weeks_to_include = $league_frequency * ( $round - 2 );
	
	$round_start_date = date_modify($league_start_date_raw, '+'.$weeks_to_include.' week');
	
	$score_accept_date = date_format($round_start_date, 'Y-m-d');
	
	
	$active_users_query = mysqli_query( $link, "SELECT * "
			 		    ."FROM bg_app_league_participants "
			 		    ."WHERE league_id = '$league_id' "
			 		    ."AND is_confirmed = '1'" );
	
	$active_users = array();

	while( $active_users_row = mysqli_fetch_array( $active_users_query, MYSQLI_ASSOC ) ){
	
		array_push( $active_users, $active_users_row );
		
	}
	
	
	foreach($active_users as $active_user){
		
		$match_options = array();
			
		$user_id = $active_user['user_id'];
		
		$user_id_to_calculate_handicap = $user_id;
		
		$active_user_handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap);
		
		$opponent_query = mysqli_query( $link, "SELECT * FROM bg_app_match_schedule "
						      ."WHERE league_id = '$league_id' "
						      ."AND round_number = '$round'" );
		
		
		$opponent_options = array();
	
		while( $opponent_options_row = mysqli_fetch_array( $opponent_query, MYSQLI_ASSOC ) ){
		
			array_push( $opponent_options, $opponent_options_row );
			
		}
		
		
		
		foreach( $opponent_options as $opponent_option ){	
						      
		
			if( $opponent_option['home_id'] == $user_id ){	
				$opponent = $opponent_option['away_id'];
			}
			
			if( $opponent_option['away_id'] == $user_id ){	
				$opponent = $opponent_option['home_id'];
			}
		
		}
		
		$round_id_and_9_played = array();
		
		if($league_holes == 9) {
			
			$eligible_18_hole_rounds_query = mysqli_query( $link, "SELECT * "
											 ."FROM bg_app_rounds "
										 ."WHERE user_id = '$user_id' "
										 ."AND start_date >= '$score_accept_date' "
										 ."AND front_back_both = 'both' "
										 ."AND is_complete = '1'" );
					 
			$eligible_18_hole_rounds = array();
	
			while( $eligible_18_hole_rounds_row = mysqli_fetch_array( $eligible_18_hole_rounds_query, MYSQLI_ASSOC ) ){
			
				array_push( $eligible_18_hole_rounds, $eligible_18_hole_rounds_row );
				
			}		 
					 
					 
			if( $eligible_18_hole_rounds ) {
				
				foreach($eligible_18_hole_rounds as $eligible_18_hole_round){
				
					
					$eligible_18_hole_round_id = $eligible_18_hole_round['round_id'];
					
					$round_already_used_query = mysqli_query( $link, 
							 "SELECT * "
							."FROM bg_app_match "
							."WHERE league_id = '$league_id' "
							."AND round_id = '$eligible_18_hole_round_id'" );
							 
					$round_already_used = array();
	
					while( $round_already_used_row = mysqli_fetch_array( $round_already_used_query, MYSQLI_ASSOC ) ){
					
						array_push( $round_already_used, $round_already_used_row );
						
					}
							
					
					if($round_already_used){
						
						if( count($round_already_used) == 1 ){
						
							foreach($round_already_used as $round_already_used_info){
						
								array_push( $round_id_and_9_played, 
											array(
												'round_id'	=> $round_already_used_info['round_id'],
												'9_played'	=> $round_already_used_info['front_back_both']
											)
								);
							
							}
						}
					}
				}
			}
			
			
			$eligible_rounds_query = mysqli_query( $link, "SELECT * "
								 ."FROM bg_app_rounds "
								 ."WHERE user_id = '$user_id' "
								 ."AND start_date >= '$score_accept_date' "
								 ."AND is_complete = '1' "
								 ."AND round_id NOT IN (SELECT round_id FROM bg_app_match "
								 ."WHERE league_id = '$league_id')" );
					 
			$eligible_rounds = array();
	
			while( $eligible_rounds_row = mysqli_fetch_array( $eligible_rounds_query, MYSQLI_ASSOC ) ){
			
				array_push( $eligible_rounds, $eligible_rounds_row );
				
			}		 
			

		}else{
		
			$eligible_rounds_query = mysqli_query( $link, "SELECT * "
								 ."FROM bg_app_rounds "
								 ."WHERE user_id = '$user_id' "
								 ."AND start_date >= '$score_accept_date' "
								 ."AND front_back_both = 'both' "
								 ."AND is_complete = '1' "
								 ."AND round_id NOT IN (SELECT round_id FROM bg_app_match "
								 ."WHERE league_id = '$league_id')" );
					 
			$eligible_rounds = array();
	
			while( $eligible_rounds_row = mysqli_fetch_array( $eligible_rounds_query, MYSQLI_ASSOC ) ){
			
				array_push( $eligible_rounds, $eligible_rounds_row );
				
			}		
			
			
		}
		
		
		if( $eligible_rounds || $round_id_and_9_played ){
		
			foreach($eligible_rounds as $eligible_round){
				
				if( $eligible_round['front_back_both'] == "both" ){
					$eligible_round_holes = 18;
				}else{
					$eligible_round_holes = 9;
				}
			
				$round_id = $eligible_round['round_id'];
				
				$match_handicap = auto_calculate_match_handicap($handicap, $active_user_handicap, $round_id, $league_holes, $link);
			
				if($league_holes == 9 && $eligible_round_holes == 18){
					
					$front_back = "front";
					
					$front_score = auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					$front_t_game_points = auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, 
										$league_id, $link);
					
					array_push( $match_options, 
						array(
							'league_id'		=> $league_id,
							'user_id'	 	=> $active_user['user_id'],
							'round_id'		=> $round_id,
							'course_name'		=> $eligible_round['course_name'],
							'front_back_both'	=> "front",
							'match_handicap' 	=> $match_handicap,
							'score'				   		=> $front_score,
							'train_game_points'	=> $front_t_game_points,
							'round_number'		=> $round,
							'opponent'		=> $opponent
						)
					);
										
					$front_back = "back";
					
					$back_score = auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					$back_t_game_points = auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					array_push( $match_options, 
						array(
							'league_id'		=> $league_id,
							'user_id'	 	=> $active_user['user_id'],
							'round_id'		=> $round_id,
							'course_name'		=> $eligible_round['course_name'],
							'front_back_both'	=> "back",
							'match_handicap' 	=> $match_handicap,
							'score'	 		=> $back_score,
							'train_game_points'	=> $back_t_game_points,
							'round_number'		=> $round,
							'opponent'		=> $opponent
						)
					);	
				
				}elseif($league_holes == 18 && $eligible_round_holes == 18){
					
					$front_back = $eligible_round['front_back_both'];
					
					$score = auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					$t_game_points = auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					array_push( $match_options, 
						array(
							'league_id'		=> $league_id,
							'user_id'	 	=> $active_user['user_id'],
							'round_id'		=> $round_id,
							'course_name'		=> $eligible_round['course_name'],
							'front_back_both'	=> $front_back,
							'match_handicap' 	=> $match_handicap,
							'score'		   		=> $score,
							'train_game_points'	=> $t_game_points,
							'round_number'		=> $round,
							'opponent'		=> $opponent
						)
					);	
				
				}elseif($league_holes == 9 && $eligible_round_holes == 9){
					
					$front_back = $eligible_round['front_back_both'];
					
					$score = auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					$t_game_points = auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					array_push( $match_options, 
						array(
							'league_id'		=> $league_id,
							'user_id'	 	=> $active_user['user_id'],
							'round_id'		=> $round_id,
							'course_name'		=> $eligible_round['course_name'],
							'front_back_both'	=> $front_back,
							'match_handicap' 	=> $match_handicap,
							'score'		   		=> $score,
							'train_game_points'	=> $t_game_points,
							'round_number'		=> $round,
							'opponent'		=> $opponent
						)
					);	
				
				}
				
			}
			
			if($round_id_and_9_played) {
			
				$round_id_and_9_played_count = count( $round_id_and_9_played );
				
				for( $i = 0; $i < $round_id_and_9_played_count; $i += 1) {
				
					$round_id = $round_id_and_9_played[$i]['round_id'];
					
					$nine_played = $round_id_and_9_played[$i]['9_played'];
					
					$match_handicap = auto_calculate_match_handicap($handicap, $active_user_handicap, $round_id, $league_holes, $link);	
					
					if( $nine_played == "front" ) {
						
						$front_back = "back";
						
					}else{
						
						$front_back = "front";
					
					}
						
					$score = auto_calculate_score_from_par($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					$t_game_points = auto_calculate_train_game_points($match_handicap, $round_id, $front_back, $league_holes, $league_id, $link);
					
					
					$course_name_query = mysqli_query( $link, "SELECT course_name FROM bg_app_rounds WHERE round_id = '$round_id'" );
					$course_name_row = mysqli_fetch_row( $course_name_query );
					$course_name = $course_name_row[0];													
					
					
					
					array_push( $match_options, 
						array(
							'league_id'			=> $league_id,
							'user_id'		=> $active_user['user_id'],
							'round_id'		=> $round_id,
							'course_name'				=> $course_name,
							'front_back_both'	=> $front_back,
							'match_handicap' 	=> $match_handicap,
							'score'	 		=> $score,
							'train_game_points'	=> $t_game_points,
							'round_number'		=> $round,
							'opponent'		=> $opponent
						)
					);	
				
				}
			
			}
		
			usort( $match_options, function($b, $a) {
		            return $b['score'] > $a['score'];
		        } );
		        
		        array_push( $matches_chosen, $match_options[0] );	
		        
		}
		
		
	
	}

	
	return $matches_chosen;
	

 }	
	


  function update_with_best_rounds($link){

	$new_today = new DateTime('now');
	
	$today = date_format($new_today, 'Y-m-d');
	
	$leagues_to_potentially_update_query = mysqli_query( $link, "SELECT * "
						     ."FROM bg_app_league "
						     ."WHERE start_date < '$today' "
		 				     ."AND is_complete = '0'" );
	
	$leagues_to_potentially_update = array();
	
	while( $leagues_to_potentially_update_row = mysqli_fetch_array( $leagues_to_potentially_update_query, MYSQLI_ASSOC ) ){
	
		array_push( $leagues_to_potentially_update, $leagues_to_potentially_update_row );
	
	}
		
	
	$leagues_to_update = array();
	
	foreach( $leagues_to_potentially_update as $league_to_potentially_update ) {
	
		$start_date = new DateTime( $league_to_potentially_update['start_date'] );
		
		$date_difference = date_diff( $new_today, $start_date );
		
		$difference_in_days = $date_difference->days;
		
		$frequency_in_days = $league_to_potentially_update['frequency_by_weeks'] * 7;
		
		$number = $difference_in_days / $frequency_in_days;
		
		if(floor($number) == $number && $number != 0){
			
			$league_round_number = $number;
			
			array_push( $leagues_to_update, 
				array( $league_to_potentially_update['league_id'], $league_round_number)
			);
		
		}else{
			
			$league_round_number = floor($number);
			
			$verify_update_league_id = $league_to_potentially_update['league_id'];
			
			if( $round_number != 0 ){
				
				$was_league_updated_query = mysqli_query( $link, "SELECT * "
									     ."FROM bg_app_league_points "
									     ."WHERE round_number = '$league_round_number' "
					 				     ."AND league_id = '$verify_update_league_id'" );
					 				     
				$result_rows = mysqli_num_rows($was_league_updated_query);
				
				
		
				if( $result_rows == 0 ){
					
					array_push( $leagues_to_update, 
						array( $verify_update_league_id, $league_round_number)
					);
				
				}
			
			}
			
		}
	
	
	}
	
		
	if($leagues_to_update) {
	
		foreach($leagues_to_update as $league_to_update) {

	
			$league_id = $league_to_update[0];
			$round_number = $league_to_update[1];
			
		
			$matches_chosen = choose_best_round($league_id, $link, $round_number);
			
			$match_count = count( $matches_chosen );
			
			$league_brogeys_query = mysqli_query( $link, "SELECT * "
			 		    ."FROM bg_app_league_participants "
			 		    ."WHERE league_id = '$league_id' "
			 		    ."AND is_confirmed = '1'" );
	
			$league_brogeys = array();
			
			while( $league_brogeys_row = mysqli_fetch_array( $league_brogeys_query, MYSQLI_ASSOC ) ){
			
				array_push( $league_brogeys, $league_brogeys_row );
				
			}
			
			foreach( $league_brogeys as $league_brogey ){
				
				$brogey_id = $league_brogey['user_id'];
				
				$match_play_points = 0;
				
				$match_play_info = auto_calculate_match_play($link, $league_id, $brogey_id, $round_number);
					
				$skins_id = uniqid();
				
				foreach( $match_play_info as $info ){
				
					$match_play_hole_id		= $info['match_hole_id'];
					$match_play_league_id		= $info['league_id'];
					$match_play_user_id		= $info['user_id'];
					$match_play_handicap		= $info['match_handicap'];
					$match_play_round_id		= $info['round_id'];
					$match_play_front_back		= $info['front_back_both'];
					$match_play_course_hole		= $info['course_hole'];
					$match_play_score		= $info['score'];
					$match_play_opponent		= $info['opponent'];
					$match_play_op_handicap		= $info['opponent_handicap'];
					$match_play_op_round_id		= $info['opponent_round_id'];
					$match_play_op_front_back	= $info['opponent_front_back'];
					$match_play_op_course_hole	= $info['opponent_course_hole'];
					$match_play_op_score		= $info['opponent_score'];
					$match_play_points_won		= $info['points_won'];
					
					$match_play_points = $match_play_points + $match_play_points_won;
					
					$match_play_hole_input_query = mysqli_query( $link, 
						 "INSERT INTO bg_app_skins_hole_info "
						."(skins_id, hole_id, course_hole, score, "
						."op_course_hole, op_score, points_won) "
						."VALUES('$skins_id', '$match_play_hole_id', "
						."'$match_play_course_hole', '$match_play_score', "
						."'$match_play_op_course_hole', '$match_play_op_score', "
						."'$match_play_points_won')"
					);
					
					
				}
				
				
				$match_play_info_input_query = mysqli_query( $link,
					 "INSERT INTO bg_app_skins_match_info "
					."(skins_id, league_id, user_id, match_handicap, round_id, "
					."front_back_both, opponent, op_handicap, op_round_id, "
					."op_front_back_both, total_points, round_number) "
					."VALUES('$skins_id', '$match_play_league_id', '$match_play_user_id', "
					."'$match_play_handicap', '$match_play_round_id', '$match_play_front_back', "
					."'$match_play_opponent', '$match_play_op_handicap', '$match_play_op_round_id', "
					."'$match_play_op_front_back', '$match_play_points', '$round_number')"
				);
			
			}
				
			
			
			for( $i = 0; $i < $match_count; $i += 1){
				
				$match_id = uniqid();
				$brogey_id			= $matches_chosen[$i]['user_id'];
				$input_score			= $matches_chosen[$i]['score'];
				$input_train_game_points	= $matches_chosen[$i]['train_game_points'];
				$input_match_handicap		= $matches_chosen[$i]['match_handicap'];
				$input_round_id			= $matches_chosen[$i]['round_id'];
				$input_league_id		= $matches_chosen[$i]['league_id'];
				$input_course_name		= $matches_chosen[$i]['course_name'];
				$input_front_back_both		= $matches_chosen[$i]['front_back_both'];
				$input_round_number		= $round_number;
				$input_opponent			= $matches_chosen[$i]['opponent'];
				$input_match_points		= 0;
				
				$input_match_points_query = mysqli_query( $link, 
					 "SELECT total_points FROM bg_app_skins_match_info "
					."WHERE round_id = '$input_round_id' "
					."AND league_id = '$input_league_id' "
					."AND round_number = '$input_round_number'"
				);
				$input_match_points_row = mysqli_fetch_row( $input_match_points_query );
				$input_match_points = $input_match_points_row[0];
				
				
				$round_selection_input_query = mysqli_query( $link, "INSERT INTO bg_app_match "
										   ."(match_id, "
										   ."user_id, "
										   ."score_from_par, "
										   ."train_game_points, "
										   ."handicap_for_match, "
										   ."round_id, "
										   ."league_id, "
										   ."course_name, "
										   ."front_back_both, "
										   ."league_round_used, "
										   ."opponent) "
										   ."VALUES( "
										   ."'$match_id', "
										   ."'$brogey_id', "
										   ."'$input_score', "
										   ."'$input_match_points', "
										   ."'$input_match_handicap', "
										   ."'$input_round_id', "
										   ."'$input_league_id', "
										   ."'$input_course_name', "
										   ."'$input_front_back_both', "
										   ."'$input_round_number', "
										   ."'$input_opponent')" );
										
			
			}
			
	
		}	
	
	}
	

  }
  