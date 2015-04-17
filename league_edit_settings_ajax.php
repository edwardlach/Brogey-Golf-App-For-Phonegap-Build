<?php
session_start();

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

$user_id = $_SESSION['id'];

$today_date_time = new DateTime('now');
$today = date_format($today_date_time, 'Y-m-d');

$user_admin_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league "
					  ."WHERE league_admin_id = '$user_id' "
					  ."AND start_date > '$today'" ); 

$user_admin_leagues = array();

while( $user_admin_leagues_row = mysqli_fetch_array( $user_admin_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $user_admin_leagues, $user_admin_leagues_row );
	
}


foreach( $user_admin_leagues as $admin_league ){

	$league_name	= $admin_league['league_name'];
	$handicap	= $admin_league['use_handicaps'];
	
	if( $handicap == '1'){
		$handicap_yn = "yes";
	}else{
		$handicap_yn = "no";
	}
	
	$weeks		= $admin_league['weeks'];
	$frequency	= $admin_league['frequency_by_weeks'];
	$holes		= $admin_league['holes'];
	$date		= $admin_league['start_date'];
	$date_split	= explode("-", $date);
	$day		= $date_split[2];
	$monthNum  	= $date_split[1];
	$dateObj   	= DateTime::createFromFormat('!m', $monthNum);
	$month_text 	= $dateObj->format('F'); 
	$year		= $date_split[0];
	
	$edit_league_html .= <<<EOHTML
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
		
		<form action="league_setup.php" id="league_setup" method="post">
			
			<div class="form-group">
				<label for="league_name">League Name</label>
		        		<input id="league_name" name="league_name" type="text" pattern=".{1,100}" value="$league_name" maxlength="40" required />
		        	</div>
		        	<div class="form-group">
			        <label for="handicap_yes_no">Handicaps:</label>
				<select name="handicap_yes_no" id="handicaps_yes_No" required>
					<option value="$handicap">$handicap_yn</option>
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select> 
			</div>
			<div class="form-group">
			        <label for="season_weeks">Weeks:</label>
				<input type="range" name="season_weeks" id="season_weeks" value="$weeks" min="1" max="52" step="1" required/>
			</div>
			<div class="form-group">
			        <label for="league_frequency">Frequency of Matches in Weeks (How often are scores accepted and points awarded):</label>
				<input type="range" name="league_frequency" id="league_frequency" value="$frequency" min="1" max="5" step="1" required />
			</div>
			<div class="form-group">			       
			        <label for="holes">League Holes:</label>
				<select name="holes" id="holes" required>
					<option value="$holes">$holes</option>
					<option value="9">9</option>
					<option value="18">18</option>
				</select> 
			</div>
			<div class="form-group">			       
			        <label for="date_day">Start Day:</label>
			        <input type="range" name="date_day" id="date_day" value="$day" min="1" max="32" step="1" required/>
			</div>
			<div class="form-group">   	 
				<label for="date_month">Start Month:</label>
			        <select id="date_month" name="date_month" required>
			            	<option value="$month">$month_text</option>
				        <option value="1">January</option>
				        <option value="2">February</option>
				        <option value="3">March</option>
				        <option value="4">April</option>
				        <option value="5">May</option>
				        <option value="6">June</option>
				        <option value="7">July</option>
				        <option value="8">August</option>
				        <option value="9">September</option>
				        <option value="10">October</option>
				        <option value="11">November</option>
				        <option value="12">December</option>
			        </select>  
			</div>
			<div class="form-group">                             
				<label for="date_year">Start Year:</label>
				<select id="date_year" name="date_year">
					<option value="$year">$year</option>
				        <option value="2015">2015</option>
				        <option value="2016">2016</option>
				        <option value="2017">2017</option>
			        </select>  
			</div>
		       
		        <div class="form-group">
		        		<input class="btn btn-default" id="submit_league_info" type="submit" name="submit_league_info" value="Create League" />
		        </div>	
		</form>
	
EOHTML;

	

}

echo $edit_league_html;



?>