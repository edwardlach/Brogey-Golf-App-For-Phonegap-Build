<?php
session_start();

if(!$_SESSION['id']){

	header( "Location: login.php" );

}elseif(!$_SESSION['round_id']){

	header( "Location: continue_round.php" );

}

if( $_GET['roundid'] ){
	
	$_SESSION['round_id'] = $_GET['roundid'];
	$_SESSION['course_name'] = $_GET['course'];
	header( "Location: score_input.php" );

	
}

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);

function display_date($link){

	
	$query_round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );
	$user_id = mysqli_real_escape_string( $link, $_SESSION['id'] );
	
	
	$round_start_date_query = mysqli_query($link, "SELECT start_date FROM bg_app_rounds "
						     ."WHERE user_id = '$user_id' "
						     ."AND round_id = '$query_round_id'");
	    	
	$round_start_date_row = mysqli_fetch_row($round_start_date_query);
	
	$round_start_date = $round_start_date_row[0];
	
	$display_date_raw = new DateTime($round_start_date);
	
	$display_date = date_format($display_date_raw, 'M d, Y');

	return $display_date;

}


$display_date = display_date($link);


function score_from_par($link){

	
	$query_round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );

	/* query to pull all of the hole info already submitted for the current round */
	
	$scorecard_query = "SELECT * FROM `bg_app_holes` "
			  ."WHERE `round_id` = '$query_round_id' "
			  ."ORDER BY `hole_id` ASC";
			  		 
	$run_scorecard_query = mysqli_query( $link, $scorecard_query );

	$scorecard_results = array();
	
	
	while( $scorecard_row = mysqli_fetch_array( $run_scorecard_query, MYSQLI_ASSOC ) ){
		
		array_push( $scorecard_results, $scorecard_row );
	
	}
	
	foreach($scorecard_results as $hole_score) {
		
		$score += ($hole_score['score']);
		
	}
	
	
	foreach($scorecard_results as $hole_par) {
			
		$par += ($hole_par['par']);
			
	}
		
	$score_from_par = $score - $par;
	
	return $score_from_par;
	
	

}


$score_from_par = score_from_par($link);

function find_current_hole_number($link) {

	
	$query_round_id	= mysqli_real_escape_string( $link, $_SESSION['round_id'] );
    	
    	$holes_played_query = "SELECT * FROM `bg_app_holes` WHERE `round_id` = '$query_round_id'";
    	
    	$holes_played_result = mysqli_query($link, $holes_played_query);	
    	
    	$holes_played = mysqli_num_rows($holes_played_result);
    	
    	$current_hole = $holes_played + 1;
    	
    	$front_back_query = "SELECT `front_back_both` FROM `bg_app_rounds` WHERE `round_id` = '$query_round_id'"; 
    	
    	
    	$run_front_back_query = mysqli_query($link, $front_back_query);
    	
    	$front_back_row = mysqli_fetch_row($run_front_back_query);
    	
    	$front_back = $front_back_row[0];
    	
    	
	if ($front_back == "back") {
		
		$current_hole = $current_hole + 9;
	}
	
	
	return $current_hole;
	 
}

$current_hole = find_current_hole_number($link);




function auto_pop_data($current_hole, $link) {
	
		
	$round_id = $_SESSION['round_id'];
	$user_id = $_SESSION['id'];
	
	$current_round_info_query = mysqli_query( $link, "SELECT * FROM bg_app_rounds "
							."WHERE round_id = '$round_id'" );
							
	$current_round_info = array();
		
	
	while( $current_round_info_row = mysqli_fetch_array( $current_round_info_query, MYSQLI_ASSOC ) ){
		
		array_push( $current_round_info, $current_round_info_row );
	
	}
	
	foreach( $current_round_info as $info ){
	
		$course_name = $info['course_name'];
		$slope_rating = $info['slope_rating'];
		$hole = $current_hole;
		
		if( $hole <= 9 ){
			
			$front_back = "front";
		
		}else{
			
			$front_back = "back";
			
		}
			
		
		
		$auto_pop_round_query = mysqli_query( $link, "SELECT * FROM  bg_app_rounds "
							   ."WHERE course_name = '$course_name' "
							   ."AND user_id = '$user_id' "
							   ."AND round_id != '$round_id' "
							   ."AND (front_back_both = '$front_back' "
							   ."OR front_back_both = 'both') " 
							   ."LIMIT 0, 1" );
		$auto_pop_round = array();
		
	
		while( $auto_pop_round_row = mysqli_fetch_array( $auto_pop_round_query, MYSQLI_ASSOC ) ){
			
			array_push( $auto_pop_round, $auto_pop_round_row );
		
		}					   
							   
		foreach( $auto_pop_round as $auto ){
			
			$auto_round = $auto['round_id'];
			
			$auto_pop_info_query = mysqli_query( $link, "SELECT * FROM bg_app_holes "
								   ."WHERE round_id = '$auto_round' "
								   ."AND hole_id = '$hole'" );
								   
			$auto_pop_info = array();
		
	
			while( $auto_pop_info_row = mysqli_fetch_array( $auto_pop_info_query, MYSQLI_ASSOC ) ){
				
				array_push( $auto_pop_info, $auto_pop_info_row );
			
			}	
									 
			foreach( $auto_pop_info as $pop_info ){
				
				$handicap = $pop_info['hole_handicap'];
				$par = $pop_info['par'];
				$pop_info_array = [$handicap, $par];	
		
			}
		
		}
		
	
	
	
	}
	
	
	
	
	if( !$pop_info_array ){
	
		$pop_info_array = [9, 4];
	
	}
	
	
	
	
	
	return $pop_info_array;


}


$pop_info_array = auto_pop_data($current_hole, $link);




?>



<!doctype html>

<html>

<head>

	<title>Brogey Golf</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	
	<script type="text/javascript">
	
		$(document).bind("mobileinit", function () {
		    $.mobile.ajaxEnabled = false;
		});
	
	</script>
	
	
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>	
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

<div data-role="page" id="score_input">
	
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
		<table id="input_header">
			<tr>
				<th id="input_course"></th>
				<th id="input_hole">Hole</th>
				<th id="input_score_from_par">Score</th>
			</tr>
			<tr>
				<td id="input_course"><?php echo $_SESSION['course_name']; ?></td>
				<td id="input_hole"><?php echo $current_hole; ?></td>
				<td id="input_score_from_par" class="<?php echo $score_from_par; ?>"><?php echo $score_from_par; ?></td>
			</tr>
		</table>
	</div><!-- /header -->
	

	


	<div data-role="content" id="score_input_content">
		
		<div id="handicap_already_used"></div>
		
		<form id="score_input" method="post">
			<div data-role="fieldcontain">
			    	<label for="hole_handicap">Handicap</label>
			    	<input type="range" name="hole_handicap" id="handicap" value="<?php echo $pop_info_array[0] ?>" min="1" max="18" step="1" required/>
			
				<label for="par">Par</label>
				<input type="range" name="par" id="par" value="<?php echo $pop_info_array[1] ?>" min="3" max="5" step="1" required/>
			
				<label for="score">Score</label>
				<input type="range" name="score" id="score" value="4" min="1" max="15" step="1" required/>
				
			</div>
			
		    	<input class="btn btn-default next_hole_button" type="submit" name="submit" id="<?php echo "next_hole"; ?>" value="Next Hole" />
		    
		</form>
		
		<a href="scorecard.php" data-role="button" data-ajax="false" id="view_scorecard">Scorecard</a>
	
		<?php
	
		
		?>
	
		
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



$(".next_hole_button").on("click", function(){
	
	var score	= $("#score").val();
	var par		= $("#par").val();
	var handicap	= $("#handicap").val();
	
	$.post("score_input_verify_handicap.php",
	  {
	    handicap:handicap
	  },
	  function(data,status){
	 	
		if( data == 0 ){
			
			$.post("score_input_ajax_new.php",
			  {
			    score:score,
			    par:par,
			    handicap:handicap
			  },
			  function(data,status){
			  
			  	if( data == "finished" ){
			 	
				 	$(location).attr( 'href', 'scorecard.php');
				
				}
				
				if( data == "continue" ){
				
					location.reload();
				
				}
			    	
			  });
		
		}else{
		
			$('#handicap_already_used').html("Handicap " + handicap + " was already used.");
			$('#handicap_already_used').addClass("error_message");
			  
		}
	    
	  });

	event.preventDefault(); 

});



setInterval(function(){
   
  	var score = $('#score').val();
	var par = $('#par').val();
	
	var score_change = score - par;
	var current_score = $("td#input_score_from_par").attr('class');
	var current_score_int = parseInt(current_score);
	
	var updated_score = current_score_int + score_change;
   	$("td#input_score_from_par").text(updated_score);
			
   
},1000);




</script>





</body>

</html>