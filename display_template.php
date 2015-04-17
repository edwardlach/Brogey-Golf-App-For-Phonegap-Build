<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


date_default_timezone_set('America/New_York');

$user_id = $_SESSION['id'];

if( $_GET['leagueid'] ){

	$league_id = $_GET['leagueid'];
	$_SESSION['league_id'] = $league_id;
	
}


if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}

include "simple_functions.php";
$link = connect_to_database();


$league_info_display_round_query = mysqli_query( $link, "SELECT * FROM bg_app_league WHERE league_id = '$league_id'" );
$league_info_display_round = array();
while( $league_info_display_round_row = mysqli_fetch_array( $league_info_display_round_query, MYSQLI_ASSOC ) ){
	array_push($league_info_display_round, $league_info_display_round_row );
}	

foreach($league_info_display_round as $league) {

	$league_name			= $league['league_name'];
	$league_holes			= $league['holes'];
	$start_date_for_round_number	= $league['start_date'];
	$use_handicap			= $league['use_handicaps'];
	$frequency			= $league['frequency_by_weeks'];
	
}








$today_for_round_number = new DateTime("now");

$start_date_for_round_number_raw = new DateTime($start_date_for_round_number);
				
$date_difference_for_round_number = date_diff( $today_for_round_number, $start_date_for_round_number_raw );

$difference_in_days_for_round_number = $date_difference_for_round_number->days;

$round_length_in_days = $frequency * 7;

$display_round_number = ceil( $difference_in_days_for_round_number / $round_length_in_days );

$round_number = $display_round_number;

$weeks_to_modify = $display_round_number * $frequency;

$round_end_date_raw = date_modify($start_date_for_round_number_raw, '+'.$weeks_to_modify.' week');

$round_end_date = date_format($round_end_date_raw, 'M d, Y');

if( $use_handicap == 1 ){
	
	$display_handicap = "Yes";
	
}else{
	
	$display_handicap = "No";

}




include "display_leaderboard.php";

$results = create_leaderboard($league_id, $user_id, $historical);

if( $_GET['pageid'] ){
	
	$page = $_GET['pageid'];

}else{

	$page = "round_in_progress";
	$page_title = "Current Round";
	
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

</head>

<body> 

<div data-role="page" id="display_most_recent_round">
	
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

	
	



	<div data-role="content" class="current_round_content">
		
		<div class="extra_league_info">Holes: <?php echo $league_holes; ?>
			</br>Hcap: <?php echo $display_handicap; ?>
			</br>Round: <?php echo $display_round_number; ?>
			</br><span class="extra_league_info_end_date_title">Round Ends</span>
			</br><span class="extra_league_info_end_date"><?php echo $round_end_date; ?></span>
		</div>
		<div class="round_result_title"><?php echo $page_title; ?><img id="round_info_icon" src="/images/information3.png" style="width:20px;height:20px"></div>
		
		<div class="variable_display_content" id="<?php echo $page; ?>"></div>
		
		<div class="league_name_watermark"><?php echo $league_name; ?></div>
		
		
		
		
		
		
	</div><!-- /content -->
	
	<div data-role="footer" data-position="fixed">
		<div class="row">
			<div <div class="col-md-10 credits">&middot; &copy; Brogey Golf 2014 &middot; Designed by Brogey Boss Ed &middot;</div>
		</div>
	</div><!-- /footer -->

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



$( ".round_result_title" ).on( "click",  function() {
	
	
 
	if( $( ".extra_league_info" ).css("display") == "none" ){ 
 	
 		
 		
  		$( ".extra_league_info" ).show();
  		
  	}else{
  	
  		$( ".extra_league_info" ).hide();
  		
  	}
  	
});


function load_round_in_progress(){
	
	$.get( "display_round_in_progress_ajax.php", function( data ) {
	  
		    $(".variable_display_content").html(data);
		  
		    function close_accordion_row() {
		        $('.leaderboard_accordion .leaderboard_section_title').removeClass('active');
		        $('.leaderboard_accordion .leaderboard_section_content').slideUp(300).removeClass('open');
		    }
		 
		    $('.leaderboard_section_title').click( function(event) {
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
		
		
		$('.current_round_content').on( 'swiperight', function(){
			
			$(".variable_display_content").attr('id', 'most_recent_round').trigger('idChanged');
	
	    	});

}


function load_most_recent_round(previousRound){
			
	$.get( "display_most_recent_round_ajax.php?"+previousRound, function( data ) {
		
		$(".variable_display_content").html(data);
		
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
		            $(".match_front").show();
		            $(".match_front").css("width", "100%");
			    $(".match_back").hide();
			    $(".match_back").css("width", "0%");
		        }else {
		            close_accordion_row();
		 
		            // Add active class to section title
		            $(this).addClass('active');
		            // Open up the hidden content panel
		            $('.leaderboard_accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
		            $(".match_front").show();
		            $(".match_front").css("width", "100%");
			    $(".match_back").hide();
			    $(".match_back").css("width", "0%");
		        }
		 
		        event.preventDefault();
		});
			
		$( ".match_result_scorecard" ).on( "click",  function() {
	
			if( $(".match_back").css("display") == "none" ){
				
				$(".match_back").show();
				$(".match_back").animate( {width: "100%"}, 800);
				$(".match_front").hide();
				$(".match_front").animate( {width: "0%"}, 800);	
				
			}else{
			
				$(".match_front").show();
				$(".match_front").animate( {width: "100%"}, 800);
				$(".match_back").hide();
				$(".match_back").animate( {width: "0%"}, 800);
				
			}
					
		});
		
	});
	
	$('.current_round_content').on( 'swiperight', function(){
	
		$(".variable_display_content").attr('id', 'previous_rounds').trigger('idChanged');
		
	});
	
	
	$('.current_round_content').on( 'swipeleft', function(){
	
		$(".variable_display_content").attr('id', 'round_in_progress').trigger('idChanged');
		
	});

}


function load_previous_rounds(){

	$.get( "display_previous_rounds_ajax.php", function( data ) {
	
		$(".variable_display_content").html(data);
	
		$('.current_round_content').on( 'swipeleft', function(){
	
			$(".variable_display_content").attr('id', 'most_recent_round').trigger('idChanged');
		
		});
		
		
		$('.previous_round_list_row').on( 'click', function(){
			
			$(".variable_display_content").attr('id', 'most_recent_round');
			
			var previousRound = $(this).attr('id');
			
			load_most_recent_round(previousRound);
		
		});
	
	});

}





$(document).ready(function() {

	var current_page = $(".variable_display_content").attr('id');
	
	if( current_page == "round_in_progress" ){	

		load_round_in_progress();
	
	}

});


$(".variable_display_content").bind('idChanged', function(){
	
	var current_page = $(this).attr('id');
	
	if( current_page == "most_recent_round"){
		
		var previousRound = "";
		load_most_recent_round(previousRound);
	
	}
	
	if( current_page == "round_in_progress" ){	

		load_round_in_progress();
	
	}
	
	if( current_page == "previous_rounds" ){
	
		load_previous_rounds();	
	
	}

});

	
	



</script>





</body>

</html>