<?php
	// DB connection
	require_once( "../lib/configuration.php"	);
	require_once( "../lib/System.php" );


	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class propertyDetailsClass
	{		
		/*
		 *	PROPERTY Details TEST
		 */		
		// 	get the data out of property table
		function getPInfo( $upn, $subupn) //, $year, $districtid)
		{
  		if ($subupn != "" && $subupn != 'null')
//		  if ($subupn != "" || $subupn != NULL || $subupn != "0")
  		{
			$run = "SELECT * 	FROM 	`property` 	WHERE 	`upn` = '".$upn."' AND `subupn` = '".$subupn."';";// AND `year` = '".$year."' AND `districtid` = '".$districtid."';";
		} else
		{
			$run = "SELECT * 	FROM 	`property` 	WHERE 	`upn` = '".$upn."';";// AND `year` = '".$year."' AND `districtid` = '".$districtid."';";
		}
			$q = mysql_query($run);
			$r = mysql_fetch_assoc($q);
			return $r;
		}
		
		/*
		 *	
		 */		
		// 	get the data out of roofing helper table
	function getHelperText($tablename)
		{
			$run = "SELECT `text`, `code` from `{$tablename}`;";
			$query 	= mysql_query($run);
			while ($r = mysql_fetch_assoc($query)) 
			{
			$json 				= array();
				$json['code']	= $r['code'];
				$json['text']	= $r['text'];
	    	$data[] 			= $json;
			}	

			return $data;
		 }
	} //end Class	

?>	