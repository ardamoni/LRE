<?php
	session_start();
	$value1 = '000001';
	$value2 = '000000';
	
?>
<!DOCTYPE html>
<html>
    <head>
		<title>Users</title>
    </head>
    <body>
        <h1 id="title">Add / Modify Users</h1>
		
	<FRAMESET cols="430, 100%" FRAMEBORDER = "NO" FRAMESPACING = "0" BORDER = "0" NORESIZE SCROLLING = "yes">
         <FRAME src="left.php" name = "left">
         <FRAME src="right.php" name = "right"> 
     </FRAMESET>	

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

<!-- Right side -->
<table cellpadding="0" cellspacing="0" border="0" width = '800' align = 'center'>
	<tr>
		<td>        
		<table width = '100%'>
			<tr>
				<td>
				   
					<h1><?php echo $r['name']; ?></h1>
					
					<form method = 'POST' action = ''>
				
					Username:<br />
					<input type = 'text' size = '40' name = 'user' value = '<?php echo $r['username']; ?>' /><br />
					
					Title:<br />
					<select name = 'title' style = "width: 158">
						<option value = "Mr" <?php if($r['title'] == 'Mr') echo "selected"; ?>>MR</option>
						<option value = "Ms" <?php if($r['title'] == 'Ms') echo "selected"; ?>>MS</option>
					</select><br />
					
					Name:<br />
					<input type = 'text' size = '40' name = 'name' value = '<?php echo $r['name']; ?>' /><br />
					
					Position:<br />
					<input type = 'text' size = '40' name = 'position' value = '<?php echo $r['position']; ?>' /><br />
					
					Email:<br />
					<input type = 'text' size = '40' name = 'email' value = '<?php echo $r['email']; ?>' /><br />
					
					Phone:<br />
					<input type = 'text' size = '40' name = 'phone' value = '<?php echo $r['phone']; ?>' /><br />
					
					Status:<br />
					<select name = 'activestatus' style = "width: 158">
						<option value = "1" <?php if($r['active'] == 1) echo "selected"; ?>>Active</option>
						<option value = "0" <?php if($r['active'] == 0) echo "selected"; ?>>Passive</option>
					</select><br />
					
					User Role:<br />
					<select name = 'role' size = "1" style = "width: 158">
						<option value = ''></option>                
						<?php 
							$q1 = mysql_query("SELECT * FROM `usr_roles` WHERE `id` >= '0' ORDER BY `id`");
							while($r1 = mysql_fetch_array($q1))
							{
								echo "<option value = '".$r1['id']."'>".$r1['id']." - ".$r1['description']."</option>";
							}			
						?>
					</select><br />
					
					District:<br />
					<select name = 'district' size = "1" style = "width: 158">
						<option value = ''></option>                
						<?php 
							$qo2 = mysql_query("SELECT * FROM `area_district` WHERE `districtid` > '0' ORDER BY `districtid`");
							while( $ro2 = mysql_fetch_array($qo2) )
							{
								echo "<option value = '".$ro2['districtid']."'>".$ro2['districtid']." - ".$ro2['district_name']."</option>";
							}			
						?>
					</select>
					</form><br /><br />         
            
		</td>			
			<td valign = 'top' align = 'right'><br /><br /><br />				
				<input type = 'submit' value = 'Reset Password' name = 'resetpass' />
				<a href = 'passReset.php=<?php echo $_GET['username']; ?>&action=reset'>Reset Password</a><br /><br />				
				<a href = 'createNewUser.php=<?php echo $_GET['username']; ?>&action=create'>Create New User</a><br /><br />
				<a href = 'modifyUser.php=<?php echo $_GET['username']; ?>&action=modify'>Modify User</a><br /><br />
				<input type = 'submit' value = 'Save' name = 'Save' />
			</td>
		</tr>
	</table>
            
		</td>
		
	</tr>
</table><br />

</body>
</html>
