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
		 *	PROPERTY_DUE
		 */
		// 	get the data out of property_due table
		function getPropertyDueInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
//			$q = mysql_query("SELECT * FROM 	`property_due` 
			$q = mysql_query("SELECT '".$f."' FROM 	`property_due` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");		
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		function getPropertyDueInfoAll( $upn = "", $subupn = "", $year = "2013")
		{
			$q = mysql_query("SELECT * FROM 	`property_due` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");		
			$r = mysql_fetch_array($q);
			return array("rate_value"=>$r["rate_value"],
										  "rate_impost_value"=>$r["rate_impost_value"],
										  "feefi_value"=>$r["feefi_value"],
										  "prop_value"=>$r["prop_value"]);
		}		
		// 	get sum of due data from property_due table
		function getAnnualDueSum( $upn = "", $subupn = "", $year = "2013" )
		{
// ??? !!! ekke ALERT THIS NEEDS ATTENTION WHEN valuation is done
// 			$q	= mysql_query("SELECT ( SUM(`rate_value`) +  SUM(`rate_impost_value`) + SUM(`feefi_value`) )
// 									AS `due` 
// 									FROM 	`property_due` 
// 									WHERE	`upn` = '".$upn."' AND 
// 											`subupn` = '".$subupn."' AND 
// 											`year` = '".$year."' ");	
			$q	= mysql_query("SELECT ( SUM(`feefi_value`) )
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
		  if (!empty($subupn)) {
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`property_payments` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											YEAR(`payment_date`) = '".$year."' ");
			} else {
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`property_payments` 
									WHERE	`upn` = '".$upn."' AND 
											YEAR(`payment_date`) = '".$year."' ");
			}
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
		
		/*
		 *	OWN_OWNER
		 */		
		// 	get the owner
		function getOwnerInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM 	`own_owner` WHERE 	`id` = '".$id."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		
		/*
		 *	District Area
		 */		
		// 	get the district info
		function getDistrictInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `area_district` WHERE 	`districtid` = '".$id."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		/*
		 *	Property Use
		 */		
		// 	get the district info
		function getFeeFixingClassInfo( $id = "", $code = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `fee_fixing_property` WHERE `districtid` = '".$id."' AND `code` = '".$code."' ");
			$r = mysql_fetch_array($q);
			return $r['class'];
		}
		
		//   get balance for one year
	  function getEndBalance( $upn = "", $subupn = "", $districtid = "", $year = "2013" )
	  {
	   $q = mysql_query("  SELECT  `balance` 
			 FROM  `property_balance` 
			 WHERE  `upn` = '".$upn."' AND 
			   `subupn` = '".$subupn."' AND
			   `districtid` = '".$districtid."' AND
			   `year` = '".$year."' "); 
	   
	   $r = mysql_fetch_array($q);
	   return $r['balance'];
	  }
	  		/*
		 *	Update demand notice record
		 */		
		// 	get the district info
		function setDemandNoticeRecord( $districtid = "",  $upn = "", $subupn = "", $year = "2013", $value = 0, $revitem = "" )
		{
			$q = mysql_query("SELECT * 	FROM `demand_notice_record` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' AND `districtid` = '".$districtid."' AND `year` = '".$year."' ");
			$r = mysql_fetch_array($q);
			if (!empty($r)) {
				$qupdate = mysql_query(" UPDATE 	`demand_notice_record` 
									SET 	`upn` = '".$upn."',
											`subupn`= '".$subupn."',
											`districtid`= '".$districtid."',
											`year`= '".$year."',
											`value`= '".$value."',
											`billprintdate`= '".date("Y-m-d")."',
											`comments`= '".$revitem."'
									
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' AND
											`year` = '".$year."' ");	
			}elseif (empty($r)){
				$qinfo = mysql_query(" INSERT INTO `demand_notice_record` (	`id`,
															`upn`, 
															`subupn`,
															`districtid`,
															`year`,	
															`value`,														
															`billprintdate`,
															`comments`
														) 
												VALUES 	( 	NULL,
															'".$upn."',
															'".$subupn."',
															'".$districtid."',
															'".$year."',
															'".$value."',
															'".date("Y-m-d")."',
															'".$revitem."'
														)");

			return mysql_affected_rows();
		}


	}

}//end of class
?>