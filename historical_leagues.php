<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

date_default_timezone_set('America/New_York');

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);

$user_id = $_SESSION['id'];


$user_name_query = mysqli_query( $link, "SELECT full_name "
					."FROM bg_app_users "
					."WHERE user_id = '$user_id'");

$user_name_row = mysqli_fetch_row( $user_name_query );

$user_name = $user_name_row[0];	





$new_today = new DateTime('now');

$display_date_raw = $new_today;
	
$display_date = date_format($display_date_raw, 'M d, Y');


$users_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants AS P "
					   ."LEFT JOIN bg_app_league AS L ON P.league_id = L.league_id "
					   ."WHERE P.user_id = '$user_id' "
					   ."AND P.is_confirmed = '1' " 
					   ."AND L.is_complete = '1' "
					   ."ORDER BY L.end_date DESC" );
					   
$users_leagues = array();

while( $users_leagues_row = mysqli_fetch_array( $users_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $users_leagues, $users_leagues_row );
	
}

if( $users_leagues ){
				   
	foreach( $users_leagues as $user_league ){
		
		$user_position 	= "--";
		$user_points 	= "--";
		$league_name 	= $user_league['league_name'];
		$league_id	= $user_league['league_id'];
		$start_date	= $user_league['start_date'];
		$end_date	= $user_league['end_date'];
		$frequency	= $user_league['frequency_by_weeks'];
		$weeks		= $user_league['weeks'];
		
		$display_start_date_raw = new DateTime($start_date);
		
		$display_start_date = date_format($display_start_date_raw, 'M d, Y');
		
		$display_end_date_raw = new DateTime($end_date);
	
		$display_end_date = date_format($display_end_date_raw, 'M d, Y');
		
		
		$round_number = $weeks / $frequency;
		
		
	
		$most_recent_round_info_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
								    ."WHERE league_id = '$league_id' "
								    ."AND round_number = '$round_number' "
								    ."ORDER BY total_points DESC" );
								    
		$most_recent_round_info = array();
		
		while( $most_recent_round_info_row = mysqli_fetch_array( $most_recent_round_info_query, MYSQLI_ASSOC ) ){
		
			array_push( $most_recent_round_info, $most_recent_round_info_row );
			
		}
		
		
		$champion = $most_recent_round_info[0]['display_name'];
		
		
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
		
	
		$league_tab_html .= <<<EOHTML
		
				
			<table class="user_leagues" id="$league_id">
					
				<tr>
			    		<th class="user_leagues_row league_name">$league_name</th>
			    		<th class="user_leagues_row rank">Rank</th>
			    		<th class="user_leagues_row points">Points</th>
			    		<th class="user_leagues_row direction_arrow">&#x232a;</th>
			    	</tr>
						
				<tr class="user_leagues_row">
					<td class="user_leagues_row league_name  historical_league_row">
						<span id="list_date">$display_start_date - $display_end_date</span></br>
						<span id="list_date">Champion: $champion</span>
					</td>
					<td class="user_leagues_row rank  historical_league_row">$user_position</td>
					<td class="user_leagues_row points  historical_league_row">$user_points</td>
					<td class="user_leagues_row direction_arrow">&#x232a;</td>
				</tr>
			</table>

EOHTML;


	}
	
}else{

	$league_tab_html .= <<<EOHTML
		
		<div class="no_league_invites_watermark">No finished leagues, I guess you&apos;re undefeated!</div>

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

<body> 

<div data-role="page" id="historical_leagues">
	
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
					<span id="site_title"><?php echo $display_date; ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="home_page_content">
	
		
	
		<?php echo $league_tab_html; ?>
			
		
	
		
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
	
	$(location).attr( 'href', 'historical_leagues_round_list.php?leagueid='+leagueid );

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

	

/*
$.get( "cron_finalize_league.php", function( data ) {
  
  $("#test").html(data);
  
});
*/


</script>





</body>

</html>