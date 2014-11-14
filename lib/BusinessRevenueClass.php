<?php
	// OBSOLETE CLASS - DELETE when convinient
	/*
	 *	Class that gets the data from DB tables
	 *
	 */
	class BusinessRevenue
	{
		// 	OBSOLETE 15.07.2014 - Arben - use getBasicInfo in Revenue
		function getBusinessInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			if( $subupn != "" || $subupn != NULL || $subupn != "0" )
			{
				$q = mysql_query("SELECT * 	FROM 	`business` 
											WHERE 	`upn` = '".$upn."' AND 
													`subupn` = '".$subupn."' ");
			}
			else
			{
				$q = mysql_query("SELECT * 	FROM 	`business` 
											WHERE 	`upn` = '".$upn."' ");
			}
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		// OBSOLETE - 15.07.2014 - Arben - use getDueInfo @ Revenue class
		function getPropertyDueInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			$q = mysql_query("SELECT * FROM 	`business_due` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");		
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		// OBSOLETE - 15.07.2014 - Arben - use getDueInfo @ Revenue class
		function getDueInfoAll( $upn = "", $subupn = "", $year = "2013")
		{
			$q = mysql_query("SELECT * FROM 	`business_due` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND 
												`year` = '".$year."' ");		
			$r = mysql_fetch_array($q);
			return array("rate_value"=>$r["bo_value"],
										  "bo_impost_value"=>$r["rate_impost_value"],
										  "feefi_value"=>$r["feefi_value"]);
		}		

		// OBSOLETE 15.07.014 - Arben - use getDueInfo (up to 4 times - rate_value, rate_impost_value, feefi_value, prop_value) @ Revenue
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
									FROM 	`business_due` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											`year` = '".$year."' ");	
			
			$r = mysql_fetch_array($q);
			return $r['due'];
		}

		// OBSOLETE 15.07.014 - Arben - use getLastPaymentInfo @ Revenue
		function getLastPropertyPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * FROM `business_payments` 
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
		
		// OBSOLETE 15.07.014 - Arben - use getSumPaymentInfo @ Revenue
		function getAnnualPaymentSum( $upn = "", $subupn = "", $year = "2013" )
		{
		  if (!empty($subupn)) {
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`business_payments` 
									WHERE	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											YEAR(`payment_date`) = '".$year."' ");
			} else {
			$q	= mysql_query("SELECT SUM(`payment_value`) AS `val` 
									FROM 	`business_payments` 
									WHERE	`upn` = '".$upn."' AND 
											YEAR(`payment_date`) = '".$year."' ");
			}
			$r = mysql_fetch_array($q);
			return $r['val'];
		}

		// OBSOLETE - 15.07.2014 - Arben - there are not tickets for businesses
		function getTicketsPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{
			// the newest entry in the table 
			$q = mysql_query("SELECT * 	FROM `business_payments` 
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
		
		// OBSOLETE - 15.07.2014 - Arben - use getBalanceInfo @ Revenue class 
		function getPropertyBalanceInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{			
			$q = mysql_query("SELECT * FROM `business_balance` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND
												`year` = '".$year."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		// OBSOLETE - 15.07.2014 - Arben - use getBalanceInfo @ Revenue class
		function getAnnualBalance( $upn = "", $subupn = "", $year = "2013" )
		{
			$q	= mysql_query("SELECT `balance` FROM `business_balance` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND
												`year` = '".$year."' ");											
			$r = mysql_fetch_array($q);
			return $r['balance'];
		}
	
		// OBSOLETE - 15.07.2014 - Arben - use getDistrictInfo @ Revenue
		function getDistrictInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `area_district` WHERE 	`districtid` = '".$id."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		// OBSOLETE - 15.07.2014 - Arben - use getFeeFixingInfo	
		function getFeeFixingClassInfo( $id = "", $code = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `fee_fixing_business` WHERE `districtid` = '".$id."' AND `code` = '".$code."' ");
			$r = mysql_fetch_array($q);
			return $r['class'];
		}

		// OBSOLETE - 15.07.2014 - Arben - use setDemandNoticeRecord @ Revenue
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
	}


?>