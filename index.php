<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

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

$user_id = $_SESSION['id'];


$user_name_query = mysqli_query( $link, "SELECT full_name "
					."FROM bg_app_users "
					."WHERE user_id = '$user_id'");

$user_name_row = mysqli_fetch_row( $user_name_query );

$user_name = $user_name_row[0];	



$scorecard_list_query = "SELECT * FROM `bg_app_rounds` "
			."WHERE `user_id` = '$user_id' "
			."AND `is_complete` = '1' "
			."ORDER BY `start_date` DESC";

$run_scorecard_list_query = mysqli_query( $link, $scorecard_list_query );

$rounds_played = mysqli_num_rows($run_scorecard_list_query);

if($rounds_played < 5 ){
	
	if($rounds_played == 0){
		
		$play_more_rounds_message = <<<EOHTML
			
			<div class="info_message">Sup Brogey, it appears you haven&apos;t submitted any rounds yet.  We highly suggest putting in at least 5 scores before you start league play in order to establish an accurate handicap.</div>
			
EOHTML;
	
	}elseif($rounds_played == 1){
	
		$play_more_rounds_message = <<<EOHTML
			
			<div class="info_message">Sup Brogey, it appears you&apos;ve only played 1 round.  We highly suggest putting in at least 5 scores before you start league play in order to establish an accurate handicap.</div>
			
EOHTML;
	
	}else{
		
		$play_more_rounds_message = <<<EOHTML
			
			<div class="info_message">Sup Brogey, it appears you$apos;ve only played $rounds_played rounds so far.  We highly suggest putting in at least 5 scores before you start league play in order to establish an accurate handicap.</div>
			
EOHTML;
		
	}
	
}

if( $_GET['contactEmail'] ){

	$contactEmail = $_GET['contactEmail'];
	$contact_message_confirm = <<<EOHTML
		
		<div class="info_message">Thank you for your message!  We will respond as quickly as possible.  Please check your $contactEmail email account for our response.</div>
		
EOHTML;
	
}


$new_today = new DateTime('now');

$today_for_query = date_format($new_today, 'Y-m-d');


$users_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants AS P "
					   ."LEFT JOIN bg_app_league AS L ON P.league_id = L.league_id "
					   ."WHERE P.user_id = '$user_id' "
					   ."AND P.is_confirmed = '1' " 
					   ."AND L.is_complete = '0' "
					   ."AND L.start_date <= '$today_for_query' "
					   ."ORDER BY L.start_date ASC" );
					   
$users_leagues = array();

while( $users_leagues_row = mysqli_fetch_array( $users_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $users_leagues, $users_leagues_row );
	
}

if( $users_leagues ){
	
	$num_leagues = mysqli_num_rows( $users_leagues_query );
	
	if( $num_leagues == 1 ){
		
		$league_grammar = "Your League";
	
	}else{
		
		$league_grammar = "Your Leagues";
	
	}
		
	$league_tab_html .= <<<EOHTML
	
		<table id="home_page_league_title">
			<tr>
				<th>$league_grammar:</th>
			</tr>
		</table>
		
EOHTML;

}


$user_league_num = 0;
				   
foreach( $users_leagues as $user_league ){
	
	$user_position 	= "--";
	$user_points 	= "--";
	$league_name 	= $user_league['league_name'];
	$league_id	= $user_league['league_id'];
	$start_date	= $user_league['start_date'];
	$end_date	= $user_league['end_date'];
	$frequency	= $user_league['frequency_by_weeks'];
	
	$display_start_date_raw = new DateTime($start_date);
	
	$display_start_date = date_format($display_start_date_raw, 'M d, Y');
	
	$display_end_date_raw = new DateTime($end_date);

	$display_end_date = date_format($display_end_date_raw, 'M d, Y');
	
	$league_start_date_raw = new DateTime($start_date);
				
	$date_difference = date_diff( $new_today, $league_start_date_raw );

	$difference_in_days = $date_difference->days;
	
	$round_length_in_days = $frequency * 7;
	
	$round_number = floor( $difference_in_days / $round_length_in_days );
	
	if ( $display_start_date_raw <= $new_today ){
	
		$display_current_round = $round_number + 1;
	
	}else{
		
		$display_current_round = $round_number;
		
	}
		

	$most_recent_round_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
							    ."WHERE league_id = '$league_id' "
							    ."AND round_number = '$round_number' "
							    ."ORDER BY total_points DESC" );
							    
	$most_recent_round_info = array();
	
	while( $most_recent_round_info_row = mysqli_fetch_array( $most_recent_round_info_query, MYSQLI_ASSOC ) ){
	
		array_push( $most_recent_round_info, $most_recent_round_info_row );
		
	}
	
	$position = 1;
	
	foreach( $most_recent_round_info as $round_info ){
	
		$round_user_id = $round_info['user_id'];
		
		if( $round_user_id == $user_id ){
		
			$user_position = $position;
			$user_points = $round_info['total_points'];
			
		}else{
			
			$position = $position + 1;
		
		}	
	
	}
	
	if( $user_position == 1 ){
		$suffix = "st";
	}elseif( $user_position == 2 ){
		$suffix = "nd";
	}elseif( $user_position == 3 ){
		$suffix = "rd";
	}else{
		$suffix = "th";
	}
	
	$user_league_num = $user_league_num + 1;
	
	if( $user_league_num == $num_leagues ){	
		$user_last_league = "user_last_league";
	}else{
		$user_last_league = "";
	} 
	
	
	$league_tab_html .= <<<EOHTML
	
			
		<table class="user_leagues $user_last_league" id="$league_id">
				
			<tr>
		    		<th class="user_leagues_row league_name">$league_name</th>
		    		<th class="user_leagues_row rank">Rank</th>
		    		<th class="user_leagues_row points">Points</th>
		    		<th class="user_leagues_row direction_arrow">&#x232a;</th>
		    	</tr>
					
			<tr class="user_leagues_row">
				<td class="user_leagues_row league_name">
					<span id="list_date">$display_start_date - $display_end_date</span></br>
					<span id="list_date">Current Round: $display_current_round</span>
				</td>
				<td class="user_leagues_row rank">$user_position</td>
				<td class="user_leagues_row points">$user_points</td>
				<td class="user_leagues_row direction_arrow"><i class="fa fa-chevron-right"></i></td>
			</tr>
		</table>

EOHTML;

	$user_last_league = "";

}

$users_future_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants AS P "
					   ."LEFT JOIN bg_app_league AS L ON P.league_id = L.league_id "
					   ."WHERE P.user_id = '$user_id' "
					   ."AND P.is_confirmed = '1' " 
					   ."AND L.is_complete = '0' "
					   ."AND L.start_date > '$today_for_query' "
					   ."AND L.league_admin_id != '$user_id' "
					   ."ORDER BY L.start_date ASC" );
					   
$users_future_leagues = array();

while( $users_future_leagues_row = mysqli_fetch_array( $users_future_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $users_future_leagues, $users_future_leagues_row );
	
}

if( $users_future_leagues ){

	$num_future_leagues = mysqli_num_rows( $users_future_leagues_query );
	
	if( $num_future_leagues == 1 ){
		
		$future_league_grammar = "League";
	
	}else{
		
		$future_league_grammar = "Leagues";
	
	}
	
	$future_league_tab_html .= <<<EOHTML
	
		<table id="home_page_league_title">
			<tr>
				<th>Your Upcoming $future_league_grammar:</th>
			</tr>
		</table>
		
EOHTML;

}

$user_future_league_num = 0;

				   
foreach( $users_future_leagues as $user_future_league ){


	$future_league_name 	= $user_future_league['league_name'];
	$future_league_id	= $user_future_league['league_id'];
	$future_start_date	= $user_future_league['start_date'];
	$future_end_date	= $user_future_league['end_date'];
	$future_frequency	= $user_future_league['frequency_by_weeks'];
	$future_handicaps	= $user_future_league['use_handicaps'];
	$future_weeks		= $user_future_league['weeks'];
	$future_holes		= $user_future_league['holes'];

	$display_future_start_date_raw = new DateTime($future_start_date);
	
	$display_future_start_date = date_format($display_future_start_date_raw, 'M d, Y');
	
	$display_future_end_date_raw = new DateTime($future_end_date);

	$display_future_end_date = date_format($display_future_end_date_raw, 'M d, Y');
	
	$user_future_league_num = $user_future_league_num + 1;
	
	if( $user_future_league_num == $num_future_leagues ){	
		$user_last_league = "user_last_league";
	}else{
		$user_last_league = "";
	} 

	$future_league_tab_html .= <<<EOHTML
	
			
		<table class="user_future_leagues user_league $user_last_league" id="$future_league_id">
			
			<td class="user_future_leagues_row league_name user">$future_league_name</br>
				<span id="list_date">
					<span id="start_date">$display_future_start_date</span> - $display_future_end_date
				</span>
			</td>
			
			<td class="user_future_leagues_row direction_arrow user"><i class="fa fa-chevron-right"></i></td>	
		</table>

EOHTML;

	$user_last_league = "";

}



$user_admin_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league "
						."WHERE league_admin_id = '$user_id' "
						."AND start_date > '$today_for_query' "
						."AND is_complete = '0'" );

$user_admin_leagues = array();

while( $user_admin_leagues_row = mysqli_fetch_array( $user_admin_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $user_admin_leagues, $user_admin_leagues_row );
	
}

if( $user_admin_leagues ){

	$num_admin_leagues = mysqli_num_rows( $user_admin_leagues_query );
	
	if( $num_admin_leagues == 1 ){
		
		$admin_league_grammar = "League";
	
	}else{
		
		$admin_league_grammar = "Leagues";
	
	}
	
	$admin_league_tab_html .= <<<EOHTML
	
		<table id="home_page_league_title">
			<tr>
				<th>Your Upcoming Admin $admin_league_grammar:</th>
			</tr>
		</table>
		
EOHTML;

}

$user_admin_league_num = 0;

foreach( $user_admin_leagues as $user_admin_league ){


	$admin_league_name 	= $user_admin_league['league_name'];
	$admin_league_id	= $user_admin_league['league_id'];
	$admin_start_date	= $user_admin_league['start_date'];
	$admin_end_date		= $user_admin_league['end_date'];
	$admin_frequency	= $user_admin_league['frequency_by_weeks'];
	$admin_handicaps	= $user_admin_league['use_handicaps'];
	$admin_weeks		= $user_admin_league['weeks'];
	$admin_holes		= $user_admin_league['holes'];

	$display_admin_start_date_raw = new DateTime($admin_start_date);
	
	$display_admin_start_date = date_format($display_admin_start_date_raw, 'M d, Y');
	
	$display_admin_end_date_raw = new DateTime($admin_end_date);

	$display_admin_end_date = date_format($display_admin_end_date_raw, 'M d, Y');
	
	$user_admin_league_num = $user_admin_league_num + 1;
	
	if( $user_admin_league_num == $num_admin_leagues ){	
		$user_last_league = "user_last_league";
	}else{
		$user_last_league = "";
	} 

	$admin_league_tab_html .= <<<EOHTML
	
			
		<table class="user_future_leagues admin_league $user_last_league">
			<td class="settings_icon_column">
				<i class="fa fa-cog"></i>
			</td>
			<td class="user_future_leagues_row league_name admin" id="$admin_league_id">$admin_league_name</br>
				<span id="list_date">
					<span id="start_date">$display_admin_start_date</span> - $display_admin_end_date</br>
					<span id="start_date">Click to add League Members!</span>
				</span>
			</td>	
			<td class="user_future_leagues_row direction_arrow admin"><i class="fa fa-chevron-right"></i></td>	
		</table>

EOHTML;

	$user_last_league = "";


}





?>



<!doctype html>

<html class="home_page">

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
		
		
		$( document ).ready(function() {

			$('.new_round_button').insertBefore('.top_wood_panel');
			
			$('.scorecard_button').insertBefore('.top_wood_panel');
			
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

<body class="home_page"> 

<div data-role="page" id="home_page">
	
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
			
			<a id="home_page_link" href="brogeygolf.com" data-ajax="false">	
				<img id="home_page_icon" src="/images/9349HomeButton.png" style="width:45px;height:34px">
			</a>
			
			<img src="/images/neon_light_logo.png" class="header_logo" style="width:280px;height:39px">
			
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="home_page_content">
	
		<div id="test"></div>	
		<?php if( $contact_message_confirm ){ echo $contact_message_confirm; } ?>
		<?php if( $play_more_rounds_message ){ echo $play_more_rounds_message; } ?>
		<div class="home_page_buttons">
			
			<a href="round_info_input.php" data-role="button" data-ajax="false" id="new_round_button">New Round</a>
			<a href="scorecard_list.php" data-role="button" data-ajax="false" id="scorecard_button">Handicap:
				<?php
					include "calculate_handicap.php";
					$handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap);
					echo $handicap;
				?>
			</a>
		</div>
	
		<div id="home_page_league_tabs">
			<?php echo $league_tab_html; ?>
			<?php echo $admin_league_tab_html; ?>
			<?php echo $future_league_tab_html; ?>
		</div>
		<!--
		<div id="home_page_logo_div">
			<img src="/images/neon_light_logo.png" id="login_logo_homepage">
		</div>
		-->
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




$('.user_leagues').on( 'click', function(){

	var leagueid = $(this).attr('id');
	
	$.post("display_just_finished_round_ajax.php",
	  {
	    leagueid:leagueid
	  },
	  function(data,status){
	  	
	  	if( data == 0 ){
	  		
	  		$(location).attr( 'href', 'display_round_in_progress.php?leagueid='+leagueid );
	  	
	  	}else{
	  	
	  		$(location).attr( 'href', 'display_most_recent_round.php?leagueid='+leagueid );
	  	
	  	}
	  
	  });
	
	
	

});


$('.user_future_leagues_row.league_name.admin').on( 'click', function(){

	var leagueadminid = $(this).attr('id');
	
	$(location).attr( 'href', 'league_add_member.php?leagueadminid='+leagueadminid );

});


$('.user_league').on( 'click', function(){

	var futureleagueid = $(this).attr('id');
	
	$(location).attr( 'href', 'future_league_roster.php?futureleagueid='+futureleagueid );

});


$('.settings_icon_column').on('click', function(){
	
	$(location).attr( 'href', 'league_edit_settings.php' );

});


/*
$.get( "scorecard_list_find_chart_data.php", function( data ) {
  
  $("#test").html(data);
  
});
*/


</script>





</body>

</html>