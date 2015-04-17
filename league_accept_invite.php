 <?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

date_default_timezone_set('America/New_York');

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);


$display_date_raw = new DateTime("now");
	
$display_date = date_format($display_date_raw, 'M d, Y');







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

</head>

<body> 

<div data-role="page" id="league_accept_invite">
	
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
	
	



	<div data-role="content" class="accept_invite_list_content">
	
		<div id="accept_invite_html"></div>
		
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


$.get( "league_accept_invite_ajax_code.php", function( data ) {
  
  $("#accept_invite_html").html(data);
  
});


$('#accept_invite_html').on( 'swiperight', '.league_invite_list_row', function(){
	
	var leagueid = $(this).attr('id');
	var accept = 1;
	
	$(this).css('background-color', '#9DFF9D');
	
	if (confirm('Quick double check that you meant to join this league, why wouldn\'t you!')) {
		
		$.post("league_accept_invite_update_database.php",
		  {
		    leagueid:leagueid,
		    accept:accept
		  },
		  function(data,status){
			
			$.get( "league_accept_invite_ajax_code.php", function( data ) {
	  
			  $("#accept_invite_html").html(data);
			  
			});
			
			$.get( "league_invite_notification.php", function( data ) {
	  
			  $("#invite_notifications").html(data);
			  
			});
		 		
		  });
	
	}else{
	
		$(this).css('background-color', 'transparent');
	
	}

});


$('#accept_invite_html').on( 'swipeleft', '.league_invite_list_row', function(){
	
	var leagueid = $(this).attr('id');
	var accept = 2;
	
	$(this).css('background-color', '#FDE4E1');
	
	if (confirm('You\'re going to turn down this amazing opportunity!')) {
		
		$.post("league_accept_invite_update_database.php",
		  {
		    leagueid:leagueid,
		    accept:accept
		  },
		  function(data,status){
		 
		 	$.get( "league_accept_invite_ajax_code.php", function( data ) {
	  
			  $("#accept_invite_html").html(data);
			  
			});
			
			$.get( "league_invite_notification.php", function( data ) {
	  
			  $("#invite_notifications").html(data);
			  
			});
		 	
		  });
		  
	}else{
	
		$(this).css('background-color', 'transparent');
	
	}
		  
});


</script>





</body>

</html>