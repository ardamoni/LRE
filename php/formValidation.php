<?php
	if( session_status() != 2 )
	{
		session_start();
	}

	require_once( "../lib/configuration.php" );
	require_once( "../lib/Revenue.php"		 );
	require_once( "../lib/System.php"		 );
		
	$Data = new Revenue;
	$System = new System;
	
	$year = $System->GetConfiguration("RevenueCollectionYear");	
	
	// validation that used tickets cannot be re-used
	$usedTickets = $Data->getTicketsPaymentInfo( $_POST['upn'], $_POST['subupn'], $year, $_POST['treceipt'] );
	
	$q = mysql_query( "SELECT * FROM 	`tickets` `t`,
										`usr_user_role` `r`, 
										`usr_user_district` `d`
								WHERE 	`t`.`starting` <= '".trim($_POST['treceipt'])."' AND 
										`t`.`ending` >= '".trim($_POST['treceipt'])."' AND
										`t`.`username` = '".$_SESSION['user']['user']."' AND
										`t`.`username` = `r`.`username` AND
										`t`.`username` = `d`.`username` AND
										`r`.`roleid` = '".$_SESSION['user']['roleid']."' AND 
										(`r`.`roleid` = 100 OR `r`.`roleid` = 0) AND
										`d`.`districtid` = '".$_SESSION['user']['districtid']."' ");	
	
	$anymatches = mysql_num_rows( $q ); 
	if( $anymatches == 1 && $usedTickets == false ) 
	{	
		$result = mysql_fetch_array( $q );				
		//echo "districtid: ",  $_SESSION['user']['districtid'], ", roleid: ", $_SESSION['user']['roleid'],  "<br>";
		echo "1";		
	}
	else
	{
		//echo " rows: ", $anymatches;
		echo "0";
	}

	
?>	