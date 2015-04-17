<?php
session_start();

date_default_timezone_set('America/New_York');

include "simple_functions.php";
$link = connect_to_database();

$league_id = $_SESSION['league_id'];

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

	array_push( $chart_data, array( 
					'name'		=> $brogey_name, 
					'handicap'	=> $brogey_handicap,
					'level'		=> $level
				)
	);

}



//var_dump($chart_data);
	
echo json_encode($chart_data);



?>