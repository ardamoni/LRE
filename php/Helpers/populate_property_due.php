<?php
	/*
	 * 	this file is used to populate due table
	 *  from property table that matches values in columns
	 *  property_use (property) / code (fee_fixing_property)
	 */

	// DB connection
	require_once( "../../lib/System.php" );
	require_once( "../../lib/configuration.php" );


	$System = new System;
	$year = $System->GetConfiguration("RevenueCollectionYear");

	//
	// CHANGE THIS VALUE for every district
	//
//	$districtID = '123';
	$districtID = $_GET['districtid'];

	// Display
	echo "START", "</br>";

	// PROTECTION
	$protection = mysql_query(" SELECT `upn`
								FROM `property_due`
								WHERE `districtid` = '".$districtID."' AND `year` = '".$year."' ");

	if( mysql_num_rows($protection) == 0 )
	{
		echo "Good, no previous data exist in the property_due table for ";
		echo "districtID ", $districtID, " and year ", $year, ". Script will proceed.", "<br>";
	}
	else
	{
		$rp =  mysql_num_rows($protection);
		echo "Stopping and exiting Script because there are previus data @ property_due for districtID", $districtID, " and year ", $year , "</br>";
		echo "Counter of Existing rows: ", $rp, "</br>";
		exit();
	}

	// generate file report  // it is created in the same directory as this file
	$file = 'report_generation/'.$districtID.'_Report_Import_into_Property_due.txt';
	// clean any previous data in the file
	file_put_contents($file, "");
	// Open the file
	$current = file_get_contents($file);
	// add the date and time to the file
	$current .= date("Y-m-d h:i:sa");	$current .= "\r\n"; $current .= "\r\n";

//STEP 1: copy information from property to property_due
	// get all the data from Property
	$querry = mysql_query(" SELECT * FROM `property` WHERE `districtid` = '".$districtID."' ");

	// Display - Missmatch or number of rows
	$i=1;
	if( mysql_num_rows($querry) == 0 )
	{
		echo "no rows @querry: ", "<br>";
	}
	else
	{
		$rows =  mysql_num_rows($querry);
		echo "districtID: ", $districtID, ", rows: ", $rows, "<br>";
		// Append a new data to the file
		$current .= "Report on COPY data from property to property_due: ";
		$current .= "districtID: "; $current .= $districtID;
		$current .= ", rows: "; $current .= $rows; $current .= ". \r\n";

	}

	// insert all property UPN, SUBUPN and DISTRICTID into Property_due
	while($BOR = mysql_fetch_array($querry))
	{

		// property_due
		mysql_query(" INSERT INTO `property_due`(	`id`,
													`upn`,
													`subupn`,
													`districtid`,
													`year`,
													`feefi_code`,
													`prop_value`,
													`comments`,
													`lastentry_person`,
													`lastentry_date`)
										VALUES (
													NULL,
													'".$BOR['upn']."',
													'".$BOR['subupn']."',
													'".$BOR['districtid']."',
													'".$year."',
													'".$BOR['property_use']."',
													'".$BOR['prop_value']."',
													'auto by populate_property_due',
													'script',
													'".date("Y-m-d")."' ) ");

		// Display
		//echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['districtid'], " & ", $year, " & ", $BOR['property_use'], "<br>";
		//$i++;
	}

//STEP 2: Update property_due with information from fee_fixing_xx
	// update
	$q1 = mysql_query(" SELECT	*
						FROM	`property_due` `b`,
								`fee_fixing_property` `f`
						WHERE	`b`.`districtid` = `f`.`districtid` AND
								`b`.`feefi_code` = `f`.`code` AND
								`f`.`year` = '".$year."' ");

	// Display - Missmatch or number of rows
	if( mysql_num_rows($q1) == 0 )
	{
		echo "no rows @ q1: ", "<br>";
	}
	else
	{
		$rq1 =  mysql_num_rows($q1);
//		echo "districtID: ", $districtID, ", rows: ", $rq1, "<br>";
		// Append a new data to the log file
		$current .= "Report on Insert fee fixing values into property_due: ";
		$current .= "districtID: "; $current .= $districtID;
		$current .= ", rows: "; $current .= $rq1; $current .= ". \r\n";
	}

	while($Results = mysql_fetch_array($q1))
	{
		// Property_due update with feefi_value
		mysql_query("	UPDATE 		`property_due`
						SET 		`feefi_value` = '".$Results['rate']."',
									`rate_value` = '".$Results['prop_value']*$Results['rate_impost']."',
									`rate_impost_value` = '".$Results['rate_impost']."',
									`feefi_unit` = '".$Results['unit']."'
						WHERE 		`upn` = '".$Results['upn']."' AND
									`subupn` = '".$Results['subupn']."' AND
									`districtid` = '".$Results['districtid']."' AND
									`year` = '".$year."' AND
									`feefi_code` = '".$Results['code']."' ");
	}

//STEP 3: Error reporting into external text file
	//
	// REPORT for ERRORS !!!
	//
	// test property_due for no value on feefi_value
	$test = mysql_query(" SELECT *
							FROM `property_due`
							WHERE `districtid` = '".$districtID."' AND `year` = '".$year."' ");

	// Append a new data to the file
	$current .= "number; upn; subupn; districtid; year; fee fixing code; fee fixing value \r\n";

	// Display
	echo "number; upn; subupn; districtid; year; fee fixing code; fee fixing value </br>";

	$j = 1;
	// Properties that have NO value
	while($Verification = mysql_fetch_array($test))
	{
		if( $Verification['feefi_value'] == 0 )
		{
			$current .= $j; $current .= ": ";
			$current .= $Verification['upn']; $current .= "; ";
			$current .= $Verification['subupn']; $current .= "; ";
			$current .= $Verification['districtid']; $current .= "; ";
			$current .= $year; $current .= "; ";
			$current .= $Verification['feefi_code']; $current .= "; ";
			$current .= $Verification['feefi_value']; $current .= ";\r\n";
			// Write the contents back to the file
			file_put_contents($file, $current);

			// display it on screen:
			$real =  mysql_num_rows($test);
			echo $j, ": ", $Verification['upn'], " & ", $Verification['subupn'], " & ", $Verification['districtid'], " & ", $year;
			echo " & ", $Verification['feefi_code'], " & ", $Verification['feefi_value'], "<br>";
			$j++;
		}
	}

	// download and open the file for the user on their local machine
	if (file_exists($file))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
	else
	{
		// inform the user to use the SCREEN data
		echo '<span style="color: red; text-align: center;">*** FILE DOES NOT EXISTS, PLEASE PRINT & USE ON-SCREEN DATA ***</span>';
	}
?>