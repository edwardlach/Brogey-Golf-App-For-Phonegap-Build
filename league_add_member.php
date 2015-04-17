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


$query_user_id = mysqli_real_escape_string( $link, $_SESSION['id'] );

$new_today = new DateTime('now');
	
$today = date_format($new_today, 'Y-m-d');


$user_admin_leagues_query = "SELECT * FROM `bg_app_league` WHERE `league_admin_id` = '$query_user_id' AND `start_date` > '$today' AND `is_complete` = '0'";

$run_user_admin_leagues_query = mysqli_query( $link, $user_admin_leagues_query );

$user_admin_leagues = array();

while( $user_admin_leagues_row = mysqli_fetch_array( $run_user_admin_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $user_admin_leagues, $user_admin_leagues_row );
	
}

$number_of_leagues = mysqli_num_rows( $run_user_admin_leagues_query );

if( $number_of_leagues == 1 ){
	
	foreach( $user_admin_leagues as $lone_row ){
	
		$_SESSION['league_id_admin'] = $lone_row['league_id'];
	
	}

}
	


if( $_GET['leagueadminid'] ){
	
	$_SESSION['league_id_admin'] = $_GET['leagueadminid'];
	header( "Location: league_add_member.php" );

	
}


if( $_GET['new'] ){

	$new_league_name = $_SESSION['league_name']; 

	$new_league_html = <<<EOHTML
		
		<div class="info_message">$new_league_name has been created!  Now it&#39;s time to add some members using the tools below.</div>

EOHTML;
		

}



if( !$user_admin_leagues ){

	$not_admin_html = <<<EOHTML
	
		<div class="error_message">You are not the administrator of any leagues that have not already started.  Create a new league as administrator to add members.</div>
		
EOHTML;

}elseif( !$_SESSION['league_id_admin'] ){

	$no_admin_session_html = <<<EOHTML
	
		<table  id="league_admin_list" >
			<tbody>
			    	<tr class="league_admin_list_row_title">
			    		<th class="list_league_name">League Name</th>
			    		<th class="list_season_length">Season Length in Weeks</th>
			    	</tr>
			   
EOHTML;
			
			foreach( $user_admin_leagues as $row ){
			
				$league_name	= $row['league_name'];
				$start_date	= $row['start_date'];
				$end_date	= $row['end_date'];
				$season_length	= $row['weeks'];
				$league_id	= $row['league_id'];
				
				
				$display_start_date_raw = new DateTime($start_date);
	
				$display_start_date = date_format($display_start_date_raw, 'M d, Y');
				
				$display_end_date_raw = new DateTime($end_date);
	
				$display_end_date = date_format($display_end_date_raw, 'M d, Y');
				
				
			
				$no_admin_session_html .= <<<EOHTML
				
					<tr class="league_admin_list_row" id="$league_id">
						<td class="list_league_name">$league_name</br><span id="list_date">$display_start_date - $display_end_date</span></td>
						<td class="list_season_length">$season_length</td>
					</tr>
EOHTML;
			
			}
			
	$no_admin_session_html .= <<<EOHTML
	
			</tbody>
		</table>
EOHTML;


}else{

	$query_league_id = mysqli_real_escape_string( $link, $_SESSION['league_id_admin'] );
	
	
	$league_name_query = "SELECT `league_name` FROM `bg_app_league` WHERE `league_id` = '$query_league_id'";
	
	$run_league_name_query = mysqli_query( $link, $league_name_query );
	
	$league_name_row = mysqli_fetch_row($run_league_name_query);
		
	$league_name = $league_name_row[0];
	

	$current_members_query = "SELECT * FROM `bg_app_league_participants` WHERE `league_id` = '$query_league_id'";
	
	$run_current_members_query = mysqli_query( $link, $current_members_query );
	
	
	$current_members = array();
	
	while( $current_members_row = mysqli_fetch_array( $run_current_members_query, MYSQLI_ASSOC ) ){
	
		array_push( $current_members, $current_members_row );
		
	}
		
	
	
	$add_member_html = <<<EOHTML
		<div class="search_current_member_title">Search Brogeys</div>
		<form id="search_members_form" method="post">
			<input type="text" data-ajax="false" name="search_members" id="search_members" value="" autocomplete="off" placeholder="Search for Brogeys:"/>
			<div id="member_search_results"></div>
		</form>
		
		<form class="change_league" method="post">
			<a id="invite_brogey_button" href="email_invite.php?leagueid=$query_league_id">Invite New Brogey</a>
		</form>
		
		<div id="admin_league_roster_title">$league_name Roster:</div>
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


	
	if( $number_of_leagues > 1 ){
		
		$other_admin_leagues_html = <<<EOHTML
		
			<form action="league_add_member.php" class="change_league" method="post">
					<input type="submit" name="change_league" id="change_league" value="Change League" />
			</form>
EOHTML;

	
	}
	

}


function record($new_members, $link){

	$new_member_league_id = mysqli_real_escape_string( $link, $_SESSION['league_id_admin'] );
	
	$_SESSION['already_invited'] = array();
	
	foreach( $new_members as $new_member ){
	
		$member_id = $new_member;
		
		$add_new_member_query = "INSERT INTO `bg_app_league_participants`(`league_id`, `user_id`, `is_confirmed`) "
					."VALUES('$new_member_league_id', '$member_id', '0')";
		
		
		$already_invited_query = "SELECT * FROM `bg_app_league_participants` "
					."WHERE `user_id` = '$member_id' "
					."AND `league_id` = '$new_member_league_id'";
					
		$run_already_invited_query = mysqli_query( $link, $already_invited_query );
		
		$already_invted_query_rows = mysqli_num_rows($run_already_invited_query);
		
		if( $already_invted_query_rows ){
		
			$already_invited_name_query = "SELECT `full_name` FROM `bg_app_users` WHERE `user_id` = '$member_id'";
			
			$run_already_invited_name_query = mysqli_query( $link, $already_invited_name_query );
			
			$already_invited_name_row = mysqli_fetch_row($run_already_invited_name_query);
		
			$already_invited_name = $already_invited_name_row[0];
			
			$already_invited_html = <<<EOHTML
			
				<div class="error_message">$already_invited_name has already been invited to your league</div>
			
EOHTML;
		
			array_push( $_SESSION['already_invited'], $already_invited_html);
		
		}else{	
	
			$run_add_new_member_query = mysqli_query( $link, $add_new_member_query );
			
		}
	
	}
	
	header( "Location: league_add_member.php" );

}

if( $_SESSION['already_invited'] ){

	$already_invited = $_SESSION['already_invited'];
	
	unset( $_SESSION['already_invited'] );
	
}



if ( isset( $_POST['add_member_button'] ) ) {
	
	$new_members = $_POST['search_name'];

	if( $new_members ){

		record($new_members, $link);
		
	}

}


if( isset( $_POST['change_league'] ) ) {	

	unset($_SESSION['league_id_admin']);
		
	header( "Location: league_add_member.php" );
		
	
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
					<span id="site_title">
						<?php echo $display_date; ?>
					</span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	



	<div data-role="content" class="league_admin_list_content" >
		
		
		<?php if( $already_invited ){
					
				foreach( $already_invited as $message ){
					
					echo $message;
				
				}
			
			}
			
			if( $new_league_html ){
				
				echo $new_league_html;
			
			}
			
			
		?>
		<?php echo $no_admin_session_html; ?>
		<?php echo $not_admin_html; ?>
		<?php echo $add_member_html; ?>
		<?php echo $other_admin_leagues_html; ?>
		
	
		
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


$('.league_admin_list_row').on( 'click', function(){

	var leagueadminid = $(this).attr('id');
	
	$(location).attr( 'href', 'league_add_member.php?leagueadminid='+leagueadminid );

});

function send_search(){
		
	
}



$(".league_admin_list_content").on( 'keypress', '#search_members', function(){
		
	var search = $(this).val();
	
	var send_search;

	function send_search() {
	
	    send_search = setTimeout(function(){ 
	    
	    	$.post("league_search_member.php",
		  {
		    search:search
		  },
		  function(data,status){
		 
		 	$("#member_search_results").html(data);
		 	
		  });
	    
	    
	    }, 300);
	    
	    $(".league_admin_list_content").on( 'keypress', '#search_members', function(){
	    	
	    	setTimeout( stop_send_search(), 100 );
	    
	    });
	    
	}
	
	function stop_send_search() {
	
	    clearTimeout(send_search);
	    
	}
	
	send_search();
	
});


$.get( "league_invite_notification.php", function( data ) {
  
  $("#invite_notifications").html(data);
  
});



</script>





</body>

</html>