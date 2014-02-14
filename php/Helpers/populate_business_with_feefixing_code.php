<?php

	/*	
	 * 	this file is used to insert the fee fixing code into business_details table
	 *  it requires the old table business from the revenue DB 
	 *  to be copied to the revenue20 DB, prior to using this file.
	 */

	// DB connection
	require_once( "../../lib/configuration.php" );
	
	//
	// CHANGE THIS VALUE for every district 
	//
	$districtID = '131';
	
	echo "start: ", "<br>";
	
	
	$q1 = mysql_query	("
							SELECT	*
						
							FROM	`business` `p`,
									`business_details` `v`
									
							WHERE	`p`.`upn` = `v`.`upn` AND
									`p`.`subupn` = `v`.`subupn` AND 
									`p`.`districtid` = '".$districtID."' AND
									`p`.`districtid` = `v`.`districtid`  AND
									`p`.`id` = `v`.`id`									
							
							ORDER BY `p`.`id` ASC							
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
						UPDATE 		`business_details` 
						SET 		`fee_fixing_business_code` = '".$BOR['business_class']."' 
						WHERE 		`upn` = '".$BOR['upn']."' AND 
									`subupn` = '".$BOR['subupn']."' AND
									`districtid` = '".$BOR['districtid']."' AND
									`id` = '".$BOR['id']."'
					");
		
		// show the cases when business_class is 0
		if( $BOR['business_class'] == 0 )
		{	
			echo $i, ": ", $BOR['id'], " & ",$BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['business_class'], "<br>";
			$i++;
		}
	}
	
		
?>