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
	
	echo "checking property: ", "<br>";
	
	
	$q1 = mysql_query("	SELECT	* FROM	`property` `b` ");
	
	$funnyChar = array('|'=>'1','I'=>'1','S'=>'5','O'=>'0','s'=>'5','o'=>'0','i'=>'1','D'=>'0');
	
	while($q2 = mysql_fetch_array($q1))
	{
	 $upn = $q2['upn'];
	 
	 
	 foreach ($funnyChar as $key => $value) {
			if (strpos($upn,$key)>=0) {
				$upn=str_replace($key,$value,$upn);
			}
	  }
		$query = mysql_query(" UPDATE 	`property` 
							SET 	`upn` = '".$upn."'
									
							WHERE 	`id` = '".$q2['id']."' ");	
	}
	
	echo "checking Business: ", "<br>";
	
	
	$q1 = mysql_query("	SELECT	* FROM	`business` `b` ");
	
	$funnyChar = array('|'=>'1','I'=>'1','S'=>'5','O'=>'0','s'=>'5','o'=>'0','i'=>'1','D'=>'0');
	
	while($q2 = mysql_fetch_array($q1))
	{
	 $upn = $q2['upn'];
	 $subupn = $q2['subupn'];
	 
	 
	 foreach ($funnyChar as $key => $value) {
			if (strpos($upn,$key)>=0) {
				$upn=str_replace($key,$value,$upn);
			}
			if (strpos($subupn,$key)>=0) {
				$subupn=str_replace($key,$value,$subupn);
			}
	  }
		$query = mysql_query(" UPDATE 	`business` 
							SET 	`upn` = '".$upn."',
									`subupn` = '".$subupn."'
									
							WHERE 	`id` = '".$q2['id']."' ");	
	}
		
?>