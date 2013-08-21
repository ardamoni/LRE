<?php
	
	/*
	 *	User Class
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	
	class User
	{

		/*
		 *	Get User Information
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */	
		function GetInformation($user = "", $f = "")
		{
			$q = mysql_query("SELECT * FROM `usr_users` WHERE `username` = '".$user."'");
			$r = mysql_fetch_array($q);
			
			return $r[$f];
		}
		
		
		/*
		 *	Update User Information
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */	
		function UpdateInformation($user = "", $f = "", $v = "")
		{
			mysql_query("UPDATE `usr_users` SET `".$f."` = '".$v."' WHERE `username` = '".$user."'");
		}
		
				
		/*
		 *	Get User Role
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */	
		function GetUserRole($user = "", $f = "")
		{
			$q = mysql_query("SELECT * FROM `usr_user_role` WHERE `username` = '".$user."' AND `roleid` = '".$f."'");
			
			if(mysql_num_rows($q) == 1)			
				return TRUE;
			else
				return FALSE;
		}
				
		
		/*
		 *	User Login
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */
		function UserLogin($user = "", $pass = "")
		{		
			/*
			 *	Log In and Start The System
			 */ 
			echo '<meta http-equiv="REFRESH" content="0;url=dbgeojsonpoly.html">';
			exit;		
		}		
	}


?>