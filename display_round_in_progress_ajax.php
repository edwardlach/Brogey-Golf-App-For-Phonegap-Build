<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


date_default_timezone_set('America/New_York');

$user_id = $_SESSION['id'];

if( $_GET['leagueid'] ){

	$league_id = $_GET['leagueid'];
	$_SESSION['league_id'] = $league_id;
	
}


if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}


include "simple_functions.php";
$link = connect_to_database();


$league_info_display_round_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
$league_info_display_round = array();
while( $league_info_display_round_row = mysqli_fetch_array( $league_info_display_round_query, MYSQLI_ASSOC ) ){
	array_push($league_info_display_round, $league_info_display_round_row );
}	

foreach($league_info_display_round as $league) {

	$league_name			= $league['league_name'];
	$league_holes			= $league['holes'];
	$start_date_for_round_number	= $league['start_date'];
	$use_handicap			= $league['use_handicaps'];
	$frequency			= $league['frequency_by_weeks'];
	
}





$today_for_round_number = new DateTime("now");

$start_date_for_round_number_raw = new DateTime($start_date_for_round_number);
				
$date_difference_for_round_number = date_diff( $today_for_round_number, $start_date_for_round_number_raw );

$difference_in_days_for_round_number = $date_difference_for_round_number->days;

$round_length_in_days = $frequency * 7;

$display_round_number = ceil( $difference_in_days_for_round_number / $round_length_in_days );

$round_number = $display_round_number;

$weeks_to_modify = $display_round_number * $frequency;

$round_end_date_raw = date_modify($start_date_for_round_number_raw, '+'.$weeks_to_modify.' week');

$round_end_date = date_format($round_end_date_raw, 'M d, Y');

if( $use_handicap == 1 ){
	
	$display_handicap = "Yes";
	
}else{
	
	$display_handicap = "No";

}






function find_rounds($league_id, $link, $round_number){

	include "calculate_select_best_score_for_current_round.php";
	
	$matches_chosen = choose_best_round($league_id, $link, $round_number);
	
	usort( $matches_chosen, function($b, $a) {
	    return $b['score'] > $a['score'];
	} );
	
	return $matches_chosen;
	
}

$matches_chosen = find_rounds($league_id, $link, $round_number);


$recent_round_html .= <<<EOHTML
	
	
	    <div id="leaderboard_column_titles">
		<div class="leaderboard_position_column_title">#</div>
		<div class="leaderboard_name_column_title">Brogey</div>
		<div class="leaderboard_score_column_title">Score</div>
		<div class="leaderboard_hcap_column_title">Hcap</div>
	    </div>
	
EOHTML;


$position = 1;

foreach( $matches_chosen as $match ){
	

	$score		= $match['score'];
	$course_name 	= $match['course_name'];
	$opponent	= $match['opponent'];
	$brogey_id	= $match['user_id'];
	$front_back	= $match['front_back_both'];
	$match_score	= 0;
	$hole		= 0;
	
	$match_info = auto_calculate_match_play($link, $league_id, $brogey_id, $round_number);
	 
	
	foreach( $match_info as $match_hole ){
		
		$match_score = $match_score + $match_hole['points_won'];
	
	}

	
	if( $front_back == "both" ){
		
		$nine_played = "";
		
	}else{
	
		$nine_played = $front_back." 9";
		
	}
	
	
	
	if( $use_handicap == 1 ){
	
		$handicap = $match['match_handicap'];
		
	}else{
		
		$handicap = "--";
	
	}
	
	$display_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$brogey_id'" );
	$display_name_row = mysqli_fetch_row( $display_name_query );
	$display_name = $display_name_row[0];
	
	
	$opponent_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$opponent'" );
	$opponent_name_row = mysqli_fetch_row( $opponent_name_query );
	$opponent_name = $opponent_name_row[0];
	
	
	$opponent_matches = choose_best_round($league_id, $link, $round_number);
	
	foreach( $opponent_matches as $opponent_info ){
		
		if( $opponent_info['user_id'] == $opponent ){
			
			$opponent_course_name	= $opponent_info['course_name'];
			$opp_points 		= 0;
			$opp_front_back		= $opponent_info['front_back_both'];
			
			$brogey_id = $opponent;
			
			$opp_match_info = auto_calculate_match_play($link, $league_id, $brogey_id, $round_number);
	 
	
			foreach( $opp_match_info as $opp_match_hole ){
				
				$opp_points = $opp_points + $opp_match_hole['points_won'];
			
			}
			
			if( $opp_front_back == "both" ){
				
				$opp_nine_played = "";
				
			}else{
			
				$opp_nine_played = $front_back." 9";
				
			}
			
			
		
		}
	
	}
	
	if( $match_score > $opp_points ){
		$difference = $match_score - $opp_points;
		$match_status = "Winning by ".$difference;
	}elseif( $match_score < $opp_points ){
		$difference = $opp_points - $match_score;
		$match_status = "Losing by ".$difference;
	}else{
		$match_status = "Tied";
	}
	
	if( !$course_name ){
		$course_name = "No valid score submitted";
		$match_score = 0;
	}
	
	if( !$opponent_course_name ){
		$opponent_course_name = "No valid score submitted";
		$opp_points = 0;
	}
	
		
	
	$recent_round_html .= <<<EOHTML
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title" href="#section_$brogey_id">
		        	<div class="leaderboard_position_column">$position</div>
				<div class="leaderboard_name_column">$display_name</div>
				<div class="leaderboard_score_column">$score</div>
				<div class="leaderboard_hcap_column">$handicap</div>
		        </a>
		        
		        <div id="section_$brogey_id" class="leaderboard_section_content">
	        		<div class="extra_info_title"><span class="match_round_info">Current Match Results:</span></br>
	        				<span>$display_name vs. $opponent_name</span>
	        		</div>
	        		<table>
		        		<td class="extra_info">
						<div class="extra_course_column">$course_name $nine_played</div>
						<div class="extra_score_column">Score: $match_score</div>
					</td>
					<td class="extra_info">
						<div class="extra_course_column">$opponent_course_name $opp_nine_played</div>
						<div class="extra_score_column">Score: $opp_points</div>
					</td>
				</table>
				<div class="current_match_status">Currently $match_status</div>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		

EOHTML;

	$opponent_course_name = "";
	$opp_points = "";
	$opp_front_back	= "";
	$opp_nine_played = "";
	$opp_match_info = "";
	$position += 1;

}


echo $recent_round_html;







?>