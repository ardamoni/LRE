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
		function getPropertyInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			if( $subupn != "" || $subupn != NULL || $subupn != "0" )
			{
				$q = mysql_query("SELECT * 	FROM 	`property` 
											WHERE 	`upn` = '".$upn."' AND 
													`subupn` = '".$subupn."' AND 
													`year` = '".$year."' ");
			}
			else
			{
				$q = mysql_query("SELECT * 	FROM 	`property` 
											WHERE 	`upn` = '".$upn."' AND 													
													`year` = '".$year."' ");
			}
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		/*
		 *	OWN_OWNER
		 */		
		// 	get the owner
		function getOwnerInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM 	`own_owner` WHERE 	`ownerid` = '".$id."' ");
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
		function getLastPropertyPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * FROM `property_payments` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												YEAR(`payment_date`) = '".$year."' 
										ORDER BY `id` DESC LIMIT 1 ");
										
			$r = mysql_fetch_array($q);
			$count = mysql_num_rows($q);
			if( $count != 1 )
			{
				//echo "ERROR: ", $count, "<br>";
				return 'error '.$count;
			}
			else
			{
				return $r[$f];
			}
		}
		
		//   get sum of payments for one year
		function getAnnualPaymentSum( $upn = "", $subupn = "", $year = "2013" )
		{
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`property_payments` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											YEAR(`payment_date`) = '".$year."' ");											
			$r = mysql_fetch_array($q);
			return $r['val'];
		}

		//   get the used tickets
		function getTicketsPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * 	FROM `property_payments` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												YEAR(`payment_date`) = '".$year."' AND
												`receipt_payment` = '".$f."'
										ORDER BY `id` DESC");
										
			$count = mysql_num_rows($q);
			// Unused ticket returns no rows
			if( $count == 0 )
			{				
				return false;
			}
			else
			{
				return true;
			}
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
		
		//   get balance for one year
		function getAnnualBalance( $upn = "", $subupn = "", $year = "2013" )
		{
			$q	= mysql_query("SELECT `balance` FROM `property_balance` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND
												`year` = '".$year."' ");											
			$r = mysql_fetch_array($q);
			return $r['balance'];
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