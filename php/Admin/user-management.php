<?php

	if(isset($_GET['resetPass']))
	{
		//mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['id'])."' WHERE `username` = '".$_GET['id']."'");
		mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['id'])."' WHERE `username` = '".$_GET['id']."'");
		echo "Pass reset - button", "<br>";
	}
	
	if(isset($_GET['action']))
	{
		//mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['id'])."' WHERE `username` = '".$_GET['id']."'");
		mysql_query("UPDATE `usr_users` SET `pass` = '".md5($_GET['id'])."' WHERE `username` = '".$_GET['id']."'");
		echo "Pass reset - link", "<br>";
	}
	
	if(isset($_POST['General']))
	{	
		// update the usr_users table
		mysql_query("UPDATE `usr_users` SET		`title` 	= 	'".$_POST['title']."',  
												`name` 		= 	'".$_POST['name']."', 
												`position` 	= 	'".$_POST['position']."',  
												`email` 	= 	'".$_POST['email']."', 												
												`phone` 	= 	'".$_POST['phone']."',  												
												`active` 	= 	'".$_POST['activestatus']."'	
										WHERE 	`username` 	= 	'".$_GET['id']."'");
		
		echo $_GET['id'], $_POST['title'],  $_POST['name'], $_POST['position'],  $_POST['email'], $_POST['phone'], $_POST['activestatus'], "<br>";
					
		
		// update / insert into the usr_users_role table
		$rol = mysql_query(" SELECT * FROM `usr_users_role` WHERE `username` = '".$_GET['id']."' ORDER BY ASC "); 
		if( mysql_num_rows($rol) > 1 )
		{
			echo "More than one user with that username in the user_role table", "<br>";
		}
		else if( mysql_num_rows($rol) == 0 )
		{
			echo "No users with that username in the user_role table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_role` ( `username`, `roleid` ) VALUES ( '".$_GET['id']."', '".$_POST['role']."') WHERE  
						NOT EXISTS (SELECT * FROM `usr_user_role` WHERE `username` = '".$_GET['id']."' AND  `roleid` = '".$rol['roleid']."' ");
		}
		else
		{
			mysql_query("UPDATE `usr_users_role` SET `roleid` = '".$_POST['role']."'  WHERE `username` = '".$_GET['id']."' ");		
		}	
			
		// update / insert into the usr_users_region table
		$rum = mysql_query(" SELECT * FROM `usr_users_region` WHERE `username` = '".$_GET['id']."' ORDER BY ASC "); 
		if( mysql_num_rows($rum) > 1 )
		{
			echo "More than one user with that username in the user_region table", "<br>";
		}
		else if( mysql_num_rows($rum) == 0 )
		{
			echo "No users with that username in the user_region table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_region` ( `username`, `regionid` ) VALUES ( '".$_GET['id']."', '".$_POST['region']."' )");
		}
		else
		{		
			mysql_query("UPDATE `usr_users_region` SET		`regionid` 	= 	'".$_POST['region']."' WHERE `username` = '".$_GET['id']."'");		
		}
		
		// update / insert into the usr_users_district table
		$rsa = mysql_query(" SELECT * FROM `usr_users_district` WHERE `username` = '".$_GET['id']."' ORDER BY ASC "); 
		if( mysql_num_rows($rsa) > 1 )
		{
			echo "More than one user with that username in the user_district table", "<br>";
		}
		else if( mysql_num_rows($rsa) == 0 )
		{
			echo "No users with that username in the user_district table, inserting new user", "<br>";
			mysql_query("INSERT INTO `usr_user_district` ( `username`, `regionid`, `districtid` ) VALUES ( '".$_GET['id']."', '".$_POST['region']."', '".$_POST['district']."')");
		}
		else // TODO check whether the district matches the region
		{
			mysql_query("UPDATE `usr_users_district` SET `districtid` = '".$_POST['district']."'  
													WHERE `username` = '".$_GET['id']."' AND `regionid` = '".$_POST['region']."' ");		
		}
	}

	// Insert
	if(isset($_POST['addRegion']))
		mysql_query("INSERT INTO `usr_user_region` ( `username`, `regionid` ) VALUES ( '".$_GET['id']."', '".$_POST['region']."' )");
	
	if(isset($_POST['addDistrict']))
		mysql_query("INSERT INTO `usr_user_district` ( `username`, `regionid`, `districtid` ) VALUES ( '".$_GET['id']."', '".$_POST['region']."', '".$_POST['district']."')");
	
	if(isset($_POST['addRole']))
		mysql_query("INSERT INTO `usr_user_role` ( `username`, `roleid` ) VALUES ( '".$_GET['id']."', '".$_POST['role']."')");

	// Delete 
	if(isset($_GET['delRegion']))
		mysql_query("DELETE FROM `usr_user_region` WHERE `username` = '".$_GET['id']."' AND `regionid` = '".$_GET['region']."'");
	
	if(isset($_GET['delDistrict']))
		mysql_query("DELETE FROM `usr_user_district` WHERE `username` = '".$_GET['id']."' AND `regionid` = '".$_GET['region']."' AND `deptid` = '".$_GET['district']."'");
	
	if(isset($_GET['delRole']))
		mysql_query("DELETE FROM `usr_user_role` WHERE `username` = '".$_GET['id']."' AND `roleid` = '".$_GET['role']."'");


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
					<input type = 'text' size = '30' name = 'user' value = '<?php echo $r['username']; ?>' /><br />
					
					Title:<br />
					<select name = 'title' style = "width: 215" >
						echo '<option value="' .$name. '">' .$name. '</option>';
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
					<select name = 'activestatus' style = "width: 215">
						<option value = "1" <?php if($r['activestatus'] == 1) echo "selected"; ?>>Active</option>
						<option value = "0" <?php if($r['activestatus'] == 0) echo "selected"; ?>>Passive</option>
					</select><br />
					
					
					<input type = 'submit' value = 'Save' id = 'Save' name = 'General' />					
					<a href = '?mod=user-management&id=<?php echo $_GET['id']; ?>&action=General'>Save</a><br />
					<input type = 'submit' value = 'Cancel' name = 'cancel' />
				
				
					</form><br /><br />
					
					
            </td>
			<td valign = 'top' align = 'right'><br /><br /><br />
				<a href = '?mod=user-management&id=<?php echo $_GET['id']; ?>&action=resetPass'>Reset Password</a><br /><br />
				<a href = '?mod=user-create&id=<?php echo $_GET['id']; ?>'>Create New User</a><br /><br />
				
			</td>
		</tr>
	</table>
            
 		</td>
	</tr>

</table><br />

<script language="javascript">
$("#region").change(function(){
    var region = $(this).val();

    if(!region){
        $("#district").attr("disabled", true);
        return false;
    }

    $("#district").attr("disabled", false);
});
</script>




