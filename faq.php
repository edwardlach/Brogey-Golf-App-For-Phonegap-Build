<?php
session_start();

if(!$_SESSION['id']){

header( "Location: login.php" );

}


$user_id = $_SESSION['id'];

include "simple_functions.php";
$link = connect_to_database();
$user_name = user_name($link);



?>

<!doctype html>

<html class="email_invite">

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
		
		
		$( document ).ready(function() {

			$('.new_round_button').insertBefore('.top_wood_panel');
			
			$('.scorecard_button').insertBefore('.top_wood_panel');
			
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

<body class="email_invite"> 

<div data-role="page" id="email_invite">
	
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
					<span id="site_title">Brogey Q&A</span>
				</div>
			</div>	
		</div>
	</div><!-- /header -->
	
	


	<div data-role="content" class="faq_content">
	
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_1">How do you start a Brogey League?</a>
		        <div id="question_1" class="leaderboard_section_content answer">1. Click “League Setup”</br>
											2. Click “Start a League”</br>
											3. Fill out the page</br>
											4. Send your friends some invites</br></br>
											*If your friends aren’t Brogeys yet send 
											them an email from the “Invite a Friend” 		
											Section of the Add a member page</br></br>
											That’s All folks!  You’ve started a Brogey League!
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
	
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_2">How do you compete in a League?</a>
		        <div id="question_2" class="leaderboard_section_content answer">
		        	There are two components to scoring in your league</br></br>
 				<p>1. Stroke Play - your position on the round leaderboard.  Your score from par is adjusted using the Brogey Equation (make that a link to what is the Brogey Equation).  The Leaderboard is arranged in ascending order (lowest score wins)
 				</p>
				<p>2. Match Play - holes are decided based on matching hole handicaps (lowest vs lowest through highest vs highest). 
				 A Brogey earns a point for a hole where they bested their opponent.  No points are awarded for tied holes.</br></br>
				 <a class="leaderboard_section_title" href="#question_10">Click here to see how points are awarded</a></br></br>
				Points are added together for the Season leaderboard which changes weekly
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_3">What do I have to do to play in the league?</a>
		        <div id="question_3" class="leaderboard_section_content answer">
		        	<p>All you need to do is input your round information through the 'Add a Round' button on the homepage or in the menu.  The app is programmed to use your lowest score that meets all the leagues criteria (It falls within the accepted time frame for a round, it meets the league hole requirement, and it hasn’t already been used for the particular league).
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_4">Do I submit all of my rounds?</a>
		        <div id="question_4" class="leaderboard_section_content answer">
		        	<p>Yes.  The league will be more accurate and enjoyable.  Unused scores are still applicable towards the round immediately proceeding, which is convenient if you can’t play during a round or are in a bit of a slump compared to the round before.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_5">What is the equation?</a>
		        <div id="question_5" class="leaderboard_section_content answer">
		        	<p>We use the official USGA handicap equation to calculate handicaps, if you’re interested it’s explained here <Link to USGA handicap equation> but it’s super boring!
We then use our Brogey equation to adjust your handicap in leagues where handicaps are used.  Our equation is simple:  course-rating/113 * handicap, where 113 is the standard average of course ratings.
				</p>
			</div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_6">What is a handicap?</a>
		        <div id="question_6" class="leaderboard_section_content answer">
		        	<p>It is a calculated formula that approximates the strokes above or below par a player might be able to play based on the ten best score of their last 20 rounds.  If you have fewer than 20 submitted rounds, the scores used to calculate your handicap will be less than 10 in accordance with how many total rounds you have available.  The nitty gritty used to calculate this number can be found <a href="http://www.usga.org/handicapfaq/handicap_answer.asp?FAQidx=4">here</a>.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_7">Do I need a handicap to start?</a>
		        <div id="question_7" class="leaderboard_section_content answer">
		        	<p>Nope, for the purposes of Brogey Golf your handicap is calculated by the app using only rounds submitted to the app.  Accuracy improves the more you submit rounds, we highly recommend submitting at least five rounds before the start of your league.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_8">What do I need for a Brogey Handicap?</a>
		        <div id="question_8" class="leaderboard_section_content answer">
		        	<p>Nothing!  Well, almost nothing.  You need to submit rounds for us to use to calculate your handicap.  An accurate handicap requires at least five rounds, we start calculating your handicap after your first round.  Your handicap will become truer the more you golf it up with us Brogeys.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_9">What’s the point of the Brogey Equation?</a>
		        <div id="question_9" class="leaderboard_section_content answer">
		        	<p>This is our solution to standardizing course difficulties and player ability.  The harder courses lead to a higher multiplier and easier courses a lower multiplier.  The effect of this multiplier is more pronounced for someone with a higher handicap than for someone with a lower handicap, basic math y’all.  This means that a poor golfer playing a difficult course will be given more strokes than a player of higher skill.  This creates an even playing field regardless of skill level and/or course difficulty.  Don’t be afraid to invite shitty golfers or your scratch buddies to the same league, and don’t be afraid to play the Oakmonts and Pebble Beaches of the world.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_10">How are points awarded?</a>
		        <div id="question_10" class="leaderboard_section_content answer">
		        	<p>Assuming everyone has participated in a week, last place will receive 0 points.  Points received will increment by 5 for everyone in the bottom half.  Points will increment by 10 for everyone in the top half until positions 1 and 2.  2nd place receives 30 points more than 3rd place and 1st place receives twice as many points as 2nd.  If there are some no shows, the last person of people who submitted scores receives 5 points and it goes from there.
		        	</p>
		        		<table>
		        			<tr>
		        				<th>Position</th>
		        				<th>Trend</th>
		        				<th>Points Earned</th>
		        			</tr>
		        			<tr>
		        				<td>1.</td>
		        				<td>(X2)</td>
		        				<td>150</td>
		        			</tr>
		        			<tr>
		        				<td>2.</td>
		        				<td>(+30)</td>
		        				<td>75</td>
		        			</tr>
		        			<tr>
		        				<td>3.</td>
		        				<td>(+10)</td>
		        				<td>45</td>
		        			</tr>
		        			<tr>
		        				<td>4.</td>
		        				<td>(+10)</td>
		        				<td>35</td>
		        			</tr>
		        			<tr>
		        				<td>5.</td>
		        				<td>(+5)</td>
		        				<td>25</td>
		        			</tr>
		        			<tr>
		        				<td>6.</td>
		        				<td>(+5)</td>
		        				<td>20</td>
		        			</tr>
		        			<tr>
		        				<td>7.</td>
		        				<td>(+5)</td>
		        				<td>15</td>
		        			</tr>
		        			<tr>
		        				<td>8.</td>
		        				<td>(+5)</td>
		        				<td>10</td>
		        			</tr>
		        			<tr>
		        				<td>9.</td>
		        				<td>(+5)</td>
		        				<td>5</td>
		        			</tr>
		        			<tr>
		        				<td>10.</td>
		        				<td>(0)</td>
		        				<td>0</td>
		        			</tr>
		        		</table>
		        		</br>
		        	<p>For matchups, the winner gets 50 points, the loser gets nothing.  In the event of a tie the points are split.  If neither player completed a round for the week there are no points awarded.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
		
		<div class="leaderboard_accordion">
		    <div class="leaderboard_section">
		        <a class="leaderboard_section_title question" href="#question_11">How do you stop people from cheating?</a>
		        <div id="question_11" class="leaderboard_section_content answer">
		        	<p>We simply can’t.  Handicaps will trend downward if someone is continually low balling their scores which hinders a players ability to cheat.  Also, cheating can be very noticeable when your shitty golfer friend suddenly starts shooting lower than expected scores.  Ultimately these leagues are based around playing with your friends, so remember the golden rule, “Don’t fuck your friends unless it’s consensual”.
		        	</p>
		        </div><!--end .leaderboard_section_content-->
		    </div><!--end .leaderboard_section-->
		</div><!--end .leaderboard_accordion-->
	
		
		
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


$(document).ready(function() {
    function close_accordion_row() {
        $('.leaderboard_accordion .leaderboard_section_title').removeClass('active');
        $('.leaderboard_accordion .leaderboard_section_content').slideUp(300).removeClass('open');
    }
 
    $('.leaderboard_section_title').click(function(event) {
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
            $('.leaderboard_accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
        }
 
        event.preventDefault();
    });
});






</script>





</body>

</html>