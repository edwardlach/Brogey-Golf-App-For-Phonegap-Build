<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


$user_id = $_SESSION['id'];

if( $_GET['leagueid'] ){

	$league_id = $_GET['leagueid'];
	
}

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);


?>

<!doctype html>

<html class="email_invite">

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

<body class="email_invite"> 

<div data-role="page" id="email_invite">
	
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
					<span id="site_title_medium">Invite Someone New!</span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="email_invite_content" id="<?php echo $league_id; ?>">
		<div id="messages"></div>
		<form id="round_info" method="post">
		
			<div class="form-group">
				<label for="first_name">First Name</label>
		        	<input id="first_name" name="first_name" type="text" pattern=".{1,100}" required />
		        </div>
		        <div class="form-group">
				<label for="last_name">Last Name</label>
		        	<input id="last_name" name="last_name" type="text" pattern=".{1,100}" required />
		        </div>
		        <div class="form-group">
				<label for="email_address">Email Address</label>
		        	<input id="email_address" name="email_address" type="text" pattern=".{1,100}" required />
		        </div>
			
			<div class="form-group">
		        		<input class="<?php echo $user_id; ?> btn btn-default" id="invite_brogey" type="submit" name="submit" value="Invite" />
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


$("#invite_brogey").on("click", function(){
	
	var str		= $(this).attr("class");
	var email	= $("#email_address").val();
	var first_name	= $("#first_name").val();
	var last_name	= $("#last_name").val();
	var leagueid	= $(".email_invite_content").attr('id');
	
	var name = first_name + " " + last_name;
	var str_array = str.split(" ");
	var invitee = str_array[0];

	if( !email == "" && !name == "" ){
		
		$.post("email_invite_ajax.php",
		  {
		    email:email,
		    invitee:invitee,
		    name:first_name,
		    leagueid:leagueid
		  },
		  function(data,status){
			
			if( data != "" ){
				
				$('#messages').html(data);
			
			}else{
		  		
		  		$('#messages').html(name + ' has been sent an invitation to join Brogey Golf.');
		  	
		  	}
		  	
			$('#messages').addClass('info_message');
			$("#email_address").val("");
			$("#first_name").val("");
			$("#last_name").val("");
		    
		  });
		
		
	}else{
		
		$('#messages').removeClass('info_message');
		$('#messages').html('All fields are required');
		$('#messages').addClass('error_message');
		
	}
	
	event.preventDefault(); 

});






</script>





</body>

</html>