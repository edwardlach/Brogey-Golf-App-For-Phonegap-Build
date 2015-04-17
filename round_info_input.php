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

<div data-role="page" id="round_info_input">
	
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
	
	



	<div data-role="content">
	
		<form id="round_info" method="post">
		
			<div class="form-group">
				<label for="course_name">Course Name</label>
		        	<input id="course_name" name="course_name" type="text" pattern=".{1,100}" placeholder="example: Oakmont CC" required />
		        </div>
		        <div class="course_search_results"></div>
	        	<div class="form-group">
		        	<label for="front_back_both">How many holes?</label>
		        	<select id="front_back_both" name="front_back_both" required>
			        	<option disabled selected>--Which holes today?--</option>
		        		<option value="front">Front 9</option>
		        		<option value="back">Back 9</option>
		        		<option value="both">All 18</option>
		        	</select>
			</div>
			<div class="form-group">
			        <label for="course_rating">Course Rating</label>
			        <input type="range" name="course_rating" id="course_rating" value="71" min="62" max="78" step=".1" required/>
			</div>
			<div class="form-group">
			        <label for="slope_rating">Slope Rating<label>
			        <input type="range" name="slope_rating" id="slope_rating" value="124" min="55" max="155" step="1" required/>
			</div>
		       	<div class="form-group">Date input is not required, if you leave the field blank today&apos;s date will be used.</div>
			<div class="form-group">			       
			        <label for="date_day">Day:</label>
			        <input type="range" name="date_day" id="date_day" value="1" min="1" max="32" step="1" />
			</div>
			<div class="form-group">   	 
				<label for="date_month">Month:</label>
			        <select id="date_month" name="date_month">
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
				<label for="date_year">Year:</label>
				<select id="date_year" name="date_year">
				        <option value="2015">2015</option>
				        <option value="2014">2014</option>
				        <option value="2013">2013</option>
			        </select>  
			</div>
		        
		        <div class="form-group">
		        		<input class="btn btn-default" id="continue_to_score_input" type="submit" name="submit" value="Continue" />
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

/*
$.get( "round_info_html_ajax.php", function( data ) {
  
  $("#round_input_form_html").html(data);
  
});
*/

$("#course_name").on( 'keypress', function(){
	
	var search = $(this).val();
	var search_populate = "search";
	
	$.post("round_search_info_ajax.php",
	  {
	    search:search,
	    search_populate:search_populate
	  },
	  function(data,status){
	 
	 	$(".course_search_results").html(data);
	 	
	  });

});


$(".course_search_results").on( 'click', ".course_result", function(){

	var round_id = $(this).attr('id');
	var search_populate = "populate";
	
	$.post("round_search_info_ajax.php",
	  {
	    round_id:round_id,
	    search_populate:search_populate
	  },
	  function(data,status){
	 	
	 	var jsonData = jQuery.parseJSON( data );
			
		var row = jsonData[0];
		var course_name = row.course_name;
		var slope_rating = row.slope_rating;
		var course_rating = row.course_rating;
		var front_back_both = row.front_back_both;
		
		$("#course_name").val(course_name);	
	 	$("#slope_rating").val(slope_rating);
	 	$("#course_rating").val(course_rating);
	 	

	 	
	  });
	  
});




$("#continue_to_score_input").on("click", function(){
	
	var course_name		= $("#course_name").val();
	var front_back		= $("#front_back_both").val();
	var course_rating	= $("#course_rating").val();
	var slope_rating		= $("#slope_rating").val();
	var date_day		= $("#date_day").val();
	var date_month		= $("#date_month").val();
	var date_year		= $("#date_year").val();

	
	if( !front_back || !course_name ){
	
		alert("Please make sure you provide both a course name and how many holes you would like to play before continuing.");
	
	}else{
	
		$.post("round_info_input_ajax.php",
		  {
		    course_name:course_name,
		    front_back:front_back,
		    course_rating:course_rating,
		    slope_rating	:slope_rating,
		    date_day:date_day,
		    date_month:date_month,
		    date_year:date_year
		  },
		  function(data,status){
			
		  	$(location).attr( 'href', 'score_input.php');
		    
		  });
		  
	}
	  
	event.preventDefault(); 

});





</script>





</body>

</html>