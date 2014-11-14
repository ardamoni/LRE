<?php

	/*	
	 * 	this file is used to insert the row in due table 
	 *  based on business_details and fee_fixing_business_code tables
	 *  the fee_finxing_code must match 
	 */

	// DB connection
	require_once( "../../lib/configuration.php" );
	require_once( "../../lib/System.php" );

	$System = new System;	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	
	//
	// CHANGE THIS VALUE for every district 
	//
	$districtID = '131';
	
	echo "start: ", "<br>";
	
	
	$q1 = mysql_query("
						SELECT	*
						
							FROM	`business` `b`,
									`fee_fixing_business` `f`		
									
							WHERE	`b`.`districtid` = `f`.`districtid` AND
									`b`.`business_class` = `f`.`code` AND
									`f`.`year` = '".$year."' 									
						");
	
	$i=1;
	if( mysql_num_rows($q1) == 0 )
	{	
		echo "no rows: ", "<br>";
	}
	else 
	{	
		$rows =  mysql_num_rows($q1);
		echo "districtID: ", $districtID, ", rows: ", $rows, "<br>";
	}
	
	while($BOR = mysql_fetch_array($q1))
	{
		mysql_query("	
						INSERT INTO `business_due`(`id`, `upn`, `subupn`, `districtid`,  `year`, `feefi_code`, `feefi_value`, `comments`, `lastentry_person`, `lastentry_date`) VALUES (NULL, '".$BOR['upn']."', '".$BOR['subupn']."', '".$BOR['districtid']."', '".$year."', '".$BOR['business_class']."', '".$BOR['rate']."', NULL , 'ekke', '".date("Y-m-d")."')
					");
									
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $year;
		echo " & ", $BOR['fee_fixing_business_code'], " & ", $BOR['value'], "<br>";
		$i++;
		
	}
	
		
?>