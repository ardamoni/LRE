<?php
	session_start();
	
	// Access the library of connnection and functions
	require_once( "../../lib/configuration.php" );

	$roleid	= $_SESSION['user']['roleid'];	
	
	// TODO: check the user's authorisation - role must be administrator
//	$sql = mysql_query("SELECT `roleid` FROM `usr_user_role` WHERE `username` = '".$roleid."' AND `roleid` = '0'");
      	
//	if( mysql_num_rows($sql) != 1 ) exit;

	// update the Revenue collection & planning years.
	if(isset($_POST['AdminBaseyear']))
	{
		mysql_query("UPDATE `system_config` SET `value` = '".$_POST['AdminBaseyear']."' WHERE `variable` = 'RevenueCollectionYear'");
		mysql_query("UPDATE `system_config` SET `value` = ('".$_POST['AdminBaseyear']."'+1) WHERE `variable` = 'RevenuePlanningYear'");
	}
?>

<html>
    <head>
		<title>Administration Module</title>	
    </head>
        
	<FRAMESET rows="160, 100%" FRAMEBORDER = "NO" FRAMESPACING = "0" BORDER = "0" NORESIZE SCROLLING = "no">
		<FRAME src="header.php" name = "top">
		<FRAMESET cols="440, 100%" FRAMEBORDER = "NO" FRAMESPACING = "0" BORDER = "0" NORESIZE SCROLLING = "no">
			<FRAME src="left.php" name = "menu">
			<FRAME src="configuration.php" name = "content"> 
		</FRAMESET>
    </FRAMESET><noframes></noframes>

</html>