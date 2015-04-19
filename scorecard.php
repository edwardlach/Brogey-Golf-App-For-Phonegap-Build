<?php
session_start();

if(!$_SESSION['id']){

	header( "Location: login.php" );

}

if( $_GET['roundid'] ){
	
	$_SESSION['round_id'] = $_GET['roundid'];
	$_SESSION['course_name'] = $_GET['course'];
	header( "Location: scorecard.php" );

	
}



if(!$_SESSION['round_id']){

	header( "Location: scorecard_list.php" );

}


include "calculate_handicap.php";


date_default_timezone_set('America/New_York');



include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);

$query_user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );
$round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );

/* query to find course name */

$course_name_query = "SELECT `course_name` FROM `bg_app_rounds` WHERE `round_id` = '$round_id'";

$run_course_name_query = mysqli_query( $link, $course_name_query );

$course_name_row = mysqli_fetch_row($run_course_name_query);

$course_name = $course_name_row[0];



/* query to determine how many holes will be played */

$holes_to_play_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$round_id'";		 

$run_holes_to_play_query = mysqli_query( $link, $holes_to_play_query );

$holes_to_play_row = mysqli_fetch_row($run_holes_to_play_query);

if( $holes_to_play_row[0] == "both" ){

	$holes_to_play = 18;
	
}else{

	$holes_to_play = 9;

}


/* query to determine if round is complete or not */

$is_complete_query = "SELECT `is_complete` FROM `bg_app_rounds` WHERE `round_id` = '$round_id'";

$run_is_complete_query = mysqli_query( $link, $is_complete_query );

$is_complete_row = mysqli_fetch_row($run_is_complete_query);




/* query to pull all of the hole info already submitted for the current round */

$scorecard_query = "SELECT * FROM `bg_app_holes` "
		  ."WHERE `round_id` = '$round_id' "
		  ."ORDER BY `hole_id` ASC";
		  		 
$run_scorecard_query = mysqli_query( $link, $scorecard_query );

$holes_played = mysqli_num_rows($run_scorecard_query);


$scorecard_results = array();


while( $scorecard_row = mysqli_fetch_array( $run_scorecard_query, MYSQLI_ASSOC ) ){
	
	array_push( $scorecard_results, $scorecard_row );

}






function front_score($scorecard_results) {

	if( $scorecard_results ) {

		foreach($scorecard_results as $hole_score) {
			
			if($hole_score['hole_id'] <= 9) {
				$front_score += ($hole_score['score']);
			}
			
		}
		
		return $front_score;
		
	}
		
}

$front_score = front_score($scorecard_results);	
	
function back_score($scorecard_results) {

	if( $scorecard_results ) {	
		
		foreach($scorecard_results as $hole_score) {
			
			if($hole_score['hole_id'] > 9) {
				$back_score += ($hole_score['score']);
			}
			
		}
		
		return $back_score;
	
	}
		
}

$back_score = back_score($scorecard_results);	
	
function front_par($scorecard_results) {

	if( $scorecard_results ) {
	
		foreach($scorecard_results as $hole_par) {
			
			if($hole_par['hole_id'] <= 9) {
				$front_par += ($hole_par['par']);
			}
			
		}
		
		return $front_par;
	}
}
	
$front_par = front_par($scorecard_results);	
	
function back_par($scorecard_results) {

	if( $scorecard_results ) {

		foreach($scorecard_results as $hole_par) {
			
			if($hole_par['hole_id'] > 9) {
				$back_par += ($hole_par['par']);
			}
			
		}
		
		return $back_par;
	}		
}

$back_par = back_par($scorecard_results);





/* Updates database with final round info */

function finalize_round( $link, $round_id, $front_score, $back_score, $front_par, $back_par ){

	$finalize_round_query = "UPDATE `bg_app_rounds` SET `front_par` = '$front_par', `back_par` = '$back_par', "
				."`front_score` = '$front_score', `back_score` = '$back_score', "
				."`is_complete` = '1' WHERE `round_id` = '$round_id'";
				
	$run_finalize_round_query = mysqli_query( $link, $finalize_round_query );
	
	$user_id_to_calculate_handicap = $_SESSION['id'];
	
	$handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap);
	
	$record_handicap = mysqli_query($link,
		"INSERT INTO bg_app_handicap_record"
		."(record_date, after_round_number, user_id, handicap) "
		."VALUES(now(),	'$round_id', '$user_id_to_calculate_handicap', '$handicap')"
	);

	header( "Location: scorecard.php" );
	
}




/* creates the html table to display all of the round information for normal screens */

if( $is_complete_row[0] == '0' ){

/* If round is not complete allows scorecard to be edited */
	$editable = "editable";
	$html .= <<<EOHTML
		
		<div class="click_to_edit"><i class="fa fa-pencil-square-o"></i> Click a hole number to edit the hole&apos;s content.</div>
	
EOHTML;
	
}else{	

/* If round is complete, prohibits scorecard from being editied */	
	$editable = "";

}

	
$html .= <<<EOHTML
<table  id="entire_scorecard">
<tbody class="$editable">
    	<th id="scorecard">
    		<div class="scorecard_hole">Hole</div>
    		<div class="scorecard_par">Par</div>
    		<div class="scorecard_handicap">Handicap</div>
    		<div class="scorecard_score">Score</div>
    	</th>
EOHTML;


foreach( $scorecard_results as $row ){

	$score 		= $row['score'];
	$hole		= $row['hole_id'];
	$par		= $row['par'];
	$handicap	= $row['hole_handicap'];
	$bad_hole	= $par + 2;
	
	if( $hole <= 9 ){
		
		$front_score_display = $front_score_display + $score;
		$front_par_display = $front_par_display + $par;
		
	}else{
	
		$back_score_display = $back_score_display + $score;
		$back_par_display = $back_par_display + $par;
	
	}	
	
	$total_score_display = $total_score_display + $score;
	$total_par_display = $total_par_display + $par;
	

	if( $holes_played > 9 ){
        	      
		if( $score == $par + 1 ){
			
			$scorecard_score_marking = "scorecard_score_bogey";
			
			if( $hole <= 9 ){
				
				$scorecard_column_class = "scorecard_front_nine";
			            
			}else{
					
				$scorecard_column_class = "scorecard_back_nine";
					
			}
					
		}elseif( $score >= $bad_hole ){
			
			$scorecard_score_marking = "scorecard_score_bad_hole";
			
			if( $hole <= 9 ){
				
				$scorecard_column_class = "scorecard_front_nine";
			
			}else{
			
				$scorecard_column_class = "scorecard_back_nine";
				
			}
			
		}elseif( $score < $par ){
			
			$scorecard_score_marking = "scorecard_score_good_hole";
			
			if( $hole <= 9 ){
				
				$scorecard_column_class = "scorecard_front_nine";
			
			}else{
			
				$scorecard_column_class = "scorecard_back_nine";
				
			}
			
		}else{
			
			$scorecard_score_marking = "scorecard_score_par";
			
			if( $hole <= 9 ){
				
				$scorecard_column_class = "scorecard_front_nine";
			
			}else{
			
				$scorecard_column_class = "scorecard_back_nine";
				
			}
			
		}
		
	}else{
		
		$scorecard_column_class = "scorecard_front_nine";
		   
		if( $score == $par + 1 ){

			$scorecard_score_marking = "scorecard_score_bogey";

		}elseif( $score >= $bad_hole ){
			
			$scorecard_score_marking = "scorecard_score_bad_hole";
					
		}elseif( $score < $par ){
			
			$scorecard_score_marking = "scorecard_score_good_hole";
					
		}else{
	
			$scorecard_score_marking = "scorecard_score_par";
						
		}
		
	}
	
	$html .= <<<EOHTML
				
		<td class="$scorecard_column_class" id="hole_$hole">
		    <div class="scorecard_hole" id="hole_$hole">$hole</div>
	            <div class="scorecard_par" id="hole_$hole">$par</div>
	            <div class="scorecard_handicap" id="hole_$hole">$handicap</div>
	            <div class="scorecard_score" id="hole_$hole">
	            	<span class="$scorecard_score_marking" id="hole_$hole">$score</span>
	            </div>
	        </td>  
EOHTML;


	if( $hole == 9 ) {
		
		$scorecard_column_class = "scorecard_front_nine";
		$in_out = "Out";
		$par_display = $front_par_display;
		$score_display = $front_score_display;
		
		$html .= <<<EOHTML
			
		<td class="$scorecard_column_class">	
			<div class="scorecard_hole">$in_out</div>
			<div class="scorecard_par">$par_display</div>
			<div class="scorecard_handicap">|</div>
			<div class="scorecard_score">$score_display</div>
		</td>
EOHTML;
		
	}
	
	
	if( $hole == 18 ) {
		
		if( $holes_played > 9 ){	
			
			$scorecard_column_class = "scorecard_back_nine";
			$in_out = "In";
			$par_display = $back_par_display;
			$score_display = $back_score_display;
			
			
		}elseif( $holes_to_play == 9 ){
			
			$scorecard_column_class = "scorecard_front_nine";
			$in_out = "In";
			$par_display = $back_par_display;
			$score_display = $back_score_display;
		
		}else{
		
			$scorecard_column_class = "scorecard_front_nine";
			$in_out = "Out";
			$par_display = $front_par_display;
			$score_display = $front_score_display;
		
		}
		
		$html .= <<<EOHTML
			
		<td class="$scorecard_column_class">	
			<div class="scorecard_hole">$in_out</div>
			<div class="scorecard_par">$par_display</div>
			<div class="scorecard_handicap">|</div>
			<div class="scorecard_score">$score_display</div>
		</td>
EOHTML;
			
	}
	
}	


$total_par = $front_par + $back_par;
$total_score = $front_score + $back_score;

$html .= <<<EOHTML
		
		<td id="scorecard">
			<div class="scorecard_hole">Total</div>
			<div class="scorecard_par">$total_par</div>
			<div class="scorecard_handicap">|</div>
			<div class="scorecard_score">$total_score</div>
		</td>


</tbody>
</table><!-- /scorecard -->

EOHTML;



$small_screen_html = <<<EOHTML
<table  id="entire_scorecard_small_screen">
<tbody class="$editable">
    	<tr id="scorecard_small_screen">
    		<th class="scorecard_hole">Hole</th>
    		<th class="scorecard_par">Par</th>
    		<th class="scorecard_handicap">Handicap</th>
    		<th class="scorecard_score_small_screen">Score</th>
    	</tr>
EOHTML;

foreach( $scorecard_results as $row ){

	$score 		= $row['score'];
	$hole			= $row['hole_id'];
	$par		= $row['par'];
	$handicap		= $row['hole_handicap'];
	$bad_hole		= $par + 2;
	
	if( $hole <= 9 ){
		
		$front_score_small_display = $front_score_small_display + $score;
		$front_par_small_display = $front_par_small_display + $par;
		
	}else{
	
		$back_score_small_display = $back_score_small_display + $score;
		$back_par_small_display = $back_par_small_display + $par;
	
	}	
	
	$total_score_small_display = $total_score_small_display + $score;
	$total_par_small_display = $total_par_small_display + $par;
	

	if( $holes_played > 9 ){
        
		if( $score == $par + 1 ){
			
			$small_scorecard_score_marking = "scorecard_score_bogey";
			
			if( $hole <= 9 ){
				
				$small_scorecard_row_class = "scorecard_front_nine_small_screen";
					        
			}else{
			
				$small_scorecard_row_class = "scorecard_back_nine_small_screen";
				
			}
					
		}elseif( $score >= $bad_hole ){
		
			$small_scorecard_score_marking = "scorecard_score_bad_hole";
		
			if( $hole <= 9 ){
				
				$small_scorecard_row_class = "scorecard_front_nine_small_screen";
					        
			}else{
			
				$small_scorecard_row_class = "scorecard_back_nine_small_screen";
				
			}
			
		}elseif( $score < $par ){
			
			$small_scorecard_score_marking = "scorecard_score_good_hole";
		
			if( $hole <= 9 ){
				
				$small_scorecard_row_class = "scorecard_front_nine_small_screen";
					        
			}else{
			
				$small_scorecard_row_class = "scorecard_back_nine_small_screen";
				
			}
							
		}else{
			
			$small_scorecard_score_marking = "scorecard_score_par";
		
			if( $hole <= 9 ){
				
				$small_scorecard_row_class = "scorecard_front_nine_small_screen";
					        
			}else{
			
				$small_scorecard_row_class = "scorecard_back_nine_small_screen";
				
			}
			
		}
		
	}else{
		
		$small_scorecard_row_class = "scorecard_front_nine_small_screen";
		    
		if( $score == $par + 1 ){

			$small_scorecard_score_marking = "scorecard_score_bogey";
			
		}elseif( $score >= $bad_hole ){
			
			$small_scorecard_score_marking = "scorecard_score_bad_hole";
					
		}elseif( $score < $par ){
		
			$small_scorecard_score_marking = "scorecard_score_good_hole";

		}else{

			$small_scorecard_score_marking = "scorecard_score_par";		
			
		}
		
	}  
	
	$small_screen_html .= <<<EOHTML
				
		<tr class="$small_scorecard_row_class" id="hole_$hole">
		    <td class="scorecard_hole" id="hole_$hole">$hole</td>
	            <td class="scorecard_par" id="hole_$hole">$par</td>
	            <td class="scorecard_handicap" id="hole_$hole">$handicap</td>
	            <td class="scorecard_score_small_screen" id="hole_$hole">
	            	<span class="$small_scorecard_score_marking" id="hole_$hole">$score</span>
	            </td>
	        </tr>  
EOHTML;

	if( $hole == 9 ) {
	
		$small_screen_html .= <<<EOHTML
		
		<tr class="scorecard_front_nine_small_screen">	
			<td class="scorecard_hole">Out</td>
			<td class="scorecard_par">$front_par_small_display</td>
			<td class="scorecard_handicap">&horbar;</td>
			<td class="scorecard_score_small_screen">$front_score_small_display</td>
		</tr>
EOHTML;
	}
	
	
	if( $hole == 18 ) {
		
		if( $holes_played > 9 ){
			
			$small_scorecard_row_class = "scorecard_back_nine_small_screen";
			$small_in_out = "In";
			$small_par_display = $back_par_small_display;
			$small_score_display = $back_score_small_display;
			
		}elseif( $holes_to_play == 9 ){
			
			$small_scorecard_row_class = "scorecard_back_nine_small_screen";
			$small_in_out = "In";
			$small_par_display = $back_par_small_display;
			$small_score_display = $back_score_small_display;
			
		}else{
			
			$small_scorecard_row_class = "scorecard_front_nine_small_screen";
			$small_in_out = "Out";
			$small_par_display = $front_par_small_display;
			$small_score_display = $front_score_small_display;
		
		}	
		
		$small_screen_html .= <<<EOHTML
		
			<tr class="$small_scorecard_row_class">	
				<td class="scorecard_hole">$small_in_out</td>
				<td class="scorecard_par">$small_par_display</td>
				<td class="scorecard_handicap">&horbar;</td>
				<td class="scorecard_score_small_screen">$small_score_display</td>
			</tr>
EOHTML;
			
	}
	


}

$total_par = $front_par + $back_par;
$total_score = $front_score + $back_score;

$small_screen_html .= <<<EOHTML
		
		<tr id="scorecard_small_screen">
			<td class="scorecard_hole">Total</td>
			<td class="scorecard_par">$total_par</td>
			<td class="scorecard_handicap">&horbar;</td>
			<td class="scorecard_score_small_screen">$total_score</td>
		</tr>


</tbody>
</table><!-- /scorecard -->

EOHTML;










/* if round is not finished it creates a link to take you back to the scoreinput page */

if( $holes_to_play > $holes_played ){

	$next_hole_html = <<<EOHTML
	
	<a href="score_input.php" data-role="button" id="add_next_hole">Add Next Hole</a>

EOHTML;

}


/* creates a link that sends a query string to kickoff the code to finalize the round via a query string */

if( $holes_to_play <= $holes_played && $is_complete_row[0] == '0'){
	
	$round_hole_handicaps_query = mysqli_query( $link, 
		"SELECT hole_handicap FROM bg_app_holes "
		."WHERE round_id = '$round_id' "
	);
	
	
	$round_hole_handicaps = array();
	
	
	while( $round_hole_handicaps_row = mysqli_fetch_array( $round_hole_handicaps_query, MYSQLI_ASSOC ) ){
		
		array_push( $round_hole_handicaps, $round_hole_handicaps_row );
	
	}
	
	$array_to_check_for_dups = $round_hole_handicaps;
	
	$has_dups = check_array_for_dups($array_to_check_for_dups);
	
	if( $has_dups != 0 ){
		
		foreach( $has_dups as $dup ){
		
		
		
		$finalize_round_html .= <<<EOHTML
			
			<div class="error_message">Handicap $dup was used more than once.  Please give each hole a unique handicap before you finish this round.</div>
			
EOHTML;
		}

	}else{
	
		$finalize_round_html = <<<EOHTML
		
			<a href="scorecard.php?finalize=1" data-role="button" id="finalize_round">Finalize Round</a>

EOHTML;
	
	}

}


/* Runs the round finaliztion function if correct query string received */
if( $_GET['finalize'] ){
	
	finalize_round( $link, $round_id, $front_score, $back_score, $front_par, $back_par );
	
}


/* adds a flip button if more than 9 holes */
if( $holes_played > 9 ){
	
	$flip_html = <<<EOHTML
	
		<a id="flip" data-role="button">Click to flip &olarr;</a>

EOHTML;

}


/* Creates course name html */

	$course_name_html = <<<EOHTML
	
		<div id="scorecard_course_name">$course_name</div>
		
EOHTML;






?>



<!doctype html>

<html>

<head>

	<title>Brogey Golf</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
	
		$(document).bind("mobileinit", function () {
		    $.mobile.ajaxEnabled = false;
		});
	
	</script>	
	<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-61063633-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
	
	

</head>

<body> 

<div data-role="page" id="scorecard">
	
	<div class="panel left" data-role="panel" data-position="left" id="brogey_menu">
		<ul id="brogey_menu">
			<a id="menu_link" href="brogeygolf.com" data-ajax="false"><li class="brogey_menu">Home</li></a>
			<a id="menu_link" href="round_info_input.php" data-ajax="false"><li class="brogey_menu">Add Round</li></a>
			<a id="menu_link" href="continue_round.php" data-ajax="false"><li class="brogey_menu">Continue Round</li></a>
			<a id="menu_link"><li class="brogey_menu" id="league_tools">League Setup</li></a>
			<a id="menu_link" href="league_setup.php" data-ajax="false"><li class="brogey_sub_menu" id="top_sub_item">Start a New League</li></a>
			<a id="menu_link" href="league_add_member.php" data-ajax="false"><li class="brogey_sub_menu" id="top_sub_item">Add Member</li></a>
			<a id="menu_link" href="league_edit_settings.php" data-ajax="false"><li class="brogey_sub_menu">Edit League Settings</li></a>
			<a id="menu_link" href="league_accept_invite.php" data-ajax="false"><li class="brogey_menu" id="invite_notifications">Accept League Invite</li></a>
			<a id="menu_link" href="email_invite.php" data-ajax="false"><li class="brogey_menu invite_menu_item">Invite New Brogeys</li></a>
			<a id="menu_link" href="scorecard_list.php" data-ajax="false"><li class="brogey_menu">Scorecards</li></a>
			<a id="menu_link" href="historical_leagues.php" data-ajax="false"><li class="brogey_menu">Historical Leagues</li></a>
			<a id="menu_link" href="faq.php" data-ajax="false"><li class="brogey_menu">FAQs</li></a>
			<a id="menu_link" href="brogey_contact.php" data-ajax="false"><li class="brogey_menu">Contact Us!</li></a>
			<a id="menu_link" href="logout.php" data-ajax="false"><li class="brogey_menu">Logout</li></a>
			<a id="menu_link" href="user_info_edit.php" data-ajax="false">
				<li class="brogey_menu"><?php echo $user_name; ?> <i class="fa fa-cog"></i></li>
			</a> 
		</ul>
	</div><!-- /div data-role="panel" id="brogey_menu"-->
	
	<div data-role="header" data-position="fixed">
		<div class="site_title">
			<a id="menu_button" href="#brogey_menu">
				<div class="menu_button">
					<span id="menu_button">menu</span>
				</div>
			</a>
			<div>
				<a id="home_page_link" href="brogeygolf.com" data-ajax="false">	
					<img id="home_page_icon" src="/images/9349HomeButton.png" style="width:45px;height:34px">
				</a>	
				<div id="site_title">
					<span id="site_title_long"><?php echo $course_name; ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	
	


	<div data-role="content" class="scorecard_content">
		
		
		<div class="messages"></div>
		<?php echo $flip_html; ?>
		<!-- <div id="click_to_edit">*Click a hole number to edit</div> -->
		<?php echo $html; ?>
		<?php echo $small_screen_html; ?>
		<?php echo $next_hole_html; ?>	
		<?php echo $finalize_round_html; ?>
		
		
		
	</div><!-- /content -->
	
	
</div><!-- /page -->



<script type="text/javascript">

$('#league_tools').on('click', function(){
	
	if ( $('.brogey_sub_menu').css('display') == 'none' ){
		
		$('.brogey_sub_menu').show();
	
	}else{
	
		$('.brogey_sub_menu').hide();
		
	}
		
} );



$(window).resize(function(){
	if ($(window).width() <= 500){	
		$('#entire_scorecard').hide();
		$('#entire_scorecard_small_screen').show();
	}else{
		$('#entire_scorecard').show();
		$('#entire_scorecard_small_screen').hide();
	}	
});




$('#flip').on( 'click', function flip_normal() {
	
	$(".messages").html("");
	$(".messages").removeClass('error_message');
	
	if( $('.scorecard_front_nine').css( 'display' ) == 'none' ) {
		$('.scorecard_front_nine').show();
		$('.scorecard_back_nine').hide();
	}else{
		$('.scorecard_front_nine').hide();
		$('.scorecard_back_nine').show();
	}
	
});

$('#flip').on( 'click', function flip_small_screen() {
	
	$(".messages").html("");
	$(".messages").removeClass('error_message');
	
	if( $('.scorecard_front_nine_small_screen').css( 'display' ) == 'none' ) {
		$('.scorecard_front_nine_small_screen').show();
		$('.scorecard_back_nine_small_screen').hide();
	}else{
		$('.scorecard_front_nine_small_screen').hide();
		$('.scorecard_back_nine_small_screen').show();
	}
	
});


if( $('tbody').attr('class') == "editable" ){  /* beginning of script for editable tables */



$('.scorecard_hole').on( 'click', function() {
	
	$(".messages").html("");
	$(".messages").removeClass('error_message');
	
	var hole = $(this).html();
	var id = "hole_" + hole;
	var par = $('.scorecard_par#'+id).html();
	var handicap = $('.scorecard_handicap#'+id).html();
	var score = $('.scorecard_score_bogey#'+id).html();
	
	if( !score ){
		var score = $('.scorecard_score_par#'+id).html();
	}
	if( !score ){
		var score = $('.scorecard_score_good_hole#'+id).html();
	}
	if( !score ){
		var score = $('.scorecard_score_bad_hole#'+id).html();
	}
	
	
	
	$(".scorecard_front_nine#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<td class="scorecard_front_nine">'
				+'<div class="scorecard_hole active_edit">'+hole+'</div>'
				+'<div class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_score">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</div>'
			+'</td>'
		+'</form>');


	$(".scorecard_back_nine#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<td class="scorecard_front_nine">'
				+'<div class="scorecard_hole active_edit">'+hole+'</div>'
				+'<div class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_score">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</div>'
			+'</td>'
		+'</form>');
		
		
	$(".scorecard_front_nine_small_screen#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<tr class="scorecard_front_nine">'
				+'<td class="scorecard_hole active_edit">'+hole+'</td>'
				+'<td class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_score_small_screen">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</td>'
			+'</tr>'
		+'</form>');
		
		
		
	$(".scorecard_back_nine_small_screen#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<tr class="scorecard_front_nine">'
				+'<td class="scorecard_hole active_edit">'+hole+'</td>'
				+'<td class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_score_small_screen">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</td>'
			+'</tr>'
		+'</form>');


});

$(".editable").on("click", ".active_edit", function(){
	
	$(".messages").html("");
	$(".messages").removeClass('error_message');
		
	var hole_id = $(this).html();
	
	$.post("scorecard_edit_return_ajax.php",
	  {
	    hole:hole_id
	  },
	  function(data,status){
	  	
	  	var jsonData = jQuery.parseJSON( data );
	  	var row = jsonData[0];
	  	
	  	var hole	= row.hole;
	  	var par		= row.par;
	  	var score	= row.score;
	  	var par_int	= parseInt(par);
	  	var score_int	= parseInt(score);
	  	var handicap	= row.handicap;
	  	var bogey	= par_int + 1;
	  	var html_scorecard_score_marking = "scorecard_score_par";
	  	
	  	if( score_int == bogey ){
	  		
	  		html_scorecard_score_marking = "scorecard_score_bogey";
	  	
	  	}
	  	
	  	if( score_int < par_int){
	  	
	  		html_scorecard_score_marking = "scorecard_score_good_hole";
	  	
	  	}
	  	
	  	if( score_int > bogey ){
	  		
	  		html_scorecard_score_marking = "scorecard_score_bad_hole";
	  		
	  	}
	  		
	  		
	  	$.get( "front_back_ajax_query.php", function( data ) {
  				

			if( data == "back" ){
			  
			  	var front_back = ".scorecard_front_nine#hole_"+hole;
		  		var front_back_small = ".scorecard_front_nine_small_screen#hole_"+hole;
		  		
		  		var front_back_for_html = "scorecard_front_nine";
		  		var front_back_small_for_html = "scorecard_front_nine_small_screen";
		  		
		  	}else{
		  		
		  		if( hole <= 9 ){
		  		
			  		var front_back = ".scorecard_front_nine#hole_"+hole;
			  		var front_back_small = ".scorecard_front_nine_small_screen#hole_"+hole;
			  		
			  		var front_back_for_html = "scorecard_front_nine";
			  		var front_back_small_for_html = "scorecard_front_nine_small_screen";
			  	
			  	}else{
			  		
			  		var front_back = ".scorecard_back_nine#hole_"+hole;
			  		var front_back_small = ".scorecard_back_nine_small_screen#hole_"+hole;
			  		
			  		var front_back_for_html = "scorecard_back_nine";
			  		var front_back_small_for_html = "scorecard_back_nine_small_screen";
			  		
			  	}
			  
			}
			
			$(front_back).html(
		  	'<div class="scorecard_hole edit_clicked" id="hole_'+hole+'">'+hole+'</div>'
			  +'<div class="scorecard_par" id="hole_'+hole+'">'+par+'</div>'
			  +'<div class="scorecard_handicap" id="hole_'+hole+'">'+handicap+'</div>'
			  +'<div class="scorecard_score" id="hole_'+hole+'">'
				+'<span class="'+html_scorecard_score_marking+'" id="hole_'+hole+'">'+score+'</span>'
			  +'</div>');
		       		
		       	$(front_back_small).html(
		       	'<td class="scorecard_hole edit_clicked" id="hole_'+hole+'">'+hole+'</td>'
			+'<td class="scorecard_par" id="hole_'+hole+'">'+par+'</td>'
			+'<td class="scorecard_handicap" id="hole_'+hole+'">'+handicap+'</td>'
			+'<td class="scorecard_score_small_screen" id="hole_'+hole+'">'
				+'<span class="'+html_scorecard_score_marking+'" id="hole_'+score+'">'+hole+'</span>'
			+'</td>');
		  
		});
	
	  });

});

$('.editable').on( 'click', '.edit_clicked',  function() {
	
	$(".messages").html("");
	$(".messages").removeClass('error_message');
	
	var hole = $(this).html();
	var id = "hole_" + hole;
	var par = $('.scorecard_par#'+id).html();
	var handicap = $('.scorecard_handicap#'+id).html();
	var score = $('.scorecard_score_bogey#'+id).html();
	
	if( !score ){
		var score = $('.scorecard_score_par#'+id).html();
	}
	if( !score ){
		var score = $('.scorecard_score_good_hole#'+id).html();
	}
	if( !score ){
		var score = $('.scorecard_score_bad_hole#'+id).html();
	}
	
	
	
	$(".scorecard_front_nine#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<td class="scorecard_front_nine">'
				+'<div class="scorecard_hole active_edit">'+hole+'</div>'
				+'<div class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_score">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</div>'
			+'</td>'
		+'</form>');


	$(".scorecard_back_nine#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<td class="scorecard_front_nine">'
				+'<div class="scorecard_hole active_edit">'+hole+'</div>'
				+'<div class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</div>'
				+'<div class="scorecard_score">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</div>'
			+'</td>'
		+'</form>');
		
		
	$(".scorecard_front_nine_small_screen#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<tr class="scorecard_front_nine">'
				+'<td class="scorecard_hole active_edit">'+hole+'</td>'
				+'<td class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_score_small_screen">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</td>'
			+'</tr>'
		+'</form>');
		
		
		
	$(".scorecard_back_nine_small_screen#"+id).html('<form action="edit_scorecard.php" class="edit_scorecard_form" id="'+hole+'" method="post">'
			+'<tr class="scorecard_front_nine">'
				+'<td class="scorecard_hole active_edit">'+hole+'</td>'
				+'<td class="scorecard_par">'
					+'<select name="par" class="par" id="'+hole+'">'
						+'<option value="'+par+'">'+par+'</option>'	
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_handicap">'
					+'<select name="hole_handicap" class="hole_handicap" id="'+hole+'">'
						+'<option value="'+handicap+'">'+handicap+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
						+'<option value="16">16</option>'
						+'<option value="17">17</option>'
						+'<option value="18">18</option>'
					+'</select>'
				+'</td>'
				+'<td class="scorecard_score_small_screen">'
					+'<select name="score" class="score" id="'+hole+'">'
						+'<option value="'+score+'">'+score+'</option>'
						+'<option value="1">1</option>'
						+'<option value="2">2</option>'
						+'<option value="3">3</option>'
						+'<option value="4">4</option>'
						+'<option value="5">5</option>'
						+'<option value="6">6</option>'
						+'<option value="7">7</option>'
						+'<option value="8">8</option>'
						+'<option value="9">9</option>'
						+'<option value="10">10</option>'
						+'<option value="11">11</option>'
						+'<option value="12">12</option>'
						+'<option value="13">13</option>'
						+'<option value="14">14</option>'
						+'<option value="15">15</option>'
					+'</select>'
				+'</td>'
			+'</tr>'
		+'</form>');


});





/* Ajax requests to change data on front nine of scorecard */

$(".scorecard_front_nine").on("change", ".score", function(){
	
	var score = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    score:score,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	    
	  });

});

$(".scorecard_front_nine").on("change", ".par", function(){
	
	var par = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    par:par,
	    hole:hole
	  },
	  function(data,status){
	    
	  	location.reload();
	    
	  });

});

$(".scorecard_front_nine").on("change", ".hole_handicap", function(){
	
	var hole_handicap = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    hole_handicap:hole_handicap,
	    hole:hole
	  },
	  function(data,status){
	  	
	  	location.reload();
	  
	  });

});


/* Ajax requests to change data on back nine of scorecard */

$(".scorecard_back_nine").on("change", ".score", function(){
	
	var score = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    score:score,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	    
	  });

});

$(".scorecard_back_nine").on("change", ".par", function(){
	
	var par = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    par:par,
	    hole:hole
	  },
	  function(data,status){
	    
	  	location.reload();
	    
	  });

});

$(".scorecard_back_nine").on("change", ".hole_handicap", function(){
	
	var hole_handicap = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    hole_handicap:hole_handicap,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	  
	  });

});


/* Ajax requests to change data on front nine of small scorecard */

$(".scorecard_front_nine_small_screen").on("change", ".score", function(){
	
	var score = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    score:score,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	    
	  });

});

$(".scorecard_front_nine_small_screen").on("change", ".par", function(){
	
	var par = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    par:par,
	    hole:hole
	  },
	  function(data,status){
	    
	  	location.reload();
	    
	  });

});

$(".scorecard_front_nine_small_screen").on("change", ".hole_handicap", function(){
	
	var hole_handicap = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    hole_handicap:hole_handicap,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	  
	  });

});


/* Ajax requests to change data on back nine of small scorecard */

$(".scorecard_back_nine_small_screen").on("change", ".score", function(){
	
	var score = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    score:score,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	    
	  });

});

$(".scorecard_back_nine_small_screen").on("change", ".par", function(){
	
	var par = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    par:par,
	    hole:hole
	  },
	  function(data,status){
	    
	  	location.reload();
	    
	  });

});

$(".scorecard_back_nine_small_screen").on("change", ".hole_handicap", function(){
	
	var hole_handicap = $(this).val();
	var hole = $(this).attr('id');
	
	$.post("edit_scorecard.php",
	  {
	    hole_handicap:hole_handicap,
	    hole:hole
	  },
	  function(data,status){
	  
	  	location.reload();
	  
	  });

});

}/* end of script for editable tables */


$.get( "league_invite_notification.php", function( data ) {
  
  $("#invite_notifications").html(data);
  
});


</script>





</body>

</html>