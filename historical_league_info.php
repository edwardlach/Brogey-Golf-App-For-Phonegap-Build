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
$league_id = $_GET['leagueid'];

if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}


$league_name_query = mysqli_query( $link, "SELECT league_name FROM bg_app_league WHERE league_id = '$league_id'" );
$league_name_row = mysqli_fetch_row( $league_name_query );
$league_name = $league_name_row[0];



include "display_leaderboard.php";

$results = create_leaderboard($league_id, $user_id, $historical);


$league_info_display_round_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
$league_info_display_round = array();
while( $league_info_display_round_row = mysqli_fetch_array( $league_info_display_round_query, MYSQLI_ASSOC ) ){
	array_push($league_info_display_round, $league_info_display_round_row );
}	

foreach($league_info_display_round as $league) {

	$league_name			= $league['league_name'];
	$league_holes			= $league['holes'];
	$start_date_for_round_number	= $league['start_date'];
	$end_date_for_round_number	= $league['end_date'];
	$use_handicap			= $league['use_handicaps'];
	$frequency			= $league['frequency_by_weeks'];
	
}

if( $frequency == 1 ){
	
	$frequency_grammar = "week";

}else{
	
	$frequency_grammar = $frequency." weeks";

}

$start_date_raw = new DateTime($start_date_for_round_number);
$end_date_raw = new DateTime($end_date_for_round_number);

$display_start_date = date_format($start_date_raw, 'M d, Y');
$display_end_date = date_format($end_date_raw, 'M d, Y');

if( $use_handicap == 1 ){
	
	$display_handicap = "Yes";
	
}else{
	
	$display_handicap = "No";

}

$match_schedule_html .= <<<EOHTML
	
		<div class="match_league_info">$display_start_date - $display_end_date</div>
		<div class="match_league_info">Holes: $league_holes</div>
		<div class="match_league_info">Rounds due every $frequency_grammar</div>
		<div class="match_league_info match_league_hcap">Handicaps: $display_handicap</div>
	

EOHTML;



$league_matchups_query = mysqli_query( $link, "SELECT * FROM bg_app_match_schedule "
					     ."WHERE league_id = '$league_id' "
					     ."ORDER BY round_number ASC " );
$league_matchups = array();
while( $league_matchups_row = mysqli_fetch_array( $league_matchups_query, MYSQLI_ASSOC ) ){
	array_push($league_matchups, $league_matchups_row );
}						     


$match_schedule_html .= <<<EOHTML
	
	<div class="round_result_title match_play_title">Match Play Schedule</div>
	<table class="match_schedule_table">
		<tr>	
			<th>Round</th>
			<th>Home</th>
			<th>Away</th>
		</tr>

EOHTML;

					     
foreach( $league_matchups as $league_matchup ){

	$home_id 	= $league_matchup['home_id'];
	$away_id 	= $league_matchup['away_id'];
	$round		= $league_matchup['round_number'];

	$date_adjust = $round * $frequency;
	$date_start =  ($round - 1) * $frequency;
	
	if( $round == 1 ){
		
		$display_round_start_raw = date_modify($start_date_raw, '+'.$date_start.' week');
		$display_round_start = date_format($display_round_start_raw, 'M d, Y');
		$display_round_end_raw = date_modify($start_date_raw, '+'.$date_adjust.' week');
		$display_round_end = date_format($display_round_end_raw, 'M d, Y');
	
	}else{
	
		$display_round_start = $display_round_end;
		$display_round_end_dateTime = new DateTime($display_round_end);
		$display_round_end_raw = date_modify($display_round_end_dateTime, '+'.$frequency.' week');
		$display_round_end = date_format($display_round_end_raw, 'M d, Y');
	
	}
	
	if( $home_id == $user_id ){
		
		$home = $home_id;
		$away = $away_id;
	
	}elseif( $away_id == $user_id ){
		
		$home = $home_id;
		$away = $away_id;
	
	}
	
	if( $home ){
		
		$home_display_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$home'" );
		$home_display_row = mysqli_fetch_row( $home_display_query );
		$home_display = $home_display_row[0];
	
	}else{
		
		$home_display = "Bye";
	
	}
	
	if( $away ){
		
		$away_display_query = mysqli_query( $link, "SELECT full_name FROM bg_app_users WHERE user_id = '$away'" );
		$away_display_row = mysqli_fetch_row( $away_display_query );
		$away_display = $away_display_row[0];
	
	}else{
		
		$away_display = "Bye";
	
	}
	
	if( $home_id == $user_id ){
		
		$user_matchup = "user_matchup";
	
	}elseif( $away_id == $user_id ){
		
		$user_matchup = "user_matchup";
	
	}else{
		
		$user_matchup = "";
	
	}

	$match_schedule_html .= <<<EOHTML
		
		<tr class="match_row $user_matchup">
			<td class="match_round">$round</td>
			<td class="match_home">$home_display</td>
			<td class="match_away">$away_display</td>
		</tr>

EOHTML;



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

<div data-role="page" id="display_previous_round">
	
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

	
	



	<div data-role="content" class="previous_round_content">
		
		<div class="round_result_title">
			<span class="league_name_display_title">
				<?php echo $league_name; ?>
			</span>
			</br>League Info
		</div>
		
		
		<?php echo $match_schedule_html; ?>
		
		
		<button class="circle_menu_button">+</button>
		<div class="circle_menu_wrapper">
			<ul>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="historical_leagues.php" data-ajax="false">League</br>List</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="historical_leagues_round_list.php" data-ajax="false">Round</br>List</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="historical_leagues_previous_round.php" data-ajax="false">Previous</br>Round</a>
				</li>
				<li class="circle_menu_item circle_menu_action">
					<a class="circle_menu_a circle_menu_action" href="historical_league_info.php" data-ajax="false">League</br>Info</a>
				</li>
			</ul>
		</div>
		
		<!-- <div class="swipe_instructions">Most Recent Round <span class="swipe_arrow">&#x2329;&emsp;</span>Swipe</div> -->
	
		
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



$('.previous_round_list_row').on( 'click', function(){

	var previousRound = $(this).attr('id');
	
	$(location).attr( 'href', 'display_most_recent_round.php?previousround='+previousRound );

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