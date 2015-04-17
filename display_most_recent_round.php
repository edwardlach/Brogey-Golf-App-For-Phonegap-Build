<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


date_default_timezone_set('America/New_York');
include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);



$user_id   = $_SESSION['id'];

/*Sets League Id to pull info specific to selected league*/

if( $_GET['leagueid'] ){

	$league_id = $_GET['leagueid'];
	$_SESSION['league_id'] = $league_id;
	
}


if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}

/* Grabs info for extra info popout */

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

if( $use_handicap == 1 ){
	
	$display_handicap = "Yes";
	
}else{
	
	$display_handicap = "No";

}





/*creates the leaderboard side pop out*/

include "display_leaderboard.php";

$results = create_leaderboard($league_id, $user_id, $historical);


/*creates breakdown of most recent round*/



if( $_GET['previousround'] ){

	$round_number = $_GET['previousround'];

}else{

	$start_date_query = mysqli_query( $link, "SELECT start_date FROM bg_app_league WHERE league_id = '$league_id'" );
	$start_date_row = mysqli_fetch_row( $start_date_query );
	$start_date = $start_date_row[0];
	
	$league_start_date_raw = new DateTime($start_date);
	
	$new_today = new DateTime('now');
	
	$date_difference = date_diff( $new_today, $league_start_date_raw );
	
	$difference_in_days = $date_difference->days;
	
	$round_number = floor( $difference_in_days / 7 );

}


$start_date_for_round_number_raw = new DateTime($start_date_for_round_number);
				
$weeks_to_modify = $round_number * $frequency;

$round_end_date_raw = date_modify($start_date_for_round_number_raw, '+'.$weeks_to_modify.' week');

$round_end_date = date_format($round_end_date_raw, 'M d, Y');




$round_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
					."WHERE league_id = '$league_id' "
					."AND round_number = '$round_number' "
					."ORDER BY position ASC" );
					
$round_info = array();

while( $round_info_row = mysqli_fetch_array( $round_info_query, MYSQLI_ASSOC ) ){
	array_push($round_info, $round_info_row );
}


$recent_round_html .= <<<EOHTML
	
	    <div id="leaderboard_column_titles">
		<div class="leaderboard_position_column_title">#</div>
		<div class="leaderboard_name_column_title">Brogey</div>
		<div class="leaderboard_score_column_title">Score</div>
		<div class="leaderboard_points_column_title">Pts</div>
	    </div>
	
EOHTML;



foreach( $round_info as $brogey_info ){

	/* Gather variables to display on round result page */
	$display_name	= $brogey_info['display_name'];
	$position	= $brogey_info['position'];
	$score		= $brogey_info['score_from_par'];
	$match_score	= $brogey_info['match_points'];
	$opponent	= $brogey_info['opponent'];
	$opp_points	= $brogey_info['opponents_match_points'];
	$match_win	= $brogey_info['match_win'];
	$leader_points	= $brogey_info['points_from_leaderboard'];
	$total_points	= $brogey_info['total_points'];
	$brogey_id	= $brogey_info['user_id'];
	$o_points_league_id	= $brogey_info['league_id'];
	$o_points_round_number	= $brogey_info['round_number'];
	
	$skins_id_query = mysqli_query( $link, "SELECT skins_id FROM bg_app_skins_match_info "
					      ."WHERE league_id = '$o_points_league_id' "
					      ."AND round_number = '$o_points_round_number' "
					      ."AND user_id = '$brogey_id'" );
	$skins_id_row = mysqli_fetch_row( $skins_id_query );
	$skins_id = $skins_id_row[0];
	
	$skin_holes_query = mysqli_query( $link, "SELECT * FROM bg_app_skins_hole_info "
						."WHERE skins_id = '$skins_id' "
						."ORDER BY hole_id ASC" );
	$skin_holes = array();

	while( $skin_holes_row = mysqli_fetch_array( $skin_holes_query, MYSQLI_ASSOC ) ){
		array_push($skin_holes, $skin_holes_row );
	}					
						
	
	
	$opponent_name_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$opponent'" );
	$opponent_name_row = mysqli_fetch_row( $opponent_name_query );
	$opponent_name = $opponent_name_row[0];
	
	$course_name_query = mysqli_query( $link, "SELECT course_name FROM bg_app_match "
						 ."WHERE league_id = '$league_id' "
						 ."AND user_id = '$brogey_id' "
						 ."AND league_round_used = '$round_number'" );
	$course_name_row = mysqli_fetch_row( $course_name_query );
	$course_name = $course_name_row[0];
	
	$opponent_course_name_query = mysqli_query( $link, "SELECT course_name FROM bg_app_match "
						 ."WHERE league_id = '$league_id' "
						 ."AND user_id = '$opponent' "
						 ."AND league_round_used = '$round_number'" );
	$opponent_course_name_row = mysqli_fetch_row( $opponent_course_name_query );
	$opponent_course_name = $opponent_course_name_row[0];
	
	if( !$course_name ){
		$course_name = "No valid score submitted";
	}
	
	if( !$opponent_course_name ){
		$opponent_course_name = "No valid score submitted";
	}
	
	if( $match_win == 0 ){
		$match_points = 0;
		$o_match_points = 50;
		$display_match_result = "LOSS";
	}elseif( $match_win == 1 ){
		$match_points = 50;
		$o_match_points = 0;
		$display_match_result = "WIN";
	}elseif( $match_win == 2 ){
		$match_points = 25;
		$o_match_points = 25;
		$display_match_result = "DRAW";
	}else{
		$match_points = 0;
		$display_match_result = "FORFEIT";
		$o_match_points_query = mysqli_query( $link, "SELECT points_from_match FROM bg_app_league_points "
							 ."WHERE league_id = '$o_points_league_id' "
							 ."AND user_id = '$opponent' "
							 ."AND round_number = '$o_points_round_number'" );
		$o_match_points_row = mysqli_fetch_row( $o_match_points_query );
		$o_match_points = $o_match_points_row[0];
	}
		
	$name_for_initials = $display_name;
	
	$user_initials = initials($name_for_initials);
	
	$name_for_initials = $opponent_name;
	
	$opponent_initials = initials($name_for_initials);
		
	
	$recent_round_html .= <<<EOHTML
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title" href="#section_$brogey_id">
		        		<div class="leaderboard_position_column">$position</div>
				<div class="leaderboard_name_column">$display_name</div>
				<div class="leaderboard_score_column">$score</div>
				<div class="leaderboard_points_column">$leader_points</div>
		        </a>
		        
		        <div id="section_$brogey_id" class="leaderboard_section_content">
	        		<div class="extra_info_title"><span class="match_round_info">Round $round_number Match Result:</span></br>
	        				<span>$display_name vs. $opponent_name</span>
	        		</div>
	        		<table class="match_scoreboard">
	        			<td>$match_score</td>
	        			<td>to</td>
	        			<td>$opp_points</td>
	        		</table>
	        		<div class="match_scorecard">
	        		 
					<span class="match_result_scorecard full_name">
						<div class="match_result_row full_name">Match Hole</div>
						<div class="match_result_row full_name">$display_name</div>
						<div class="match_result_row full_name">$opponent_name</div>
						<div class="match_result_row full_name">Points Won</div>
					</span>
					<span class="match_result_scorecard initials">
						<div class="match_result_row initials">Hole</div>
						<div class="match_result_row initials">$user_initials</div>
						<div class="match_result_row initials">$opponent_initials</div>
						<div class="match_result_row initials">Points</div>
					</span>
					
EOHTML;
	
	foreach( $skin_holes as $skin_hole ){
		
		$hole		= $skin_hole['hole_id'];
		$course_hole	= $skin_hole['course_hole'];
		$score		= $skin_hole['score'];	
		$op_course_hole	= $skin_hole['op_course_hole'];
		$op_score	= $skin_hole['op_score'];
		$points_won	= $skin_hole['points_won'];
		
		if( $points_won > 0 ){
		
			$points_won_class = "points_won";
		
		}else{
			
			$points_won_class = "";
			
		}
		
		if( $hole > 9 ){
			
			$match_front_back = "match_back";
		
		}else{
			
			$match_front_back = "match_front";
		
		}	
		
		
		$recent_round_html .= <<<EOHTML
					
					<span class="match_result_scorecard match_hole $match_front_back">
						<div class="match_result_row $points_won_class $match_front_back">$hole</div>
						<div class="match_result_row $points_won_class $match_front_back">$score</div>
						<div class="match_result_row $points_won_class $match_front_back">$op_score</div>
						<div class="match_result_row $points_won_class $match_front_back">$points_won</div>
					</span>
EOHTML;
	
	}
	
	$recent_round_html .= <<<EOHTML
					<span class="match_result_scorecard">
						<div class="match_result_row row_end">Total</div>
						<div class="match_result_row row_end">|</div>
						<div class="match_result_row row_end">|</div>
						<div class="match_result_row row_end">$match_score</div>
					</span>
				    
				</div>
				<table class="match_result">
					<td>$display_match_result +$match_points pts</td>
				</table>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		

EOHTML;


$skin_holes = "";


}





?>



<!doctype html>

<html>

<head>

	<title>Brogey Golf</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Calligraffitti" />
	
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

<div data-role="page" id="display_most_recent_round">
	
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
	
	
	<div class="panel right" data-role="panel" data-position="right" id="league_leaderboard">
	
		<?php echo $results; ?>
	
	
	</div><!-- /div data-role="panel" id="league_leaderboard"-->

	<div data-role="header" data-position="fixed">
		<div class="site_title">
			<a id="menu_button" href="#brogey_menu">
				<div class="menu_button">
					<span id="menu_button">menu</span>
				</div>
			</a>
			<a id="home_page_link" href="brogeygolf.com" data-ajax="false">	
				<img id="home_page_icon" src="/images/9349HomeButton.png" style="width:45px;height:34px">
			</a>	
			<a id="leaderboard_button" href="#league_leaderboard">
				<div id="site_title">
					<span id="site_title_medium">Tap for Season Leaderboard</span>
				</div>
			</a>
		</div>
	</div><!-- /header -->

	
	



	<div data-role="content" class="most_recent_round_content">
		
		
		<div class="extra_league_info">Holes: <?php echo $league_holes; ?>
			</br>Hcap: <?php echo $display_handicap; ?>
			</br>Round: <?php echo $round_number; ?>
			</br><span class="extra_league_info_end_date_title">Round Ended</span>
			</br><span class="extra_league_info_end_date"><?php echo $round_end_date; ?></span>
		</div>
		<div class="round_result_title">
			<span class="league_name_display_title">
				<?php echo $league_name; ?>
			</span>
			</br>Round <?php echo $round_number; ?> Results, ended <?php echo $round_end_date; ?>
		</div>
	
		<?php echo $recent_round_html; ?>
		</br>
		
		
		<button class="circle_menu_button">+</button>
		<div class="circle_menu_wrapper">
			<ul>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="display_league_info.php" data-ajax="false">League</br>Info</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="display_previous_rounds.php" data-ajax="false">Rounds</br>List</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="display_most_recent_round.php" data-ajax="false">Previous</br>Round</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="display_round_in_progress.php" data-ajax="false">Current</br>Round</a>
				</li>
			</ul>
		</div>
		
		<!-- <div class="swipe_instructions">In Progress <span class="swipe_arrow">&#x2329;&emsp;</span>Swipe<span class="swipe_arrow">&emsp;&#x232a;</span> Past Rounds</div> -->
		
		
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


$.get( "league_invite_notification.php", function( data ) {
  
  $("#invite_notifications").html(data);
  
});




$(document).ready(function() {
    function close_accordion_row() {
        $('.leaderboard_accordion .leaderboard_section_title').removeClass('active');
        $('.leaderboard_accordion .leaderboard_section_content').slideUp(300).removeClass('open');
        $('.circle_menu_button').removeClass('circle_menu_open_section');
    }
 
    $('.leaderboard_section_title').click(function(event) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');
 	
 	var currentClass = $(this).attr('class');
 	
        if( $(this).hasClass("active") ) {
            close_accordion_row();
            $(".match_front").show();
            $(".match_front").css("width", "100%");
	    $(".match_back").hide();
	    $(".match_back").css("width", "0%");
        }else {
            close_accordion_row();
 
            // Add active class to section title
            $(this).addClass('active');
            // Open up the hidden content panel
            $('.leaderboard_accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
            $(".match_front").show();
            $(".match_front").css("width", "100%");
	    $(".match_back").hide();
	    $(".match_back").css("width", "0%");
	    $('.circle_menu_button').addClass('circle_menu_open_section');
        }
 
        event.preventDefault();
    });
});


$( ".round_result_title" ).on( "click",  function() {
	
	if( $( ".extra_league_info" ).css("display") == "none" ){ 
 	
 			
  		$( ".extra_league_info" ).show();
  		
  	}else{
  	
  		$( ".extra_league_info" ).hide();
  		
  	}
  	
});



$( ".match_result_scorecard" ).on( "click",  function() {
	
	
		
	if( $(".match_back").css("display") == "none" ){
		
		$(".match_back").show();
		$(".match_back").animate( {width: "100%"}, 800);
		$(".match_front").hide();
		$(".match_front").animate( {width: "0%"}, 800);
		
		
	}else{
	
		$(".match_front").show();
		$(".match_front").animate( {width: "100%"}, 800);
		$(".match_back").hide();
		$(".match_back").animate( {width: "0%"}, 800);
		
	}
	
			
});


$(".circle_menu_button").on( "click", function() {
	
	if( $(".circle_menu_wrapper").hasClass('opened-nav') ){
		
		$(".circle_menu_wrapper").removeClass('opened-nav');
		$(".circle_menu_button").html('+');
		
	}else{
	
		$(".circle_menu_wrapper").addClass('opened-nav');
		$(".circle_menu_button").html('-');
	
	}
	
});



</script>





</body>

</html>