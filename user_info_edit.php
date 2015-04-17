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

$user_info_query = mysqli_query( $link, "SELECT * FROM bg_app_users WHERE user_id = '$user_id'" );

$user_info = array();

while( $user_info_row = mysqli_fetch_array( $user_info_query, MYSQLI_ASSOC ) ){

	array_push( $user_info, $user_info_row );
	
}

foreach( $user_info as $info ){

	$first_name	= $info['first_name'];
	$last_name	= $info['last_name'];
	$email_address	= $info['email_address'];
	$username	= $info['username'];

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

<body class="edit_user_info"> 

<div data-role="page" id="edit_user_info">
	
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
					<span id="site_title_medium">Your Brogey Info!</span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="home_page_content">
	
		<div class="edit_settings_message"></div>
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section user_settings_section">
		        <a class="leaderboard_section_title user_settings" href="#first_name"><i class="fa fa-cog"></i> First Name: <span class="user_settings_content_first_name"><?php echo $first_name; ?></span></a>
		        <div id="first_name" class="leaderboard_section_content edit_user_settings">
		        	
		        	<form> 
			   		<input type="text" name="first_name" class="settings_input" id="settings_first_name" value="<?php echo $first_name; ?>" />
			 
		   			<button class="btn btn-default" id="edit_first_name">Edit</button>				
		   			
				</form>		
				       
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section user_settings_section">
		        <a class="leaderboard_section_title user_settings" href="#last_name"><i class="fa fa-cog"></i> Last Name: <span class="user_settings_content_last_name"><?php echo $last_name; ?></span></a>
		        <div id="last_name" class="leaderboard_section_content edit_user_settings">
		        	
		        	<form> 
			   		<input type="text" name="last_name" class="settings_input" id="settings_last_name" value="<?php echo $last_name; ?>" />
			   		
		   			<button class="btn btn-default" id="edit_last_name">Edit</button>
				</form>	
		        
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section user_settings_section">
		        <a class="leaderboard_section_title user_settings" href="#email_address"><i class="fa fa-cog"></i> Email: <span class="user_settings_content_email_address"><?php echo $email_address; ?></span></a>
		        <div id="email_address" class="leaderboard_section_content edit_user_settings">
		        	
		        	<form> 
			   		<input type="email" name="email_address" class="settings_input" id="settings_email_address" value="<?php echo $email_address; ?>" />
			   		
		   			<button class="btn btn-default" id="edit_email_address">Edit</button>
				</form>	
				
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section user_settings_section">
		        <a class="leaderboard_section_title user_settings" href="#username"><i class="fa fa-cog"></i> Username: <span class="user_settings_content_username"><?php echo $username; ?></span></a>
		        <div id="username" class="leaderboard_section_content edit_user_settings">
		        	
		        	<form> 
			   		<input type="text" name="username" class="settings_input" id="settings_username" value="<?php echo $username; ?>" />
			   		
			   		<button class="btn btn-default" id="edit_username">Edit</button>	
				</form>
				
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		
		
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
        }
 
        event.preventDefault();
    });
});


$('#edit_first_name').on('click', function(){
	
	event.preventDefault();
	
	$('.edit_settings_message').html("");	
	$('.edit_settings_message').removeClass('error_message');
	
	var first_name = $('#settings_first_name').val();
	var last_name = $('#settings_last_name').val();
	var first = 1;
	
	$.post("user_info_edit_ajax.php",
	{
		first_name:first_name,
		last_name:last_name,
		first:first
	},
	function(data,status){
	
		$('.user_settings_content_first_name').html(data);
	
	});
	

});

$('#edit_last_name').on('click', function(){
	
	event.preventDefault();
	
	$('.edit_settings_message').html("");	
	$('.edit_settings_message').removeClass('error_message');
	
	var first_name = $('#settings_first_name').val();
	var last_name = $('#settings_last_name').val();
	var last = 1;
	
	$.post("user_info_edit_ajax.php",
	{
		first_name:first_name,
		last_name:last_name,
		last:last
	},
	function(data,status){
	
		$('.user_settings_content_last_name').html(data);
	
	});

});

$('#edit_email_address').on('click', function(){
	
	event.preventDefault();
	
	$('.edit_settings_message').html("");	
	$('.edit_settings_message').removeClass('error_message');
	
	var email_address = $('#settings_email_address').val();
	
	$.post("user_info_edit_ajax.php",
	{
		email_address:email_address
	},
	function(data,status){
		
		var split_data = data.split("...");
		var content = split_data[0];
		var action = split_data[1];
		
		if( action == "success" ){
		
			$('.user_settings_content_email_address').html(content);
		
		}else{
			
			$('.edit_settings_message').html(content);
			$('.edit_settings_message').addClass('error_message');
		
		}
	
	});

});

$('#edit_username').on('click', function(){
	
	event.preventDefault();
	
	$('.edit_settings_message').html("");	
	$('.edit_settings_message').removeClass('error_message');
	
	var username = $('#settings_username').val();
	
	$.post("user_info_edit_ajax.php",
	{
		username:username
	},
	function(data,status){
	
		var split_data = data.split("...");
		var content = split_data[0];
		var action = split_data[1];
		
		if( action == "success" ){
		
			$('.user_settings_content_username').html(content);
		
		}else{
			
			$('.edit_settings_message').html(content);
			$('.edit_settings_message').addClass('error_message');
			
		
		}
	
	});

});





</script>





</body>

</html>