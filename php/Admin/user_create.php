<?php

	if(isset($_POST['CreateUser']))
	{
		mysql_query("INSERT INTO `usr_users` (	`username`, 
												`pass`,												
												`name` ) 
									VALUES (	'".$_POST['user']."', 
												'".md5($_POST['user'])."', 
												'".$_POST['name']."' )");
																
		echo "<META HTTP-EQUIV=\"Refresh\"CONTENT=\"0; URL=?mod=user-management&id=".$_POST['user']."\">";
	}
?>


<table cellpadding="0" cellspacing="0" border="0" width = '800' align = 'center'>

	<tr>
		<td>
			<br />
			<h1>Create New User</h1>

            <form method = 'POST' action = ''>
        
            Username:<br />
            <input type = 'text' size = '20' name = 'user' value = '<?= $r['username']; ?>' /><br /><br />
            
            Name:<br />
            <input type = 'text' size = '20' name = 'name' value = '<?= $r['name']; ?>' /><br /><br />
                
			<input type = 'submit' value = 'Create User' name = 'CreateUser' />
        
			</form><br /><br />

		</td>
	</tr>
    
</table><br />




