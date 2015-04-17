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

$league_id = $_SESSION['league_id_admin'];

$search = $_POST['search'];

$query_search = "%".$search."%";

$search_member_query = "SELECT * FROM `bg_app_users` WHERE "
			."`first_name` LIKE '$query_search' OR "
			."`last_name` LIKE '$query_search' OR "
			."`username` LIKE '$query_search' OR "
			."`full_name` LIKE '$query_search' "
			."LIMIT 0, 10";
			
$run_search_member_query = mysqli_query( $link, $search_member_query );

$member_search_results = array();

if( $run_search_member_query ){

	while( $member_search_results_row = mysqli_fetch_array( $run_search_member_query, MYSQLI_ASSOC ) ){
	
		array_push( $member_search_results, $member_search_results_row );
		
	}

}

if( $member_search_results ){
	
	$search_result_html = <<<EOHTML
	
		<form action="league_add_member.php" class="league_add_member" method="post">		
	
EOHTML;

	foreach( $member_search_results as $row ){
	
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$display_name = $first_name." ".$last_name;
		$user_id = $row['user_id'];
		
		$search_result_html .= <<<EOHTML
		
			<div data-role="fieldcontain" id="search_name_row">
			    <fieldset data-role="controlgroup">
				   <input type="checkbox" name="search_name[]" id="search_name" value="$user_id">  $display_name</input>
			    </fieldset>
			</div>
EOHTML;

	}
	
	$search_result_html .= <<<EOHTML
	
			<div data-role="fieldcontain">
			    <fieldset data-role="controlgroup">
				   <input type="submit" name="add_member_button" id="add_member_button" value="Add Member" />
			    </fieldset>
			</div>
		</form>
EOHTML;
	
	
}



echo $search_result_html;
	








?>