<?php
session_start();
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
		 *
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

		// 	get the local plan data out of KML_from_LUPIS table
		function getLPInfo( $upn) //, $year, $districtid)
		{
			$conndb = new db(cDsn, cUser, cPass);

			$bind = array(
				":upn" => "$upn"
			);
 			$result = $conndb->select("KML_from_LUPMIS INNER JOIN area_district ON KML_from_LUPMIS.districtid = area_district.districtid", "KML_from_LUPMIS.upn = :upn", $bind);
			return $result[0];
		}

		// 	get the local plan data out of KML_from_LUPIS table
		function getFFInfo( $upn)
		{
			$conndb = new db(cDsn, cUser, cPass);

			$bind = array(
				":upn" => "$upn"
			);
 			$result = $conndb->select("fees_fines INNER JOIN area_district ON fees_fines.districtid = area_district.districtid", "upn = :upn", $bind);
			return $result[0];
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