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


			$headers = apache_request_headers();
			$ip = getenv ('REMOTE_ADDR');
			$ipname = gethostbyaddr ($ip);
			foreach ($headers as $header => $value) {
				if ($header=='Accept-Language') {$acceptlanguage=$value;}
				if ($header=='Accept') {$accept=$value;}
				if ($header=='User-Agent') {$useragent=$value;}
				if ($header=='Host') {$host=$value;}
			}
			$today = date("Y-m-d H:i:s");

			if( mysql_num_rows($q) == 1 )
			{
				$username 	= $r['username'];
				$name		= $r['name'];
				$comment	= 'user exists in database';
			}else{
				$username	= $user;
				$name		= $pass; //log what password was entered
				$comment	= 'user does not exist in database - name has stored the password';
			}

			// update log
			//mysql_query("UPDATE `usr_users` SET `loged` = '1' WHERE `username` = '".$u."'");
				$insert = array(
					'username' => $username,
					'name' => $name,
					'remoteip' => $ip,
					'remotehostname' => $ipname,
					'acceptlanguage' => $acceptlanguage,
					'accept' => $accept,
					'useragent' => $useragent,
					'host' => $host,
					'time' => $today,
					'comments' => $comment
					);
				$conn = new db(cDsn, cUser, cPass);
				$result = $conn->insert("usr_user_accesslog", $insert);

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


				/*
	 			 *	Log In and Start The System
		 		 */
		 		 if ($role['roleid']==200){
					echo '<meta http-equiv="REFRESH" content="0;url=LREstats.php">';
				}else {
					echo '<meta http-equiv="REFRESH" content="0;url=LREinit.php">';
				}
				exit;

			}
		} // end of function UserLogin
	} // end of class USER


?>