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

$user_email_query = mysqli_query( $link, "SELECT email_address FROM bg_app_users WHERE user_id = '$user_id'" );
$user_email_row = mysqli_fetch_row( $user_email_query );
$user_email = $user_email_row[0];


	






?>



<!doctype html>

<html class="brogey_contact">

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

<body class="brogey_contact"> 

<div data-role="page" id="brogey_contact">
	
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
	
	


	<div data-role="content" class="brogey_contact_content">
		
		<div class="message_space"></div>
		
		<form>
			<div class="form-group">
				<label for="subject_label">Subject:</label>
		        	<input class="message_subject" id="subjet_label" name="subject_label" type="text" pattern=".{1,100}" required />
		        </div>
		        
			<div class="form-group">
				<label for="textarea-a">Message:</label>
		        	<textarea class="message_content" name="textarea" id="textarea-a" required></textarea>
		        </div>
		        
		        <div class="form-group">
		        	<a class="send_brogey_email" id="<?php echo $user_email; ?>" href="#">Send us a Message!</a>
		        </div>	
		</form>
			
		
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


$('.send_brogey_email').on('click', function(){

	var subject = $('.message_subject').val();
	var message = $('.message_content').val();
	var email   = $(this).attr('id');
	
	if( $('.message_space').hasClass('error_message') ){
		
		$('.message_space').removeClass('error_message');
		$('.message_space').empty();	
	
	}
	
	if( subject != "" && message != "" ){
		$.post("brogey_contact_ajax.php",
		  {
		  	subject:subject,
		  	message:message,
		  	email:email
		  },
		  function(data,status){
		 
			$(location).attr( 'href', 'index.php?contactEmail=' + email );	
				
		  });
	}else{
		
		$('.message_space').addClass('error_message');
		$('.message_space').html("Please include both a Subject and a message.");		
	
	}
	
		


});




</script>





</body>

</html>