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



$query_user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );



$unfinished_round_query = "SELECT * FROM `bg_app_rounds` "
			 ."WHERE `user_id` = '$query_user_id' "
			 ."AND `is_complete` = '0' "
			 ."ORDER BY `start_date` DESC";

$run_unfinished_round_query = mysqli_query( $link, $unfinished_round_query );

$unfinished_round_results = array();


while( $unfinished_round_row = mysqli_fetch_array( $run_unfinished_round_query, MYSQLI_ASSOC ) ){
	
	array_push( $unfinished_round_results, $unfinished_round_row );

}


if($unfinished_round_results ){
	
	
	foreach( $unfinished_round_results as $unfinished_round_result ){
	
		$round_id = $unfinished_round_result['round_id'];
		$course_name = $unfinished_round_result['course_name'];
		$start_date = $unfinished_round_result['start_date'];
		$front_back_both = $unfinished_round_result['front_back_both'];
		
		if( $front_back_both == "both" ){
			
			$holes_to_play = 18;
			
		}else{
		
			$holes_to_play = 9;
		
		}
		
		$display_date_raw = new DateTime($start_date);
		
		$display_date = date_format($display_date_raw, 'M d, Y');
		
		
		
		$unfinished_round_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
				     			      ."WHERE round_id = '$round_id'" );
		
		$unfinished_round = array();
		
		
		while( $unfinished_round_row = mysqli_fetch_array( $unfinished_round_query, MYSQLI_ASSOC ) ){
			
			array_push( $unfinished_round, $unfinished_round_row );
		
		}
		
		foreach( $unfinished_round as $round_hole ){
		
			$score = $score + $round_hole['score'];
			$par = $par + $round_hole['par'];
		
		}
		
		$score_from_par = $score - $par;
		
		$holes_played = mysqli_num_rows($unfinished_round_query);
	
	
		$html .= <<<EOHTML
		
			<table class="continue_round_list" id="$round_id.$course_name">		
				<tr class="continue_round_row">
			    		<th class="continue_round_column course_name">$course_name</th>
			    		<th class="continue_round_column round_progress">Hole</th>
			    		<th class="continue_round_column points">Score</th>
			    		<th class="continue_round_column direction_arrow"><i class="fa fa-chevron-right"></i></th>
			    	</tr>
						
				<tr class="continue_round_row">
					<td class="continue_round_column course_name" id="list_date">$display_date</td>
					<td class="continue_round_column round_progress">$holes_played of $holes_to_play</td>
					<td class="continue_round_column points">$score_from_par</td>
					<td class="continue_round_column direction_arrow"><i class="fa fa-chevron-right"></i></td>
				</tr>
			</table>
		
EOHTML;

	}

}else{

	$html .= <<<EOHTML
	
	<div class="no_league_invites_watermark">No unfinished rounds, go ahead and start something fresh!</div>

EOHTML;

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
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>	
	<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	
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

<div data-role="page" id="continue_round">
	
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
	
	<div data-role="header" id="unfinished_rounds_header">Unfinished Rounds:</div><!-- /unfinished_round_header -->
	
	



	<div data-role="content" class="continue_round_content">
		
			
		<?php
		
			echo $html;
			
		?>
		
		<div class="swipe_instructions">To Delete Round<span class="swipe_arrow">&emsp;<i class="fa fa-chevron-left"></i>&emsp;</span>Swipe</div>
		
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


$('.continue_round_list').on('click', function(){

	var data = $(this).attr('id');
	var data_split = data.split(".");
	var round_id = data_split[0];
	var course = data_split[1];
	
	$(location).attr( 'href', 'score_input.php?roundid='+round_id+'&course='+course );

});


$('.continue_round_list').on( 'swipeleft', function(){
	
	var data = $(this).attr('id');
	var data_split = data.split(".");
	var roundid = data_split[0];
	
	$(this).css('background-color', '#FDE4E1');
	
	if (confirm('Are you sure you would like to delete this unfinished round?')) {
		
		$.post("continue_round_ajax_remove_round.php",
		  {
		    roundid:roundid
		  },
		  function(data,status){
		 
		 	location.reload();
		 	
		  });
		  
		
	}else{
	
		$(this).css('background-color', 'transparent');
	
	}

});












</script>





</body>

</html>