<?php
session_start();

include "simple_functions.php";
$link = connect_to_database();


$league_id = $_POST['leagueid'];

$league_info_query = mysqli_query( $link,
	"SELECT * FROM bg_app_league "
	."WHERE league_id = '$league_id'" );

$league_info = array();


while( $league_info_row = mysqli_fetch_array( $league_info_query, MYSQLI_ASSOC ) ){
	
	array_push( $league_info, $league_info_row );

}

foreach( $league_info as $info ){

	$weeks		= $info['weeks'];
	$frequency	= $info['frequency_by_weeks'];
	$start_date_db	= $info['start_date'];

}


$today_dateTime = new DateTime('now');
$start_dateTime = new DateTime($start_date_db);
$date_difference = date_diff( $today_dateTime, $start_dateTime );
$difference_in_days = $date_difference->days;
$frequency_in_days = $frequency * 7;

if( floor($difference_in_days/$frequency_in_days) == ($difference_in_days/$frequency_in_days) && floor($difference_in_days/$frequency_in_days) != 0 ){

	$round_ended_today = 1;
	
}else{
	
	$round_ended_today = 0;

}

echo $round_ended_today;






?>