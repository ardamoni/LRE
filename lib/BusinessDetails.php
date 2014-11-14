<?php
	// DB connection
	require_once( "../lib/configuration.php"	);
	require_once( "../lib/System.php" );

	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class businessDetailsClass
	{		
		/*
		 *	PROPERTY Details TEST
		 */		
		// 	get the data out of business table
		function getBInfo( $upn, $subupn)//, $year, $districtid)
		{
//  		if ($subupn != "" || $subupn != NULL || $subupn != "0" || $subupn != 'null')
  		if ($subupn != "" && $subupn != 'null')
  		{
			$run = "SELECT * 	FROM 	`business` 	WHERE 	`upn` = '".$upn."' AND `subupn` = '".$subupn."';"; // AND `year` = '".$year."' AND `districtid` = '".$districtid."';";
		} else
		{
			$run = "SELECT * 	FROM 	`business` 	WHERE 	`upn` = '".$upn."';"; // AND `year` = '".$year."' AND `districtid` = '".$districtid."';";
		}
			$q = mysql_query($run);
			$r = mysql_fetch_assoc($q);
			return $r;
		}
		
		/*
		 *	
		 */		
		// 	get the data out of helper table
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