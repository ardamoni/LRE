<?php

	require_once( "../lib/configuration.php" );
	require_once( "../lib/Revenue.php"		 );
	require_once( "../lib/System.php"		 );
		
	$Data = new Revenue;
	$System = new System;
	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	
	// collector's ticket validation	
	// TODO must match the username session - 
	// for the time beeing there is no session and every user loged 
	// is converted to collector1
	
	$usedTickets = $Data->getTicketsPaymentInfo( $_POST['upn'], $_POST['subupn'], $year, $_POST['treceipt'] );
	
	$q = mysql_query( "SELECT * FROM 	`tickets` 
								WHERE 	`starting` <= '".trim($_POST['treceipt'])."' AND 
										`ending` >= '".trim($_POST['treceipt'])."' ");
	
	$anymatches = mysql_num_rows( $q ); 
	if( $anymatches == 1 && $usedTickets == false ) 
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
		if( $result['rid'] == '100' && $result['did'] == '130' && $rows == 1 )
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