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


$id = mysqli_real_escape_string( $link, $_SESSION['id'] );

$find_invites_query = mysqli_query( $link, "SELECT * FROM bg_app_league_participants "
					  ."LEFT JOIN bg_app_league ON bg_app_league_participants.league_id=bg_app_league.league_id "
					  ."WHERE bg_app_league_participants.user_id = '$id' "
					  ."AND bg_app_league_participants.is_confirmed = '0'" );

$invites = array();

while( $invites_row = mysqli_fetch_array( $find_invites_query, MYSQLI_ASSOC ) ){

	array_push( $invites, $invites_row );
	
}

if( $invites ){

$accept_invite_html = <<<EOHTML

	<!-- <div class="info_message"><img src="/images/information3.png" style="width:20px;height:20px">    Swipe right to accept, left to reject</div> -->
	<table  id="league_invite_list" >
		<tbody>
		    	<tr class="league_invite_list_row_title">
		    		<th class="list_league_name">League</th>
		    		<th class="list_use_handicap">Handicap</th>
		    		<th class="list_holes">Holes</th>
		    	</tr>
   
EOHTML;

foreach( $invites as $row ){

	$league_name	= $row['league_name'];
	$start_date	= $row['start_date'];
	$end_date	= $row['end_date'];
	$use_handicaps	= $row['use_handicaps'];
	$holes		= $row['holes'];
	$frequency	= $row['frequency_by_weeks'];
	$league_id	= $row['league_id'];
	
	if( $frequency == 1 ){
	
		$display_frequency = "every week";
	
	}else{
	
		$display_frequency = "every ".$frequency." weeks";
		
	}
	
	if( $use_handicaps == '0' ){
	
		$handicaps = "No";
		
	}else{
	
		$handicaps = "Yes";
	
	}
	
	$display_start_date_raw = new DateTime($start_date);
	
	$display_start_date = date_format($display_start_date_raw, 'M d, Y');
	
	$display_end_date_raw = new DateTime($end_date);

	$display_end_date = date_format($display_end_date_raw, 'M d, Y');

		$accept_invite_html .= <<<EOHTML
	
			<tr class="league_invite_list_row" id="$league_id">
				<td class="list_league_name">$league_name</br><span id="list_date">$display_start_date - $display_end_date</span></br><span id="list_date">Round due $display_frequency</span></td>
				<td class="list_use_handicap">$handicaps</td>
				<td class="list_holes">$holes</td>	
			</tr>
EOHTML;

}

	$accept_invite_html .= <<<EOHTML
	
		</tbody>
	</table>
	<div class="swipe_instructions">Reject&emsp;
		<span class="swipe_arrow"><i class="fa fa-chevron-left"></i>&emsp;</span>Swipe&emsp;
		<span class="swipe_arrow"><i class="fa fa-chevron-right"></i>&emsp;</span>Accept
	</div>

EOHTML;



echo $accept_invite_html;

}else{

$accept_invite_html = <<<EOHTML

<div class="no_league_invites_watermark">Sorry Brogey, no invites to accept at this time.  Time to start a new league!</div>

EOHTML;

echo $accept_invite_html;

}



?>