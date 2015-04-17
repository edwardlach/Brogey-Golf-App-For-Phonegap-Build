<?php
session_start();

date_default_timezone_set('America/New_York');

include "simple_functions.php";
$link = connect_to_database();

$league_id = $_SESSION['league_id'];
$level_selected = $_POST['level'];


$league_participants_query = mysqli_query( $link,
	"SELECT * FROM bg_app_league_participants "
	."WHERE league_id = '$league_id' "
	."AND is_confirmed = '1'"
);

$league_participants = array();

while( $league_participants_row = mysqli_fetch_array( $league_participants_query, MYSQLI_ASSOC ) ){

	array_push( $league_participants, $league_participants_row );
	
}

$chart_data = array();

foreach( $league_participants as $particpant ){
	
	$brogey_id = $particpant['user_id'];
	
	$brogey_handicaps_query = mysqli_query( $link,
		"SELECT * FROM bg_app_handicap_record "
		."WHERE user_id = '$brogey_id' "
		."ORDER BY record_date DESC"
	);
	
	$brogey_handicaps = array();

	while( $brogey_handicaps_row = mysqli_fetch_array( $brogey_handicaps_query, MYSQLI_ASSOC ) ){
	
		array_push( $brogey_handicaps, $brogey_handicaps_row );
		
	}
	
	$brogey_handicap = $brogey_handicaps[0]['handicap'];
	
	
	$brogey_name_query = mysqli_query( $link,
		"SELECT full_name FROM bg_app_users "
		."WHERE user_id = '$brogey_id'"
	);
	$brogey_name_row = mysqli_fetch_row( $brogey_name_query );
	$brogey_name = $brogey_name_row[0];
	
	if( $brogey_handicap <= 0 ){
		$level = 5;
	}elseif( $brogey_handicap <= 5 ){
		$level = 4;
	}elseif( $brogey_handicap <= 12 ){
		$level = 3;
	}elseif( $brogey_handicap <= 22 ){
		$level = 2;
	}elseif( $brogey_handicap > 22 ){
		$level = 1;
	}
	
	if( $level == $level_selected ){
		array_push( $chart_data, array( 
						'name'		=> $brogey_name, 
						'handicap'	=> $brogey_handicap,
						'level'		=> $level
					)
		);
	}

}


if( $level_selected == 1 ){
	$level_color = '#004472';
	$level_font = 'white';
}elseif( $level_selected == 2 ){
	$level_color = '#0071be';
	$level_font = 'white';
}elseif( $level_selected == 3 ){
	$level_color = '#0b9cff';
	$level_font = 'rgb(252,252,252)';
}elseif( $level_selected == 4 ){
	$level_color = '#57bbff';
	$level_font = '#333333';
}elseif( $level_selected == 5 ){
	$level_color = '#a4daff';
	$level_font = '#333333';
}


$level_brogeys_html .= <<<EOHTML

	<table class="level_brogeys" style="background:$level_color; color:$level_font">

EOHTML;

foreach( $chart_data as $data ){
	
	$level_name = $data['name'];
	$level_handicap = $data['handicap'];
	
	if($level_handicap == ""){
		$level_handicap = "0";
	}
	
	$level_brogeys_html .= <<<EOHTML
		
		<tr>
			<td>$level_name</td>
			<td>$level_handicap</td>
		</tr>
EOHTML;

}

$level_brogeys_html .= <<<EOHTML
	
	</table>

EOHTML;

echo $level_brogeys_html;

?>