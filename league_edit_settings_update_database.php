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


$user_id	= mysqli_real_escape_string( $link, $_SESSION['id'] );
$league_name 	= $_POST['league_name'];
$handicap	= $_POST['handicap'];
$season_weeks 	= $_POST['season_weeks'];
$frequency 	= $_POST['frequency'];
$holes	 	= $_POST['holes'];
$day 		= $_POST['date_day'];
$month 		= $_POST['date_month'];
$year 		= $_POST['date_year'];
$league_id	= $_POST['league_id'];

$parsed_date = new DateTime( "$year-$month-$day" );
        
$start_date = date_format($parsed_date, 'Y-m-d');

$date = new DateTime($start_date);

$new_date = date_modify($date, '+'.$season_weeks.' week');

$end_date = date_format($new_date, 'Y-m-d');



$run_update_league_settings_query = mysqli_query( $link, "UPDATE bg_app_league "
							."SET league_name = '$league_name', "
							."use_handicaps = '$handicap', "
							."weeks = '$season_weeks', "
							."frequency_by_weeks = '$frequency', "
							."holes = '$holes', "
							."start_date = '$start_date', "
							."end_date = '$end_date' "
							."WHERE league_id = '$league_id'" );
	




?>