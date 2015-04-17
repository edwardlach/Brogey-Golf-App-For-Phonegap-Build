<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

date_default_timezone_set('America/New_York');

$display_date_raw = new DateTime("now");
	
$display_date = date_format($display_date_raw, 'M d, Y');

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);


$league_id = $_GET['futureleagueid'];


$league_name_query = "SELECT `league_name` FROM `bg_app_league` WHERE `league_id` = '$league_id'";

$run_league_name_query = mysqli_query( $link, $league_name_query );

$league_name_row = mysqli_fetch_row($run_league_name_query);
	
$league_name = $league_name_row[0];



$league_start_date_query = mysqli_query( $link, "SELECT start_date FROM bg_app_league "
				   		."WHERE league_id = '$league_id'" );
$league_start_date_row = mysqli_fetch_row($league_start_date_query);

$league_start_date = $league_start_date_row[0];

$display_league_start_date_raw = new DateTime($league_start_date);
	
$display_league_start_date = date_format($display_league_start_date_raw, 'M d, Y');



$current_members_query = "SELECT * FROM `bg_app_league_participants` WHERE `league_id` = '$league_id'";

$run_current_members_query = mysqli_query( $link, $current_members_query );


$current_members = array();

while( $current_members_row = mysqli_fetch_array( $run_current_members_query, MYSQLI_ASSOC ) ){

	array_push( $current_members, $current_members_row );
	
}
	


$add_member_html = <<<EOHTML

	<div class="league_start_date_notification">League Starts $display_league_start_date</div>
	<table id="current_members">
		<tbody>
		    	<tr class="current_members_list_row_title">
		    		<th class="list_member_name">Member Name</th>
		    		<th class="list_status">Status</th>
		    	</tr>
EOHTML;

foreach( $current_members as $row ){

	$user_id 	= $row['user_id'];
	$is_confirmed 	= $row['is_confirmed'];
	
	
	$user_name_query 	= "SELECT `full_name` FROM `bg_app_users` WHERE `user_id` = '$user_id'";
	$run_user_name_query	= mysqli_query( $link, $user_name_query );
	$user_name_row 		= mysqli_fetch_row( $run_user_name_query );
	
	
	$user_name = $user_name_row[0];

	
	if( $is_confirmed == '0' ){		
		$status = "pending";
	}else{
		$status = "joined";
	}
	
	
	$add_member_html .= <<<EOHTML
	
		<tr class="current_members_list_row">
			<td class="list_member_name">$user_name</td>
			<td class="list_status">$status</td>	
		</tr>
EOHTML;
}

$add_member_html .= <<<EOHTML
	
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

<div data-role="page" id="add_member">
	
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
					<span id="site_title"><?php echo $league_name; ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	



	<div data-role="content" class="league_admin_list_content" >
		
	
		<?php echo $add_member_html; ?>
	
		
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



</script>





</body>

</html>