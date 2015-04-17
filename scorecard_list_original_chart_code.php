<?php
session_start();

date_default_timezone_set('America/New_York');


$host="Localhost"; // Host name 
$username="brogeygo"; // Mysql username 
$password="EdandTylershotpar$100"; // Mysql password 
$db_name="brogeygo_wor1"; // Database name 
$tbl_name="bg_app_users"; // Table name 

// Connect to server and select database.
$link = mysqli_connect($host, $username, $password, $db_name);

if (mysqli_connect_error()) {

 	 die("Could not connect to database");

}

$query_user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );


/* query to pull all of the scorecards successfully submitted */

$scorecard_list_query = "SELECT * FROM `bg_app_rounds` "
			."WHERE `user_id` = '$query_user_id' "
			."AND `is_complete` = '1' "
			."ORDER BY `start_date` ASC";

$run_scorecard_list_query = mysqli_query( $link, $scorecard_list_query );

$scorecard_list = array();

while( $scorecard_list_row = mysqli_fetch_array( $run_scorecard_list_query, MYSQLI_ASSOC ) ){

	array_push( $scorecard_list, $scorecard_list_row );
	
}


$chart_data = array();

foreach( $scorecard_list as $row ){

	$date		= $row['start_date'];
	$front_back	= $row['front_back_both'];
	$front_score 	= $row['front_score'];
	$back_score	= $row['back_score'];
	$front_par	= $row['front_par'];
	$back_par	= $row['back_par'];
	$course_name	= $row['course_name'];
	
	if( $front_back == "both" ){
		
		$score = $front_score + $back_score;
		$par = $front_par + $back_par;
		$holes = 18;
	
	}elseif( $front_back == "back" ){
		
		$score = $back_score;
		$par = $back_par;
		$holes = 9;
	
	}else{
	
		$score = $front_score;
		$par = $front_par;
		$holes = 9;
		
	}

	$strokes_from_par = $score - $par;

	array_push( $chart_data, array( 
					'date'		=> $date, 
					'strokes'	=> $strokes_from_par,
					'course_name'	=> $course_name,
					'score'		=> $score,
					'par'		=> $par,
					'holes'		=> $holes
				)
	);
	
}


//var_dump($chart_data);
	
echo json_encode($chart_data);



?>



<script type="text/javascript">
	
	google.setOnLoadCallback(load_page_data(18));
		
	function load_page_data(holesRequired){
	
		$.get( "scorecard_list_find_chart_data.php", function( data ) {
      			
			var jsonData = jQuery.parseJSON( data );
			
			var chartData = [["Course Name", "Score From Par"]];
			
			for (var i = 0; i < jsonData.length; i++) {
      				
      				var j = chartData.length;
      				var row = jsonData[i];
      				var date = row.date;
      				var strokes = row.strokes;
      				var course = row.course_name;
      				var par = row.par;
      				var score = row.score;
      				var holes = row.holes;
      				
      				if( holes == holesRequired ){
      					chartData[j] = [course, strokes];
      				}
      					
	      		}
	      		
	      		if( chartData.length == 1 ){
	      		
	      			chartData[1] = ["No Course Yet", 0];
	      		
	      		}
		
	               drawChart(chartData, holesRequired);
			
		});
		
	}
	
	function drawChart(chartData, holesRequired) {
	
		var data = google.visualization.arrayToDataTable(chartData);
		
		var options = {
			          title: holesRequired + ' Hole Score Trend',
			          curveType: 'function',
			          series: {
   					 0: { color: '#57bbff' }
   				  },
   				  lineWidth: 3
			        };
	
		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		
		chart.draw(data, options);
		
		$('#chart_div').attr('class', holesRequired);
	
	
	}	
	
	
	
	

</script>