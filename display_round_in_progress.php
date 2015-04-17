<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


date_default_timezone_set('America/New_York');

$user_id   = $_SESSION['id'];
$league_id = $_GET['leagueid'];

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);


if( $_GET['leagueid'] ){

	$league_id = $_GET['leagueid'];
	$_SESSION['league_id'] = $league_id;
	
}


if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}


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

if( $round_number == 0 ){
	
	$round_number = 1;
	$display_round_number = 1;

}

$weeks_to_modify = $display_round_number * $frequency;

$round_end_date_raw = date_modify($start_date_for_round_number_raw, '+'.$weeks_to_modify.' week');

$round_end_date = date_format($round_end_date_raw, 'M d, Y');

if( $use_handicap == 1 ){
	
	$display_handicap = "Yes";
	
}else{
	
	$display_handicap = "No";

}




include "display_leaderboard.php";

$results = create_leaderboard($league_id, $user_id, $historical);




include "calculate_select_best_score_for_current_round.php";

$matches_chosen = choose_best_round($league_id, $link, $round_number);

usort( $matches_chosen, function($b, $a) {
    return $b['score'] > $a['score'];
} );




$recent_round_html .= <<<EOHTML
	
	
	    <div id="leaderboard_column_titles">
		<span class="leaderboard_position_column_title">#</span>
		<span class="leaderboard_name_column_title">Brogey</span>
		<span class="leaderboard_score_column_title">Score</span>
		<span class="leaderboard_hcap_column_title">Hcap</span>
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

	
	



	<div data-role="content" class="current_round_content">
		
		<div class="extra_league_info">Holes: <?php echo $league_holes; ?>
			</br>Hcap: <?php echo $display_handicap; ?>
			</br>Round: <?php echo $display_round_number; ?>
			</br><span class="extra_league_info_end_date_title">Round Ends</span>
			</br><span class="extra_league_info_end_date"><?php echo $round_end_date; ?></span>
		</div>
		
		<div class="round_result_title">
			<span class="league_name_display_title">
				<?php echo $league_name; ?>
			</span>
			</br>Current Round ends <?php echo $round_end_date; ?> 
		</div>
		<?php echo $recent_round_html; ?>
		
		<!--<div class="league_name_watermark"><?php echo $league_name; ?></div>-->
		
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
	
		
		<!-- <div class="swipe_instructions">Swipe<span class="swipe_arrow">&emsp;&#x232a;</span> Most Recent Round</div> -->
	
		
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
        }else {
            close_accordion_row();
            
 
            // Add active class to section title
            $(this).addClass('active');
            // Open up the hidden content panel
            $('.leaderboard_accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
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