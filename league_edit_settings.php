<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}

date_default_timezone_set('America/New_York');


include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);

$user_id = $_SESSION['id'];

$display_date_raw = new DateTime("now");
	
$display_date = date_format($display_date_raw, 'M d, Y');


$today_date_time = new DateTime('now');
$today = date_format($today_date_time, 'Y-m-d');

$user_admin_leagues_query = mysqli_query( $link, "SELECT * FROM bg_app_league "
					  ."WHERE league_admin_id = '$user_id' "
					  ."AND start_date > '$today'" ); 

$user_admin_leagues = array();

while( $user_admin_leagues_row = mysqli_fetch_array( $user_admin_leagues_query, MYSQLI_ASSOC ) ){

	array_push( $user_admin_leagues, $user_admin_leagues_row );
	
}


if( $user_admin_leagues ){

	foreach( $user_admin_leagues as $admin_league ){
	
		$league_name	= $admin_league['league_name'];
		$league_id	= $admin_league['league_id'];
		$handicap	= $admin_league['use_handicaps'];
		
		if( $handicap == '1'){
			$handicap_yn = "yes";
		}else{
			$handicap_yn = "no";
		}
		
		$weeks		= $admin_league['weeks'];
		$frequency	= $admin_league['frequency_by_weeks'];
		$rounds		= $weeks/$frequency;
		$holes		= $admin_league['holes'];
		$date		= $admin_league['start_date'];
		$end_date	= $admin_league['end_date'];
		$date_split	= explode("-", $date);
		$day		= $date_split[2];
		$monthNum  	= $date_split[1];
		$dateObj   	= DateTime::createFromFormat('!m', $monthNum);
		$month_text 	= $dateObj->format('F'); 
		$year		= $date_split[0];
		
		$display_start_date_raw = new DateTime($date);
		$display_start_date = date_format($display_start_date_raw, 'M d, Y');
		
		$display_end_date_raw = new DateTime($end_date);
		$display_end_date = date_format($display_end_date_raw, 'M d, Y');
		
		
		$edit_league_html .= <<<EOHTML
		
		<div class="league_edit_accordion">
			<div class="league_edit_section">
		        <a class="league_edit_section_title" href="#section_$league_id">
		        		<div id="section_$league_id" class="edit_league_title">$league_name</br><span class="list_date_range">$display_start_date - $display_end_date</span></div><!--end .edit_league_title-->
		        </a>
		        
		        
		        
		       <div id="section_$league_id" class="league_edit_section_content"> 
		        
				<form action="league_setup.php" id="league_setup" method="post">
					
					<div class="form-group">
						<label for="league_name">League Name</label>
				        		<input id="league_name" class="$league_id" name="league_name" type="text" pattern=".{1,100}" value="$league_name" required />
				        	</div>
				        	<div class="form-group">
					        <label for="handicap_yes_no">Handicaps:</label>
						<select name="handicap_yes_no" id="handicaps_yes_no" class="$league_id" required>
							<option value="$handicap">$handicap_yn</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select> 
					</div>
					<div class="form-group">
					        <label for="season_weeks">Number of Rounds:</label>
						<input type="range" name="season_weeks" id="season_weeks" class="$league_id" value="$rounds" min="1" max="52" step="1" required/>
					</div>
					<div class="form-group">
					        <label for="league_frequency">Length of a Round in Weeks (How often are scores accepted and points awarded):</label>
						<input type="range" name="league_frequency" id="league_frequency" class="$league_id" value="$frequency" min="1" max="5" step="1" required />
					</div>
					<div class="form-group">			       
					        <label for="holes">League Holes:</label>
						<select name="holes" id="holes" class="$league_id" required>
							<option value="$holes">$holes</option>
							<option value="9">9</option>
							<option value="18">18</option>
						</select> 
					</div>
					<div class="form-group">			       
					        <label for="date_day">Start Day:</label>
					        <input type="range" name="date_day" id="date_day" class="$league_id" value="$day" min="1" max="32" step="1" required/>
					</div>
					<div class="form-group">   	 
						<label for="date_month">Start Month:</label>
					        <select id="date_month" class="$league_id" name="date_month" required>
					            	<option value="$monthNum">$month_text</option>
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
						<select id="date_year" class="$league_id" name="date_year">
							<option value="$year">$year</option>
						        <option value="2015">2015</option>
						        <option value="2014">2014</option>
						        <option value="2013">2013</option>
					        </select>  
					</div>
				       
				        <div class="form-group">
				        		<input class="btn btn-default submit_league_info" id="$league_id" type="submit" name="submit_league_info" value="Edit League" />
				        </div>	
				</form>
			</div><!--end .league_edit_section_content-->
			</div><!--end .league_edit_section-->
		</div><!--end .league_edit_accordion-->		
	
EOHTML;

	

	}
	
	$edit_league_html .= <<<EOHTML
		
		<div class="swipe_instructions">To Delete League<span class="swipe_arrow">&emsp;<i class="fa fa-chevron-left"></i>&emsp;</span>Swipe</div>
	
EOHTML;

}else{

	$edit_league_html .= <<<EOHTML
		
		<div class="no_league_invites_watermark">No editable leagues, but you are nonetheless a gorgeous human being!</div>

EOHTML;

}









?>



<!doctype html>

<html>

<head>

	<title>Brogey Golf</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
	
		$(document).bind("mobileinit", function () {
		    $.mobile.ajaxEnabled = false;
		});
	
	</script>			
	<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	
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

<div data-role="page" id="edit_league_settings_content">
	
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
					<span id="site_title"><?php echo $display_date; ?></span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="edit_league_settings_content">

		
		<div id="settings_html"><?php echo $edit_league_html; ?></div>
		
		
	</div><!-- /content -->
	
	
</div><!-- /page -->



<script type="text/javascript">

$('#league_tools').on('click', function(){
	
	if ( $('.brogey_sub_menu').css('display') == 'none' ){
		
		$('.brogey_sub_menu').show();
	
	}else{
	
		$('.brogey_sub_menu').hide();
		
	}
		
} );


$.get( "league_invite_notification.php", function( data ) {
  
  $("#invite_notifications").html(data);
  
});


$("#settings_html").on("click", ".submit_league_info", function(){
	
	var league_id		= $(this).attr('id');
	var league_name		= $("#league_name." + league_id).val();
	var handicap		= $("#handicaps_yes_no." + league_id).val();
	var number_of_rounds	= $("#season_weeks." + league_id).val();
	var frequency		= $("#league_frequency." + league_id).val();
	var season_weeks	= number_of_rounds * frequency;
	var holes		= $("#holes." + league_id).val();
	var date_day		= $("#date_day." + league_id).val();
	var date_month		= $("#date_month." + league_id).val();
	var date_year		= $("#date_year." + league_id).val();

	
	$.post("league_edit_settings_update_database.php",
	  {
	    league_name:league_name,
	    handicap:handicap,
	    season_weeks:season_weeks,
	    frequency:frequency,
	    holes:holes,
	    date_day:date_day,
	    date_month:date_month,
	    date_year:date_year,
	    league_id:league_id
	  },
	  function(data,status){
	 
	  	location.reload();		
	    
	  });
	  
	event.preventDefault(); 

});


$(document).ready(function() {
    function close_accordion_row() {
        $('.league_edit_accordion .league_edit_section_title').removeClass('active');
        $('.league_edit_accordion .league_edit_section_content').slideUp(300).removeClass('open');
    }
 
    $('.league_edit_section_title').click(function(event) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');
 	
 	var currentClass = $(this).attr('class');
 	
        if( $(this).hasClass("active") ) {
            close_accordion_row();
        }else {
            close_accordion_row();
 
            // Add active class to section title
            $(this).addClass('active');
            // Open up the hidden content panel
            $('.league_edit_accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
        }
 
        event.preventDefault();
    });
});


$('.edit_league_title').on( 'swipeleft', function(){
	
	var id_conc = $(this).attr('id');
	var id_split = id_conc.split('_');
	var leagueid = id_split[1];
	
	$(this).css('background-color', 'rgba(253,228,225, .95) !important');
	
	if (confirm('Are you sure you would like to delete this unstarted league?')) {
		
		$.post("league_delete_ajax.php",
		  {
		    leagueid:leagueid
		  },
		  function(data,status){
		 
		 	location.reload();
		 	
		  });
		  
		
	}else{
	
		$(this).css('background-color', 'transparent');
	
	}

});





</script>





</body>

</html>