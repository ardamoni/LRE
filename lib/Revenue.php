<?php

	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class Revenue
	{
		/*
		 *	PROPERTY
		 */
		
		// 	get the data out of property table
		function getPropertyInfo( $upn = "", $subupn = "", $year = 2013, $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM 	`property` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		
		/*
		 *	PROPERTY_DUE
		 */
		// 	get the data out of property_due table
		function getPropertyDueInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			$q = mysql_query("SELECT * FROM 	`property_due` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");		
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		// 	get sum of due data from property_due table
		function getAnnualDueSum( $upn = "", $subupn = "", $year = "2013" )
		{			
			$q	= mysql_query("SELECT ( SUM(`rate_value`) +  SUM(`rate_impost_value`) + SUM(`feefi_value`) )
									AS `due` 
									FROM 	`property_due` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											`year` = '".$year."' ");	
			
			$r = mysql_fetch_array($q);
			return $r['due'];
		}

		
		/*
		 *	PROPERTY_PAYMENTS
		 */
		//   get the last entry from the property_payments table
		function getPropertyLastPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * FROM `property_payments` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' 
										ORDER BY `id` DESC 
										LIMIT 1 ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		//   get sum of payments for one year
		function getAnnualPaymentSum( $upn = "", $subupn = "", $year = "2013" )
		{
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`property_payments` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											`payment_date` > '".$year."' ");											
			$r = mysql_fetch_array($q);
			return $r['val'];
		}

		
		/*
		 *	PROPERTY_BALANCE
		 */
		//   get the data from the property_balance table
		function getPropertyBalanceInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{			
			$q = mysql_query("SELECT * FROM `property_balance` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND
												`year` = '".$year."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		
		// 	get the data out of business table
		function getBusinessInfo( $upn = "", $subupn = "", /*$year = 2013,*/ $f = "" )
		{
			//$q = mysql_query("SELECT * FROM `property` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' AND `year` = '".$year."' ");
			$q = mysql_query("SELECT * FROM `business` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
	}


?>