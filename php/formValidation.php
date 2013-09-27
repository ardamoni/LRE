<?php

	require_once( "../lib/configuration.php" );
	
	// collector's ticket validation	
	// TODO must match the username session - 
	// for the time beeing there is no session and every user loged 
	// is converted to collector1
	$q = mysql_query( "SELECT * FROM 	`tickets` 
								WHERE 	`starting` <= '".trim($_POST['treceipt'])."' AND 
										`ending` >= '".trim($_POST['treceipt'])."' ");
	
	$anymatches = mysql_num_rows( $q ); 
	if( $anymatches == 1 ) 
	{
		$t = mysql_fetch_array( $q );
		$user123 = $t['username'];		
		
		$query = mysql_query( "SELECT DISTINCT 	(`r`.`roleid`) AS `rid`,
												`d`.`districtid` AS `did`
										FROM 	`usr_user_role` `r`, 
												`usr_user_district` `d`
										WHERE 	`r`.`username` = '".$user123."' AND 
												`d`.`username` = '".$user123."' ");
		
		$result = mysql_fetch_array( $query );	
		$rows = mysql_num_rows( $query ); 		
		//echo "user: ",  $user123, "roelid: ", $result['rid'],  ", districtid ", $result['did'], ", rows: ", $rows, "<br>";
		if( $result['rid'] == '100' && $result['did'] == '1840' && $rows == 1 )
		{
			echo 1;
		}
		else
		{
			echo 0; 			
		}			
	}
	else
	{
		echo 0; 
	}

	
?>	