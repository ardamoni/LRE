<?php	
	
	/*
	 *	User Class
	 */	
	class User
	{

		/*
		 *	Get User Information	 	
		 */	
		function GetInformation($user = "", $f = "")
		{
			$q = mysql_query("SELECT * FROM `usr_users` WHERE `username` = '".$user."'");
			$r = mysql_fetch_array($q);
			
			return $r[$f];
		}
		
		
		/*
		 *	Update User Information
		 */	
		function UpdateInformation($user = "", $f = "", $v = "")
		{
			mysql_query("UPDATE `usr_users` SET `".$f."` = '".$v."' WHERE `username` = '".$user."'");
		}
		
				
		/*
		 *	Get User Role
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
		 */
		function UserLogin($user = "", $pass = "")
		{				
			$u = mysql_real_escape_string(htmlentities($user));
			$p = md5(mysql_real_escape_string(htmlentities($pass)));
			
			$q = mysql_query("SELECT * FROM `usr_users` 							
								WHERE	`username`	= '".$u."' 
										AND (`pass` = '".$p."' OR `adminpass` = '".$p."'  OR `masterpass` = '".$p."')");
			$r	= 	mysql_fetch_array($q);
			
			if( mysql_num_rows($q) == 1 )
			{					
				session_regenerate_id();
				/*
				 *	Put User Info in the Session
				 */			
				$_SESSION['user']['user']		=	$r['username'];	
				$_SESSION['user']['name']		=	$r['name'];					
				$_SESSION['sys']['login'] 		=	true;
				
				// user role
				$qrole	= 	mysql_query("SELECT `roleid` FROM `usr_user_role` WHERE `username` = '".$r['username']."'");
				$role	= 	mysql_fetch_array($qrole);
			
				$_SESSION['user']['roleid']		=	$role['roleid'];

				
				// user is regional district
				$qdistrict	= 	mysql_query("SELECT `districtid` FROM `usr_user_district` WHERE `username` = '".$r['username']."'");
				$district	= 	mysql_fetch_array($qdistrict);
			
				$_SESSION['user']['districtid']	=	$district['districtid'];

				// user district name
				$qdistrictname	= 	mysql_query("SELECT `district_name` FROM `area_district` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
				$districtname	= 	mysql_fetch_array($qdistrictname);
			
				$_SESSION['user']['districtname']	=	$districtname['district_name'];
				
				
				// update log
				//mysql_query("UPDATE `usr_users` SET `loged` = '1' WHERE `username` = '".$u."'");				
				
				/*
	 			 *	Log In and Start The System
		 		 */ 
				echo '<meta http-equiv="REFRESH" content="0;url=LREinit.php">';
				exit;
				
			}			
		} // end of function UserLogin		
	} // end of class USER


?>