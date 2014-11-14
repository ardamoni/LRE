<?php
	session_start();
	
	// Access the library of connnection and functions
	require_once( "../../lib/configuration.php" );

	// TODO: check the user's authorisation - role must be administrator
//	$sql = mysql_query("SELECT `roleid` FROM `usr_user_role` WHERE `username` = '".$_SESSION['role']."' AND `roleid` = '0'");
      	
//	if( mysql_num_rows($sql) != 1 ) exit;
	
?>

<html>
    <head>
		<title>Admin</title>
    </head>
        
	<FRAMESET rows="100, 100%" FRAMEBORDER = "NO" FRAMESPACING = "0" BORDER = "0" NORESIZE SCROLLING = "no">
    <FRAME src="header.php" name = "top">
     <FRAMESET cols="430, 100%" FRAMEBORDER = "NO" FRAMESPACING = "0" BORDER = "0" NORESIZE SCROLLING = "no">
         <FRAME src="left.php" name = "menu">
         <FRAME src="right.php" name = "content"> 
    </FRAMESET>
        
   </FRAMESET><noframes></noframes>


	

</html>