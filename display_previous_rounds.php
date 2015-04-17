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


$previous_round_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
					    ."WHERE user_id = '$user_id' "
					    ."AND league_id = '$league_id' "
					    ."ORDER BY round_number ASC" );

$previous_round = array();

while( $previous_round_row = mysqli_fetch_array( $previous_round_query, MYSQLI_ASSOC ) ){

	array_push( $previous_round, $previous_round_row );
	
}


$previous_round_html = <<<EOHTML
<table  id="previous_round_list" >
<tbody>
    	<tr class="previous_round_list_row_title">
    		<th class="list_round">Round</th>
    		<th class="list_position">Position</th>
    		<th class="list_total_points">Points</th>
    		<th class="direction_arrow">&#x232a;</th>
    	</tr>
   
EOHTML;

foreach( $previous_round as $row ){

	$round		= $row['round_number'];
	$position	= $row['position'];
	$match		= $row['points_from_match'];
	$leaderboard 	= $row['points_from_leaderboard'];
	$total_points	= $match + $leaderboard;
	
	$previous_round_html .= <<<EOHTML
	<tr class="previous_round_list_row" id="$round">
		<td class="list_round">$round</td>
		<td class="list_position">$position</td>
		<td class="list_total_points">$total_points</td>
		<td class="direction_arrow">&#x232a;</td>	
	</tr>
EOHTML;




}

$previous_round_html .= <<<EOHTML

</tbody>
</table>

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
			</br>Rounds List
		</div>
		
		<?php echo $previous_round_html; ?>
		</br>
		
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