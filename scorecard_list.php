<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

date_default_timezone_set('America/New_York');


include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);


$display_date_for_title_raw = new DateTime("now");
	
$display_date_for_title = date_format($display_date_for_title_raw, 'M d, Y');

$user_id_to_calculate_handicap = $_SESSION['id'];

include "calculate_handicap.php";
$handicap = main_handicap_calculation($link, $user_id_to_calculate_handicap); 

$query_user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );


/* query to pull all of the scorecards successfully submitted */

$scorecard_list_query = "SELECT * FROM `bg_app_rounds` "
			."WHERE `user_id` = '$query_user_id' "
			."AND `is_complete` = '1' "
			."ORDER BY `start_date` DESC";

$run_scorecard_list_query = mysqli_query( $link, $scorecard_list_query );

$scorecard_list = array();

while( $scorecard_list_row = mysqli_fetch_array( $run_scorecard_list_query, MYSQLI_ASSOC ) ){

	array_push( $scorecard_list, $scorecard_list_row );
	
}







$html = <<<EOHTML
<table  id="scorecard_list" >
<tbody>
    	<tr class="scorecard_list_row_title">
    		<th class="list_course_name">Course</th>
    		<th class="list_par">Par</th>
    		<th class="list_score">Score</th>
    		<th class="direction_arrow"><i class="fa fa-chevron-right"></i></th>
    	</tr>
   
EOHTML;

foreach( $scorecard_list as $row ){

	$course_name	= $row['course_name'];
	$date		= $row['start_date'];
	$front_back	= $row['front_back_both'];
	$front_score 	= $row['front_score'];
	$back_score	= $row['back_score'];
	$front_par	= $row['front_par'];
	$back_par	= $row['back_par'];
	$round_id	= $row['round_id'];
	
	
	$display_date_raw = new DateTime($date);
	
	$display_date = date_format($display_date_raw, 'M d, Y');
	
	
	if( $front_back == "front" ){
		
		$par	= $front_par;
		$score	= $front_score;
		
		$calculation_par = $front_par * 2;
		$calculation_score = $front_score * 2;
		$calculation_diff = $calculation_score - $calculation_par;
		
		if( $calculation_diff > $handicap ){
			
			$above_below = "above";
		
		}else{
			
			$above_below = "below";
			
		}

	}elseif( $front_back == "back" ){
	
		$par	= $back_par;
		$score	= $back_score;
		
		$calculation_par = $back_par * 2;
		$calculation_score = $back_score * 2;
		$calculation_diff = $calculation_score - $calculation_par;
		
		if( $calculation_diff > $handicap ){
			
			$above_below = "above";
		
		}else{
			
			$above_below = "below";
			
		}
	
	}else{
		
		$par 	= $front_par + $back_par;
		$score	= $front_score + $back_score;
		
		$calculation_diff = $score - $par;
		
		if( $calculation_diff > $handicap ){
			
			$above_below = "above";
		
		}else{
			
			$above_below = "below";
			
		}

	}
	
	$html .= <<<EOHTML
		<tr class="scorecard_list_row total_split $above_below" id="$round_id">
			<td class="list_course_name">$course_name</br><span id="list_date">$display_date</span></td>
			<td class="list_par">$par</td>
			<td class="list_score">$score</td>	
			<td class="direction_arrow"><i class="fa fa-chevron-right"></i></td>
		</tr>
EOHTML;



}

$skill_html = <<<EOHTML

	<table class="skill in_words">
		<tr>
			<td>
				<div id="level_5"><span id="level_5">John Daly</span></div>
				<div id="level_4"><span id="level_4">T-Rex</span></div>
				<div id="level_3"><span id="level_3">Bear</span></div>
				<div id="level_2"><span id="level_2">Squirrel</span></div>
				<div id="level_1"><span id="level_1">Acorn</span></div>
			<td>
		</tr>
	</table>
	
	<table class="skill in_numbers">
		<tr>
			<td>
				<div id="level_5"><span id="level_5">< 0</span></div>
				<div id="level_4"><span id="level_4">0-5</span></div>
				<div id="level_3"><span id="level_3">5-12</span></div>
				<div id="level_2"><span id="level_2">12-22</span></div>
				<div id="level_1"><span id="level_1">> 22</span></div>
			<td>
		</tr>
	</table>

EOHTML;



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
	
	<!--Load the AJAX API-->
	<script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

	<script type="text/javascript">
	
		google.setOnLoadCallback(load_page_data());
			
		function load_page_data(){
		
			$.get( "scorecard_list_find_chart_data.php", function( data ) {
	      			
				var jsonData = jQuery.parseJSON( data );
				
				
				var above = 0;
				var below = 0;
				
			
				for (var i = 0; i < jsonData.length; i++) {
	      				
	      				var row = jsonData[i];
	      				var date = row.date;
	      				var strokes = row.strokes;
	      				var course = row.course_name;
	      				var par = row.par;
	      				var score = row.score;
	      				var above_below = row.above_below;
	      			
	      				if(above_below == "above"){
	      					
	      					above = above + 1;
	      				
	      				}
	      				
	      				if(above_below == "below"){
	      					
	      					below = below + 1;
	      				
	      				}
	      	
	      					
		      		}
		      		
		      		var chartData = google.visualization.arrayToDataTable([
			          ['Above or Below Handicap', 'Rounds'],
			          ['Above Handicap', above],
			          ['Below Handicap', below]
			        ]);
			        
		               drawChart(chartData);
				
			});
			
		}
		
		
		
		function drawChart(chartData) {
			
			data = chartData;		

			var options = {
				          pieHole: 0.6,
				          legend: 'none',
				          backgroundColor: 'transparent',
				          chartArea:{width:'100%',height:'90%'},
				          enableInteractivity: 'true',
				          slices: {0: {color: '#004472'}, 1: {color: '#a4daff'}},
				          pieSliceText: 'none',
				          tooltip: {trigger:'selection'}
				      };
		
			var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
			
			chart.draw(data, options);
			
			// The select handler. Call the chart's getSelection() method
			function selectHandler() {
				
				if( $('.above').hasClass('graph_element') ){
					$('.above').removeClass('graph_element');
				}
				
				if( $('.below').hasClass('graph_element') ){	
					$('.below').removeClass('graph_element');
				}
				
				var selectedItem = chart.getSelection();
				
				if( selectedItem != "" ){
					var item_obj = selectedItem[0];
					var item_row = item_obj.row;
					
					var str = data.getFormattedValue(item_obj.row, 0);
					
					if( str == "Above Handicap" ){
						$('.above').addClass('graph_element');
					}
					
					if( str == "Below Handicap" ){	
						$('.below').addClass('graph_element');
					}
				}
				
			}
			
			// Listen for the 'select' event, and call my function selectHandler() when
			// the user selects something on the chart.
			google.visualization.events.addListener(chart, 'select', selectHandler);
		
		
		}	
		
		
		
		
	
	</script>
	
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

<div data-role="page" id="scorecard_list">
	
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
					<span id="site_title"><?php echo $display_date_for_title; ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	



	<div data-role="content" class="scorecard_list_content">
		
		<div id="score_data_dashboard">
			<div id="handicap_label">Handicap</div>
			<div id="handicap"><?php echo $handicap; ?></div>
			<div id="chart_div" style="width:100%; height:150px"></div>
			<?php echo $skill_html; ?>
			
		</div>
		

		<?php echo $html; ?>
		
	</div><!-- /content -->
	
</div><!-- /page -->



<script type="text/javascript">

$( document ).ready(function() {
    var handicap_text = $('#handicap').html();
    var handicap = parseFloat(handicap_text);
    
    if( handicap <= 0 ){
    	$('span#level_5').addClass('user_skill_level');
    }
    if( handicap > 0 && handicap <= 5 ){
    	$('span#level_4').addClass('user_skill_level');
    }
    if( handicap > 5 && handicap <= 12 ){
    	$('span#level_3').addClass('user_skill_level');
    }
    if( handicap > 12 && handicap <= 22){
    	$('span#level_2').addClass('user_skill_level');
    }
    if( handicap > 22 ){
    	$('span#level_1').addClass('user_skill_level');
    }
    
});

$( document ).on('click', function() {	

	if( $('.above').hasClass('graph_element') ){
		$('.above').removeClass('graph_element');
	}
	
	if( $('.below').hasClass('graph_element') ){
		$('.below').removeClass('graph_element');
	}
	
});


$('.skill').on('click', function(){
	
	if ( $('.in_numbers').css('display') == 'none' ){
		
		$('.in_numbers').show();
		$('.in_words').hide();
	
	}else{
	
		$('.in_words').show();
		$('.in_numbers').hide();
		
	}	

});
	

$('#league_tools').on('click', function(){
	
	if ( $('.brogey_sub_menu').css('display') == 'none' ){
		
		$('.brogey_sub_menu').show();
	
	}else{
	
		$('.brogey_sub_menu').hide();
		
	}
		
} );




$('.scorecard_list_row').on( 'click', function(){

	var roundid = $(this).attr('id');
	
	$(location).attr( 'href', 'scorecard.php?roundid='+roundid );

});


$.get( "league_invite_notification.php", function( data ) {
  
  $("#invite_notifications").html(data);
  
});


	
$( window ).resize(function(){ 

	var chart_holes = $('#chart_div').attr('class');
	
	load_page_data(chart_holes);

});

/*

$('#chart_div').on('click', function(){
		
	if( $(this).attr('class') == '18' ){
		
		load_page_data(9);
	
	}else{
		
		load_page_data(18);
		
	}
	

});

*/






/*
$.get( "scorecard_list_find_chart_data.php", function( data ) {
  
  $("#test").html(data);
  
});
*/


</script>





</body>

</html>