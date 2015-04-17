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

<div data-role="page" id="league_setup">
	
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
					<span id="site_title"><?php echo $display_date ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	



	<div data-role="content">
	
		<form id="league_setup" method="post">
			
			<div class="form-group">
				<label for="league_name">League Name</label>
		        		<input id="league_name" name="league_name" type="text" pattern=".{1,100}" placeholder="example: League of Extraordinary Gentleman" maxlength="40" required />
		        	</div>
		        	<div class="form-group">
			        <label for="handicap_yes_no">Handicaps:</label>
				<select name="handicap_yes_no" id="handicap_yes_no" data-role="slider" required>
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select> 
			</div>
			<div class="form-group">
			        <label for="season_weeks">Number of Rounds:</label>
				<input type="range" name="season_weeks" id="season_weeks" value="8" min="1" max="52" step="1" required/>
			</div>
			<div class="form-group">
			        <label for="league_frequency">Length of a Round in Weeks (How often are scores accepted and points awarded):</label>
				<input type="range" name="league_frequency" id="league_frequency" value="1" min="1" max="5" step="1" required />
			</div>
			<div class="form-group">			       
			        <label for="holes">League Holes:</label>
				<select name="holes" id="holes" data-role="slider" required>
					<option value="9">9</option>
					<option value="18">18</option>
				</select> 
			</div>
			<div class="form-group">			       
			        <label for="date_day">Start Day:</label>
			        <input type="range" name="date_day" id="date_day" value="1" min="1" max="32" step="1" required/>
			</div>
			<div class="form-group">   	 
				<label for="date_month">Start Month:</label>
			        <select id="date_month" name="date_month" required>
			            	<option disabled selected>-- Select a Month --</option>
				        <option value="1">January</option>
				        <option value="2">February</option>
				        <option value="3">March</option>
				        <option value="4">April</option>
				        <option value="5">May</option>
				        <option value="6">June</option>
				        <option value="7">July</option>
				        <option value="8">August</option>
				        <option value="9">September</option>
				        <option value="10">October</option>
				        <option value="11">November</option>
				        <option value="12">December</option>
			        </select>  
			</div>
			<div class="form-group">                             
				<label for="date_year">Start Year:</label>
				<select id="date_year" name="date_year">
				        <option value="2015">2015</option>
				        <option value="2016">2016</option>
				        <option value="2017">2017</option>
			        </select>  
			</div>
		       
		        <div class="form-group">
		        		<input class="btn btn-default" id="submit_league_info" type="submit" name="submit_league_info" value="Create League" />
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




$("#submit_league_info").on("click", function(){
	
	var league_name		= $('#league_name').val();
	var handicap_yes_no	= $('#handicap_yes_no').val();
	var number_of_rounds	= $('#season_weeks').val();
	var league_frequency	= $('#league_frequency').val();
	var season_weeks	= number_of_rounds * league_frequency;
	var holes		= $('#holes').val();
	var date_year		= $('#date_year').val();
	var date_month		= $('#date_month').val();
	var date_day		= $('#date_day').val();
	
	
	$.post("league_setup_ajax.php",
	  {
	  	league_name:league_name,
	  	handicap_yes_no:handicap_yes_no,
	  	season_weeks:season_weeks,
	  	league_frequency:league_frequency,
	  	holes:holes,
	  	date_year:date_year,
	  	date_month:date_month,
	  	date_day:date_day
	  },
	  function(data,status){
	 	
	 	$(location).attr( 'href', 'league_add_member.php?new=1');
		
	  });

	event.preventDefault(); 

});









</script>





</body>

</html>