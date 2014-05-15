<?php

	/*	
	 * 	DESCRIPTION: Insert the owners into hlp_owners_clients
	 *  by taking them from property and business from the old revenue DB.
	 *  
	 *	ASSUMPTION: property table from the old revenue DB has the correct owner details
	 *	TODO: ASSUMPTION: business table from the old revenue DB has the correct owner details
	 *	 
	 *	LOGIC: get the owner details from property and add them to hlp_owners_clients
	 *	only if they match with their upn and subupn compared to property_details
	 *	then do the reverse add the ownerid to the property_details from the hlp_owners_clients.
	 *  Finally do the verification
	 *
	 **************************************************************************************************
	 *	WARNING: to be used only once !!!   Otherwise it will duplicate the data in hlp_owners_clients
	 ************************************************************************************************** 
	 */

	// DB connection
	require_once( "../../lib/configuration.php" );
	
	echo "start: ", "<br>";
	
	// change this manually for all the districts
	$districtID = '131';
	
	// querry the property and property_details to extract the owner details 
	// this will distinguish Arben - ghana - 01234 - ar@ar.net and Arben - Ghana_ - 01234 - ar@ar.nets
	$q1 = mysql_query("SELECT 	DISTINCT `p`.`owner`, `p`.`owneraddress`, `p`.`owner_tel`, `p`.`owner_email`
						FROM 
								`property`  `p`,
								`property_details` `v`
						
						WHERE	
								`p`.`upn` = `v`.`upn` AND
								`p`.`subupn` = `v`.`subupn` AND 
								`p`.`districtid` = '".$districtID."' AND
								`p`.`districtid` = `v`.`districtid`  								
						
						LIMIT 0, 20														
						");
	
	// errors
	if( mysql_num_rows($q1) == 0 )
	{	
		echo "no rows: ", "<br>";
	}
	else
	{
		$rows =  mysql_num_rows($q1);
		echo "PART1: insert into hlp_owners_clients", "<br>";
		echo "districtID: ", $districtID, ", rows: ", $rows, "<br>";
		echo "Owner", " & address ", " & telephone", " & e-mail ", "<br>";
	}

	// add the owner details in the temporary table
	// filter them based on same name, address, owner_tel and owner_email
	// use the temporary table same as the hlp_owners_clients
	//
	/*	 TODO filter similarities before populating the table.
	 *	(197, 'SHAMA METHODIST SCHOOL', NULL, '2014-03-17', 'P O.BOX 16 SHAMA', NULL, NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'testing'),
	 *	(198, 'SHAMA METHODIST CHURCH', NULL, '2014-03-17', 'P O.BOX 16  SHAMA', NULL, NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'testing'),
	 *	(199, 'SHAMA METHODIST SCHOOL', NULL, '2014-03-17', 'P.O.BOX 16 SHAMA', NULL, NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'testing'),
	 *	(200, 'SHAMA METHODIST CHURCH', NULL, '2014-03-17', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, NULL, 'testing');
	 *	VERY DIFFICULT to achieve it automatically, since there are various combinations as above.   It may never be spotless clean.
	*/
	$i=1;
	while($BOR = mysql_fetch_array($q1))
	{
		mysql_query("INSERT INTO `hlp_owners_clients_temp` (	`id`, `name`, `parent_name`, `dob`, 
																`address1`, `address2`, `address3`, 
																`tel1`, `tel2`, `tel3`, 
																`email1`, `email2`, `email3`, 
																`description`, `comments`) 
													VALUES (	NULL, upper('".$BOR['owner']."'), NULL, '2014-03-17', 
																upper('".$BOR['owneraddress']."'), NULL, NULL, 
																upper('".$BOR['owner_tel']."'), NULL, NULL, 
																upper('".$BOR['owner_email']."'), NULL, NULL, 
																NULL, 'testing')");
											
		echo $i, ": ", $BOR['owner'], " & ", $BOR['owneraddress'], " & ", $BOR['owner_tel'], " & ", $BOR['owner_email'], "<br>";
		$i++;
	}
	
	
	
	// query property and temp tables based on owner
	$querry = mysql_query("SELECT 	`v`.`id`, `p`.`upn`, `p`.`subupn`, `p`.`districtid`
							FROM 
									`property`  `p`,
									`hlp_owners_clients_temp` `v`														
							WHERE
									upper(`p`.`owner`) LIKE upper(`v`.`name`) AND 
									upper(`p`.`owneraddress`) LIKE upper(`v`.`address1`) AND 
									upper(`p`.`owner_tel`) LIKE upper(`v`.`tel1`) AND 
									upper(`p`.`owner_email`) LIKE upper(`v`.`email1`)
							ORDER BY `p`.`id` ASC							
						");	
	
	// errors
	if( mysql_num_rows($querry) == 0 )
	{	
		echo "no rows: ", "<br>";
	}
	else
	{	
		echo "PART2: update property_details with ownerid", "<br>";	
		$rows =  mysql_num_rows($querry);
		echo "districtID: ", $districtID, ", rows: ", $rows, "<br>";
	}
	
	// update property_details with ownerid, only if the property matches with upn, subupn and districtid
	$j=1;
	while($change = mysql_fetch_array($querry))
	{		
		mysql_query( "UPDATE 	`property_details_temp` 
						SET 
								`ownerid` = '".$change['id']."'
						WHERE
								`upn` = '".$change['upn']."' AND
								`subupn` = '".$change['subupn']."' AND
								`districtid` = '".$change['districtid']."' 								
						" );	
						
		echo $j, ": ", $change['id'], " & ", $change['upn'], " & ", $change['subupn'], "<br>";
		$j++;	
	}
	
	// verification
	// compare 3 tables property (upn, subupn, ownerid) with property_details (upn, subupn, ownerid) and temp (ownerid)
	$k = 0;
	$ver = mysql_query("SELECT 	`d`.`upn`, `d`.`subupn`, `d`.`districtid`, `v`.`id`, `v`.`name`, `v`.`address1`, `v`.`tel1`, `v`.`email1`
							FROM 
									`property`  `p`,
									`property_details_temp`  `d`,
									`hlp_owners_clients_temp` `v`
											
							WHERE
									upper(`p`.`owner`) = upper(`v`.`name`) AND 
									upper(`p`.`owneraddress`) = upper(`v`.`address1`) AND 
									upper(`p`.`owner_tel`) = upper(`v`.`tel1`) AND 
									upper(`p`.`owner_email`) = upper(`v`.`email1`) AND
									`d`.`ownerid` = `v`.`id` AND 
									`d`.`upn` = `p`.`upn` AND 
									`d`.`subupn` = `p`.`subupn` AND 
									`d`.`districtid` = `p`.`districtid` 
							ORDER BY `p`.`id` ASC							
						");	
						
	if( mysql_num_rows($ver) == 0 )
	{	
		echo "no rows: ", "<br>";
	}
	else
	{		
		echo "PART3: verification", "<br>";	
		$outcome =  mysql_num_rows($ver);
		echo "rows: ", $outcome, "<br>";
	}
	
	
	while($verification = mysql_fetch_array($ver))
	{	
		$array = array();		 
		if( in_array( $verification['upn'], $array) == 0 )
		{
			array_push( $array, $verification['upn'] );
			echo $k, ": ", $verification['upn'], " & ", $verification['subupn'], " & ", $verification['id'], " & ", $verification['address1'], " & ", $verification['tel1'], " & ", $verification['email1'], "<br>";
			$j++;	
		}
		else if( in_array( $verification['upn'], $array) == 1 )
		{
			echo $verification['upn'], " found in array", "<br>";
		}			
		else if( in_array( $verification['upn'], $array) > 1 )
		{
			echo $verification['upn'], " More than one in array", "<br>";
		}
	}
	
?>