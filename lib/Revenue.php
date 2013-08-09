<?php

	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class Revenue
	{

		// 	get the data out of property table
		function getPropertyInfo( $upn = "", $subupn = "", /*$year = 2013,*/ $f = "" )
		{
			//$q = mysql_query("SELECT * FROM `property` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' AND `year` = '".$year."' ");
			$q = mysql_query("SELECT * FROM `property` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
	
		//   get the data from the payments_property table
		function getPropertyPaymentsInfo( $upn = "", $subupn = "", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * FROM `payments_property` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' ORDER BY `id` DESC LIMIT 1 ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
	}


?>