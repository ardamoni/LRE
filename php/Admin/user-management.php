<?php

	// reset password to user_id
	if(isset($_GET['action']))
	{
		mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['id'])."' WHERE `username` = '".$_GET['id']."'");
		echo "Pass reset - link", "<br>";
	}
	
	// update user
	if(isset($_POST['General']))
	{	
		// update the usr_users table
		mysql_query("UPDATE `usr_users` SET		`title` 		= 	'".$_POST['title']."',  
												`name` 			= 	'".$_POST['name']."', 
												`position` 		= 	'".$_POST['position']."',  
												`email` 		= 	'".$_POST['email']."', 												
												`phone` 		= 	'".$_POST['phone']."',  												
												`activestatus` 	= 	'".$_POST['activestatus']."'	
										WHERE 	`username` 		= 	'".$_GET['id']."' ");
	}
	
	// ONE USERNAME for ONE REGION !!!
	if(isset($_POST['AddRegion']))
	{	
		$rum = mysql_query(" SELECT * FROM `usr_user_region` WHERE `username` = '".$_GET['id']."' ");				
		if( mysql_num_rows($rum) > 1 )
		{
			echo "More than one user with that username in the usr_user_region table", "<br>";
			echo "Please delete", "<br>";
		}
		else if( mysql_num_rows($rum) == 0 )
		{
			echo "No users with that username in the user_region table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_region` ( `username`, `regionid` ) VALUES ( '".$_GET['id']."', '".$_POST['region']."' )");
		}
		else
		{	
			// when we Modify Region we must delete District
			mysql_query("UPDATE `usr_user_region` SET `regionid` = '".$_POST['region']."' WHERE `username` = '".$_GET['id']."'");					
			$qdist = mysql_query(" SELECT `districtid` FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' "); 
			while($rdist =  mysql_fetch_array($qdist))
			{
				mysql_query("DELETE FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' AND `districtid` = '".$rdist['districtid']."'");				
			}
		}
	}
	
	// ONE USERNAME for ONE DISTRICT !!!
	if(isset($_POST['AddDistrict']))
	{	
		// get the region
		$qreg = mysql_query(" SELECT `regionid` FROM `usr_user_region` WHERE `username` = '".$_GET['id']."' ");
		$rreg =  mysql_fetch_array($qreg);
		
		// update the usr_user_district
		$rsa = mysql_query(" SELECT * FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' "); 
		if( mysql_num_rows($rsa) > 1 )
		{
			echo "More than one user with that username in the usr_user_district table", "<br>";
			echo "Please delete", "<br>";
		}
		else if( mysql_num_rows($rsa) == 0 )
		{
			echo "No users with that username in the usr_user_district table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_district` ( `username`, `regionid`, `districtid` ) VALUES ( '".$_GET['id']."', '".$rreg['regionid']."', '".$_POST['district']."')");
		}
		else 
		{
			mysql_query("UPDATE `usr_user_district` SET `districtid` = '".$_POST['district']."', `regionid` = '".$rreg['regionid']."' WHERE `username` = '".$_GET['id']."' ");			
		}
	}
	
	// ONE USERNAME ONE ROLE !!!
	if(isset($_POST['AddRole']))
	{
		$rol = mysql_query(" SELECT * FROM `usr_user_role` WHERE `username` = '".$_GET['id']."' "); 
		if( mysql_num_rows($rol) > 1 )
		{
			echo "More than one user with that username in the usr_user_role table", "<br>";
			echo "Please delete", "<br>";
		}
		else if( mysql_num_rows($rol) == 0 )
		{
			echo "No users with that username in the user_role table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_role` ( `username`, `roleid` ) VALUES ( '".$_GET['id']."', '".$_POST['role']."') ");
		}
		else
		{
			mysql_query("UPDATE `usr_user_role` SET `roleid` = '".$_POST['role']."'  WHERE `username` = '".$_GET['id']."' ");		
		}		
	}
	
	
	// Delete 
	if(isset($_GET['delRegion']))
	{
		mysql_query("DELETE FROM `usr_user_region` WHERE `username` = '".$_GET['id']."' AND `regionid` = '".$_GET['delRegion']."'");
		$qdist = mysql_query(" SELECT `districtid` FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' "); 
		while($rdist =  mysql_fetch_array($qdist))
		{
			mysql_query("DELETE FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' AND `districtid` = '".$rdist['districtid']."' AND `regionid` = '".$_GET['delRegion']."'");				
		}
	}
		
	if(isset($_GET['delDistrict']))
		mysql_query("DELETE FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' AND `districtid` = '".$_GET['delDistrict']."'");

	if(isset($_GET['delRole']))
		mysql_query("DELETE FROM `usr_user_role` WHERE `username` = '".$_GET['id']."' AND `roleid` = '".$_GET['delRole']."'");


	$q = mysql_query("SELECT * FROM `usr_users` WHERE `username` = '".$_GET['id']."'");
	$r = mysql_fetch_array($q);
?>


<table cellpadding="0" cellspacing="0" border="0" width = '600' align = 'left'>
	<tr>
		<td>
			<table width = '100%'>
			<tr>
				<td>
					<h1><?php echo $r['name']; ?></h1>

					<form method = 'POST' action = ''>
				
					Username:<br />
					<input type = 'text' size = '30' name = 'user' value = '<?php echo $r['username']; ?>' disabled /><br />
					
					Title:<br />
					<select name = 'title' style = "width: 200" >						
						<option value = "Mr" <?php if($r['title'] == 'Mr') echo "selected"; ?>>Mr</option>
						<option value = "Ms" <?php if($r['title'] == 'Ms') echo "selected"; ?>>Ms</option>
					</select><br />
					
					Name:<br />
					<input type = 'text' size = '30' name = 'name' value = '<?php echo $r['name']; ?>' /><br />
					
					Position:<br />
					<input type = 'text' size = '30' name = 'position' value = '<?php echo $r['position']; ?>' /><br />
					
					Email:<br />
					<input type = 'text' size = '30' name = 'email' value = '<?php echo $r['email']; ?>' /><br />
					
					Phone:<br />
					<input type = 'text' size = '30' name = 'phone' value = '<?php echo $r['phone']; ?>' /><br />
					
					Status:<br />
					<select name = 'activestatus' style = "width: 200">
						<option value = "1" <?php if($r['activestatus'] == '1') echo "selected"; ?>>Active</option>
						<option value = "0" <?php if($r['activestatus'] == '0') echo "selected"; ?>>Passive</option>
					</select><br />
					<br />
					<input type = 'submit' value = 'Save' id = 'Save' name = 'General' />
					</form><br /><br />
				</td>
				<td valign = 'top' align = 'right'><br /><br /><br />
					<a href = '?mod=user-management&id=<?php echo $_GET['id']; ?>&action=resetPass'>Reset Password</a><br /><br />
					<a href = '?mod=user_create&id=<?php echo $_GET['id']; ?>'>Create New User</a><br /><br />
					
				</td>
			</tr>
		</table>	
		
			<form method = 'POST' action = ''>
			<font color = #000000'>
			Region:<br />
			<select name = 'region' style = "width: 200">
				<option value = ''></option>
			<?php	
				$qReg = mysql_query("SELECT * FROM `area_region` ORDER BY `id`");
				while($rReg = mysql_fetch_array($qReg))
				{
					echo "<option value = '".$rReg['id']."'>".$rReg['id']." - ".$rReg['region_name']."</option>";
				}			
			?></select>
			<input type = 'submit' value = 'Add' name = 'AddRegion' />
			<?php 
				$qa1 = mysql_query("SELECT `b`.`id`, `b`.`region_name` 
										FROM `area_region` `b`, `usr_user_region` `u` 
										WHERE `b`.`id` = `u`.`regionid` 												
										AND `u`.`username` = '".$r['username']."' 
										ORDER BY `b`.`id`");
				while($ra1 = mysql_fetch_array($qa1))
				{
					echo "<p><a href = 'content.php?mod=user-management&id=".$_GET['id']."&delRegion=".$ra1['id']."'><img src = 'img/del.png' align = 'absmiddle' border = '0'></a> <font color = #000000'>".$ra1['id']." - ".$ra1['region_name']."</p>";
				}					
			?> 
			</form><br />
			
			
			<form method = 'POST' action = ''>
			District:<br />
			<select name = 'district' style = "width: 200">
				<option value = ''></option>
			<?php 			
				$qRQ = mysql_query("SELECT `b`.`id`, `b`.`region_name` 
										FROM `area_region` `b`, `usr_user_region` `u` 
										WHERE `b`.`id` = `u`.`regionid` 												
										AND `u`.`username` = '".$r['username']."' 
										ORDER BY `b`.`id`");
				while($rRQ = mysql_fetch_array($qRQ))
				{
					$q1 = mysql_query("SELECT	`d`.`districtid`, `d`.`district_name`
											FROM `area_district` `d`
											WHERE `d`.`regionid` = '".$rRQ['id']."' 
											ORDER BY `d`.`districtid`");
												
					while($r1 = mysql_fetch_array($q1))
					{
						echo "<option value = '".$r1['districtid']."'>".$r1['districtid']." - ".$r1['district_name']."</option>";
					}					
					echo "<option value = ''> </option>";
				}					
			?></select>
			 <input type = 'submit' value = 'Add' name = 'AddDistrict' />
            <?php 
				$qd1 = mysql_query("SELECT		`d`.`districtid`, `d`.`district_name`				
										FROM 	`area_district` `d`, `usr_user_district` `u` 
										WHERE 	`d`.`districtid` = `u`.`districtid` 
										AND 	`u`.`username` = '".$r['username']."' 
										ORDER BY `d`.`districtid`");
											
				while($rd1 = mysql_fetch_array($qd1))
				{
					echo "<p><a href = 'content.php?mod=user-management&id=".$_GET['id']."&delDistrict=".$rd1['districtid']."'><img src = 'img/del.png' align = 'absmiddle' border = '0'></a> <font color = #000000'>".$rd1['districtid']." - ".$rd1['district_name']."</p>";
				}			
			?>
			</form><br />

			
			<form method = 'POST' action = ''>
			Role:<br />
			<select name = 'role' style = "width: 200">
				<option value = ''></option>
			<?php 
				$qRol = mysql_query("SELECT * FROM `usr_roles` ORDER BY `id`");
				while($rRol = mysql_fetch_array($qRol))
				{
					echo "<option value = '".$rRol['id']."'>".$rRol['id']." - ".$rRol['description']."</option>";
				}			
			?> </select>
			<input type = 'submit' value = 'Add' name = 'AddRole' />
            <?php 
				$qq1 = mysql_query("SELECT `r`.`id`, `r`.`description` 
										FROM 	`usr_roles` `r`, `usr_user_role` `u` 
										WHERE 	`r`.`id` = `u`.`roleid` 										
										AND 	`u`.`username` = '".$r['username']."' 
										ORDER BY `r`.`id` ");
				while($rr1 = mysql_fetch_array($qq1))
				{
					echo "<p><a href = 'content.php?mod=user-management&id=".$_GET['id']."&delRole=".$rr1['id']."'><img src = 'img/del.png' align = 'absmiddle' border = '0'></a> <font color = #000000'>".$rr1['id']." - ".$rr1['description']."</p>";
				}			
			?>
			</form><br /><br />		
 		</td>		
	</tr>
	
</table><br />






