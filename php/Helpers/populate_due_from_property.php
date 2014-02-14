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
						
							FROM	`property_details` `b`,
									`fee_fixing_property` `f`		
									
							WHERE	`b`.`districtid` = '".$districtID."' AND
									`b`.`districtid` = `f`.`districtid` AND
									`b`.`fee_fixing_property_code` = `f`.`code` AND
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
						INSERT INTO `due`(`id`, `upn`, `subupn`, `year`, `code`, `value`, `comments`) VALUES (NULL, '".$BOR['upn']."', '".$BOR['subupn']."', '".$year."', '".$BOR['fee_fixing_property_code']."', '".$BOR['value']."', '".$districtID."')
					");
									
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $year;
		echo " & ", $BOR['fee_fixing_property_code'], " & ", $BOR['value'], "<br>";
		$i++;
		
	}
	
		
?>