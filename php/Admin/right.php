<?php

	require_once( "../lib/configuration.php"	);
	
	// Left side
	echo "Users by District";			
	
	$q1 = mysql_query("SELECT * FROM `area_district` WHERE `districtid` > '0' ORDER BY `districtid`");		
	while( $r1 = mysql_fetch_array($q1) )
	{
		echo '["||'.$r1['districtid']." - ".$r1['district_name'].'"],';

		$q2 = mysql_query("SELECT * FROM 	`usr_user_district` `b`, 
											`usr_users` `s` 
											
									WHERE 	`b`.`username` = `s`.`username` AND 
											`boid` = '".$r1['districtid']."' 
											
									ORDER BY `s`.`username` ASC");		

		while( $r2 = mysql_fetch_array($q2) )
		{
			echo '["|||'.$r2['name']." - ".$r2['username'].'"],';
		
		}
	}
	
	
	if(isset($_POST['CreateUser']))
	{
		mysql_query("INSERT INTO `usr_users` (	`username`, 
												`pass`,
												`adminpass`,
												`masterpass`,
												`title`,
												`name`, 
												`position`,
												`email`,
												`phone`,
												`activestatus`,
												`loged`) 
									VALUES (	'".$_POST['username']."', 
												'".md5($_POST['username'])."', 
												'".md5($value1)."', 
												'".md5($value2)."', 
												'".$_POST['title']."', 
												'".$_POST['name']."', 
												'".$_POST['position']."',
												'".$_POST['email']."',
												'".$_POST['phone']."',												
												'".$_POST['activestatus']."',
												'".$_POST['loged']."'
												)");
																			
		echo "<META HTTP-EQUIV=\"Refresh\"CONTENT=\"0; URL=?users=".$_POST['user']."\">";
	}


	if(isset($_GET['resetpass']) )
		mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['username'])."' WHERE `username` = '".$_GET['username']."'");



	if(isset($_POST['Save']) )
		mysql_query("UPDATE `usr_users` SET		`title` 	= 	'".$_POST['title']."',  
												`name` 		= 	'".$_POST['name']."', 
												`position` 	= 	'".$_POST['position']."',  
												`email` 	= 	'".$_POST['email']."', 												
												`phone` 	= 	'".$_POST['phone']."',  												
												`active` 	= 	'".$_POST['activestatus']."'	
										WHERE 	`username` 	= 	'".$_GET['username']."'");


/*										
	if(//* Add role  )
		mysql_query("INSERT INTO `usr_user_role` VALUES ( '".$_GET['username']."', '".$_POST['role']."')");


	if(//* Modify Role )
		mysql_query("UPDATE `usr_user_role` SET `roleid` = '".$_GET['role']."' WHERE `username` = '".$_GET['username']."' ");

*/
	$q = mysql_query("SELECT `u`.* FROM `usr_users` `u` WHERE `u`.`username` = '".$_GET['username']."'");						
	$r = mysql_fetch_array($q);
?>
