<?php
session_start();

session_unset();

header( 'Location: login.php' );


?>

<!doctype html>

<html>

<head>

	<title>Brogey Golf</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>	
	<script src="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

</head>

<body> 

<div data-role="page" id="login">
	
	<div class="panel left" data-role="panel" data-position="left" id="brogey_menu">
		<ul id="brogey_menu">
			<a id="menu_link" href="brogeygolf.com"><li id="brogey_menu">Home</li></a>
			<a id="menu_link"><li id="brogey_menu">Add Round</li></a>
			<a id="menu_link"><li id="brogey_menu">Continue Round</li></a>
			<a id="menu_link"><li id="brogey_menu">League Setup</li></a>
			<a id="menu_link"><li id="brogey_menu">Accept League Invite</li></a>
			<a id="menu_link"><li id="brogey_menu">Scorecards</li></a>
			<a id="menu_link"><li id="brogey_menu">Historical Leagues</li></a>
			<a id="menu_link" href="logout.php"><li id="brogey_menu">Logout</li></a>
		</ul>
	</div><!-- /div data-role="panel" id="brogey_menu"-->

	<div data-role="header">
		<div class="site_title">
			<a id="menu_button" href="#brogey_menu">
				<div class="menu_button">
					<span id="menu_button">menu</span>
				</div>
			</a>
			<div>	
				<div id="site_title">
					<span id="site_title">Brogey Golf</span>
				</div>
			</div>
		</div>
	</div><!-- /header -->
	
	



	<div data-role="content">
		
	
		
				
	</div><!-- /content -->
	
	<div data-role="footer">
		<div class="row">
			<div <div class="col-md-10 credits">&middot; &copy; Brogey Golf 2014 &middot; Designed by Brogey Boss Ed &middot;</div>
		</div>
	</div><!-- /footer -->

</div><!-- /page -->


<script type="text/javascript">



</script>



</body>

</html>