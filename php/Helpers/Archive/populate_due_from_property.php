<?php

	/*	
	 * 	this file is used to populate due table
	 *  from property_details table that matches the 
	 *  fee_fixing_property_code in the fee_fixing_property table 
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
						
							FROM	`property` `b`,
									`fee_fixing_property` `f`		
									
							WHERE	`b`.`districtid` = `f`.`districtid` AND
									`b`.`property_use` = `f`.`code` AND
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
						INSERT INTO `property_due`(`id`, `upn`, `subupn`, `districtid`, `year`, `feefi_code`, `feefi_value`, `comments`) VALUES (NULL, '".$BOR['upn']."', '".$BOR['subupn']."', '".$BOR['districtid']."', '".$year."', '".$BOR['code']."', '".$BOR['rate']."', 'ekke - ".date("Y-m-d")."')
					");
									
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $year;
		echo " & ", $BOR['fee_fixing_property_code'], " & ", $BOR['value'], "<br>";
		$i++;
		
	}
	
//finally set pay_status in property
 $qupdate = mysql_select("update `property` a set a.`pay_status`='1';");

?>