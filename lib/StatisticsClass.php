<?php
	require_once( "configuration.php"	);

	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class StatsData
	{
		/*
		 *	REGIONS
		 */		
		// 	get the Nr of Districts based on the regionid
		function getNrDistricts( $regionid = "", $f = "NrOfDistricts" )
		{
			$q = mysql_query("SELECT count(`districtid`) as '".$f."' from `area_district` WHERE `regionid` =  '".$regionid."'; ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		// 	get the TotalDueExpected based on the regionid from property_due
		function getTotalPropertyDueForRegion( $regionid = "", $year = "2014", $f = "TotalDue" )
		{
			$q = mysql_query("select d3.`district_name`, d2.year, sum(d2.`feefi_value`) as '".$f."' 
								from `property_due` d2, `area_district` d3 
								WHERE d3.`regionid`= '".$regionid."' AND d2.year = '".$year."' AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, d2.`year`;");

			$r = mysql_fetch_array($q);
			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}
		
		// 	get the TotalPropertyExpected based on the regionid from fee_fixing_property
		function getTotalPropertyExpected( $regionid = "", $year = "2014", $f = "TotalExpected" )
		{
			$q = mysql_query("select d3.`district_name`, d2.year, sum(d2.`rate`) as '".$f."' 
								from `property` d1, `fee_fixing_property` d2, `area_district` d3 
								WHERE d3.`regionid`='".$regionid."' AND d2.year = '".$year."' AND d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, d2.`year`;");

			$r = mysql_fetch_array($q);

			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}

		// 	get the TotalPyements based on the regionid from property_payments
		function getTotalPropertyPayments( $regionid = "", $year = "2014", $f = "TotalPayments" )
		{
			$q = mysql_query("select d3.`regionid`, d2.`payment_date`, sum(d2.`payment_value`) as '".$f."' 
								from `property_payments` d2, `area_district` d3 
								WHERE  d3.`regionid`='".$regionid."' AND 
									YEAR(d2.`payment_date`) = '".$year."' AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, YEAR(d2.`payment_date`);");

			$r = mysql_fetch_array($q);

			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}
		
		// 	get the TotalDueExpected based on the regionid from business_due
		function getTotalBusinessDueForRegion( $regionid = "", $year = "2014", $f = "TotalDue" )
		{
			$q = mysql_query("select d3.`district_name`, d2.year, sum(d2.`feefi_value`) as '".$f."' 
								from `business_due` d2, `area_district` d3 
								WHERE d3.`regionid`= '".$regionid."' AND d2.year = '".$year."' AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, d2.`year`;");
			$r = mysql_fetch_array($q);
			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}
		
		// 	get the TotalBusinessExpected based on the regionid from fee_fixing_property
		function getTotalBusinessExpected( $regionid = "", $year = "2014", $f = "TotalExpected" )
		{
			$q = mysql_query("select d3.`district_name`, d2.year, sum(d2.`rate`) as '".$f."' 
								from `business` d1, `fee_fixing_business` d2, `area_district` d3 
								WHERE d3.`regionid`='".$regionid."' AND d2.year = '".$year."' AND d1.`districtid`=d2.`districtid` AND d1.`business_class`=d2.`code` AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, d2.`year`;");

			$r = mysql_fetch_array($q);
			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}

		// 	get the TotalPyements based on the regionid from business_payments
		function getTotalBusinessPayments( $regionid = "", $year = "2014", $f = "TotalPayments" )
		{
			$q = mysql_query("select d3.`regionid`, d2.`payment_date`, sum(d2.`payment_value`) as '".$f."' 
								from `business_payments` d2, `area_district` d3 
								WHERE  d3.`regionid`='".$regionid."' AND 
									YEAR(d2.`payment_date`) = '".$year."' AND d2.`districtid`=d3.`districtid` 
								GROUP BY d3.`regionid`, YEAR(d2.`payment_date`);");

			$r = mysql_fetch_array($q);
			if (!empty($r[$f])) {
				return $r[$f];
			} else {
				return 0;}
		}
		
		/*
		 *	Individual district
		 */			
		 
	} // end of class


?>