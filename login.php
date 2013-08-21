<?php

  	/*
	 *	No Direct Access To This File
	 *	-----------------------------------------------------------------------
	 */ 
	defined( 'VALID_REVENUE' ) or die( 'STOP' );
 
?>

<form name = "login" action = "index.php" method = "POST">

<table width = "300" align = "center" border = "0">
	<tr>
		<td><p id = "form-desc"><?php echo "USERNAME"; ?>:</p>
		<input type = "text" id = "txt1" name = "user" size = "30"><br /><br /></td>
	</tr>
	<tr>
		<td><p id = "form-desc"><?php echo "PASSWORD"; ?>:</p>
		<input type = "password" id = "txt1" name = "pass" size = "30"><br /><br /></td>
	</tr>
	<tr>
		<td colspan = "2">		
			<input type = "submit" value = "<?php echo "SIGN IN"; ?>" id = "btn1">		
		</td>
	</tr>
</table>

</form>