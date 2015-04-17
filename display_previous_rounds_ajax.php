<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


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




$user_id   = $_SESSION['id'];
$league_id = $_GET['leagueid'];

if( !$league_id ){

	$league_id = $_SESSION['league_id'];

}


$league_name_query = mysqli_query( $link, "SELECT league_name FROM bg_app_league WHERE league_id = '$league_id'" );
$league_name_row = mysqli_fetch_row( $league_name_query );
$league_name = $league_name_row[0];



include "display_leaderboard.php";

$results = create_leaderboard($league_id, $user_id, $historical);


$previous_round_query = mysqli_query( $link, "SELECT * FROM bg_app_league_points "
					    ."WHERE user_id = '$user_id' "
					    ."AND league_id = '$league_id' "
					    ."ORDER BY round_number ASC" );

$previous_round = array();

while( $previous_round_row = mysqli_fetch_array( $previous_round_query, MYSQLI_ASSOC ) ){

	array_push( $previous_round, $previous_round_row );
	
}


$previous_round_html = <<<EOHTML
<table  id="previous_round_list" >
<tbody>
    	<tr class="previous_round_list_row_title">
    		<th class="list_round">Round</th>
    		<th class="list_position">Position</th>
    		<th class="list_total_points">Points</th>
    		<th class="direction_arrow">&#x232a;</th>
    	</tr>
   
EOHTML;

foreach( $previous_round as $row ){

	$round		= $row['round_number'];
	$position	= $row['position'];
	$match		= $row['points_from_match'];
	$leaderboard 	= $row['points_from_leaderboard'];
	$total_points	= $match + $leaderboard;
	
	$previous_round_html .= <<<EOHTML
	<tr class="previous_round_list_row" id="$round">
		<td class="list_round">$round</td>
		<td class="list_position">$position</td>
		<td class="list_total_points">$total_points</td>
		<td class="direction_arrow">&#x232a;</td>	
	</tr>
EOHTML;




}

$previous_round_html .= <<<EOHTML

</tbody>
</table>

EOHTML;

echo $previous_round_html;


?>