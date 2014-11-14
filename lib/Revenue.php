<?php
	/*
	 *	Class that gets the data from DB tables
	 *	PROPERTY AND BUSINESS
	 */
	class Revenue
	{
		// OBSOLETE: 15.07.2014 - Arben, use getBasicInfo
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
		 *  BASIC INFO
		 *  Tables: property, business
		 */ 
		function getBasicInfo( $upn = "", $subupn = "", $districtid = "", $type = "", $f = "" )
		{
			switch ($type) 
			{
				case "property":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{							
						$q = mysql_query("SELECT * 	FROM 	`property` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' ");
					}
					else
					{
						$q = mysql_query("SELECT * 	FROM 	`property` 
													WHERE 	`upn` = '".$upn."' AND 													
															`districtid` = '".$districtid."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];
				break;
				
				case "business":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{
						$q = mysql_query("SELECT * 	FROM 	`business` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' ");
					}
					else
					{
						$q = mysql_query("SELECT * 	FROM 	`business` 
													WHERE 	`upn` = '".$upn."' AND 													
															`districtid` = '".$districtid."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];	
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of function getBasicInfo
		
		// 	OBSOLETE -15.07.2014 - Arben - use getDueInfo
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
		
		/*
		 *  DUE INFO
		 *  Tables: property_due, business_due
		 */ 
		function getDueInfo( $upn = "", $subupn = "", $districtid = "", $year = "2013", $type = "", $f = "" )
		{
			switch ($type) 
			{
				case "property":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{							
						$q = mysql_query("SELECT * 	FROM 	`property_due` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					else
					{
						$q = mysql_query("SELECT * 	FROM 	`property_due` 
													WHERE 	`upn` = '".$upn."' AND 													
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];
				break;
				
				case "business":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{
						$q = mysql_query("SELECT * 	FROM 	`business_due` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					else
					{
						$q = mysql_query("SELECT * 	FROM 	`business_due` 
													WHERE 	`upn` = '".$upn."' AND 													
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];	
				break;
			 
				default:
					return "Your type of entity is not set!";
			}		
		} // end of getDueInfo function
		
		// OBSOLETE - 15.07.014 - Arben - use getDueInfo up to 4 times - rate_value, rate_impost_value, feefi_value, prop_value
		// it was used in propertyAnnualBill and propertyAnnualBill_One
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

		// 	NOT USED !!! 
		// OBSOLETE because of logic change.   
		// Due tables are filled once in the beggining of the year.   so there is one row for each property / business
		// therefore no need to use the sum('x')
		// you can use getDueInfo(...), up to 4 times - rate_value, rate_impost_value, feefi_value, prop_value
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

		// OBSOLETE - 15.07.2014 - Arben - use getLastPaymentInfo
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

		/*
		 *  LAST PAYMENTS INFO
		 *  Tables: property_payments, business_payments
		 */	
		function getLastPaymentInfo( $upn = "", $subupn = "", $districtid = "", $year = "2013", $type = "", $f = "" )
		{
			switch( $type ) 
			{
				case "property":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{							
						$q = mysql_query(" SELECT * FROM 	`property_payments` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' 
													ORDER BY `id` DESC LIMIT 1 ");
					}
					else
					{
						$q = mysql_query(" SELECT * FROM 	`property_payments` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' 
													ORDER BY `id` DESC LIMIT 1 ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];						
				break;
				
				case "business":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{
						$q = mysql_query("SELECT * FROM 	`business_payments` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' 
													ORDER BY `id` DESC LIMIT 1 ");
					}
					else
					{
						$q = mysql_query("SELECT * FROM 	`business_payments` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' 
													ORDER BY `id` DESC LIMIT 1 ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];	
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of getLastPaymentInfo function		
		
		// OBSOLETE 15.07.014 - Arben - use getSumPaymentInfo
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

		/*
		 *  SUM PAYMENTS INFO
		 *  Tables: property_payments, business_payments
		 */	
		function getSumPaymentInfo( $upn = "", $subupn = "", $districtid = "", $year = "2013", $type = "" )
		{
			switch( $type ) 
			{
				case "property":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{							
						$q = mysql_query(" SELECT SUM(`payment_value`) AS `val`
													FROM 	`property_payments` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' ");
					}
					else
					{
						$q = mysql_query(" SELECT SUM(`payment_value`) AS `val`
													FROM 	`property_payments` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r['val'];						
				break;
				
				case "business":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{
						$q = mysql_query("SELECT SUM(`payment_value`) AS `val`
													FROM 	`business_payments` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND 
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' ");
					}
					else
					{
						$q = mysql_query("SELECT SUM(`payment_value`) AS `val`
													FROM 	`business_payments` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r['val'];
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of getSumPaymentInfo function		
		
		/*
		 *	GET USED TICKETS
		 *	Tables: property_payments
		 */
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
		
		// OBSOLETE - use getBalanceInfo
		function getPropertyBalanceInfo( $upn = "", $subupn = "", $year = "2013", $f = "" )
		{			
			$q = mysql_query("SELECT * FROM `property_balance` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' AND
												`year` = '".$year."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		/*
		 *	BALANCE INFO
		 *	Tables: property_balance, business_balance
		 */
		function getBalanceInfo( $upn = "", $subupn = "", $districtid = "", $year = "2013", $type = "", $f = "" )
		{	
			switch( $type ) 
			{
				case "property":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{							
						$q = mysql_query(" SELECT * FROM 	`property_balance` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					else
					{
						$q = mysql_query(" SELECT * FROM 	`property_balance` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];						
				break;
				
				case "business":
					if( $subupn != "" || $subupn != NULL || $subupn != "0" )
					{
						$q = mysql_query(" SELECT * FROM 	`business_balance` 
													WHERE 	`upn` = '".$upn."' AND 
															`subupn` = '".$subupn."' AND
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					else
					{
						$q = mysql_query(" SELECT * FROM 	`business_balance` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					}
					$r = mysql_fetch_array($q);
					return $r[$f];	
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of function getBalanceInfo

		/*
		 *	BALANCE TOTAL 
		 *	Tables: property_balance, business_balance
		 */
		function getBalanceTotal( $upn = "", $districtid = "", $year = "2013", $type = "", $f = "" )
		{	
			switch( $type ) 
			{
				case "property":
						$q = mysql_query(" SELECT SUM(`balance`) as sumbalance FROM 	`property_balance` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					$r = mysql_fetch_array($q);
 					return $r[$f];						
				break;
				
				case "business":
						$q = mysql_query(" SELECT SUM(`balance`) as 'sumbalance' FROM 	`business_balance` 
													WHERE 	`upn` = '".$upn."' AND 															
															`districtid` = '".$districtid."' AND
															`year` = '".$year."' ");
					$r = mysql_fetch_array($q);
					return $r[$f];	
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of function getBalanceTotalSubupn
		
		// OBSOLETE - use getBalanceInfo where $f = 'balance'
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
		
		
		// 	OBSOLETE - 15.07.2014
		// get the data out of business table
		function getBusinessInfo( $upn = "", $subupn = "", /*$year = 2013,*/ $f = "" )
		{
			//$q = mysql_query("SELECT * FROM `property` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' AND `year` = '".$year."' ");
			$q = mysql_query("SELECT * FROM `business` WHERE `upn` = '".$upn."' AND `subupn` = '".$subupn."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}
		
		/*
		 *	OWNER INFO
		 *	TABLES: own_owner
		 */		
		function getOwnerInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * FROM `own_owner` WHERE `id` = '".$id."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		
		/*
		 *	District Area
		 *  Tables: area_district
		 */		
		function getDistrictInfo( $id = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `area_district` WHERE 	`districtid` = '".$id."' ");
			$r = mysql_fetch_array($q);
			return $r[$f];
		}

		// OBSOLETE - 15.07.2014 Arben - use getFeeFixingInfo
		// 	get the district info
		function getFeeFixingClassInfo( $id = "", $code = "", $f = "" )
		{
			$q = mysql_query("SELECT * 	FROM `fee_fixing_property` WHERE `districtid` = '".$id."' AND `code` = '".$code."' ");
			$r = mysql_fetch_array($q);
			return $r['class'];
		}
		
		/*
		 *	FEE FIXING INFO
		 *	Tables: fee_fixing_property, fee_fixing_business 
		 */
		function getFeeFixingInfo( $districtid = "", $code = "", $year = "2013", $type = "", $f = "" )
		{
			switch( $type ) 
			{
				case "property":
					$q = mysql_query(" SELECT 	* 
										FROM 	`fee_fixing_property` 
										WHERE 	`districtid` = '".$districtid."' AND 
												`code` = '".$code."' AND 
												`year` = '".$year."' ");
					$r = mysql_fetch_array($q);
					return $r[$f];						
				break;
				
				case "business":
					$q = mysql_query(" SELECT 	* 
										FROM 	`fee_fixing_business` 
										WHERE 	`districtid` = '".$districtid."' AND 
												`code` = '".$code."' AND 
												`year` = '".$year."' ");
					$r = mysql_fetch_array($q);					
					return $r[$f];
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			
		} // end of getFeeFixingInfo
		
		// OBSOLETE - use getBalanceInfo
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
		 *	SET DEMAND NOTICE RECORD
		 *	Tables: demand_notice_record
		 */		
		function setDemandNoticeRecord( $upn = "", $subupn = "", $districtid = "", $year = "2013", $type = "", $value = 0 )
		{
			if( $type == "" ) return "Your type of entity is not set!";
			else 
			{	
				$q = mysql_query(" SELECT 	* 	
									FROM 	`demand_notice_record` 
									WHERE 	`upn` = '".$upn."' AND 
											`subupn` = '".$subupn."' AND 
											`districtid` = '".$districtid."' AND 
											`year` = '".$year."' AND 
											`comments` = '".$type."' ");
				$r = mysql_fetch_array($q);
				if( !empty($r) ) 
				{
					mysql_query(" UPDATE 	`demand_notice_record` 
									SET 	`value`= '".$value."',
											`billprintdate`= '".date("Y-m-d")."'										
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' AND
											`districtid` = '".$districtid."' AND 
											`year` = '".$year."' AND 
											`comments` = '".$type."' ");	
				} else {
					mysql_query(" INSERT INTO `demand_notice_record` 
											(	`id`,
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
												'".$type."' 
											)");

					return mysql_affected_rows();
				}
			}
		} // end of setDemandNoticeRecord function

	  	/*
		 *	checkOnDuplicateUPNInKML
		 *	Tables: property/business, KML_from_LUPMIS
		 */		
		function checkOnDuplicateUPNInKML( $districtid = "",  $type = ""  )
		{
			switch( $type ) 
			{
				case "property":
						$q = mysql_query("SELECT d1.`UPN` as NewUPN, d2.`UPN` as OldUPN, (Select d3.`upn` FROM property d3 where d3.`upn`=d2.`UPN` group BY d3.`upn` ) as PropertyUPN FROM `KML_from_LUPMIS` d1 inner join `KML_from_LUPMIS_copy_2014Oct28` d2 on d1.`boundary`=d2.`boundary`  where d1.`districtid` = '".$districtid."' AND d1.`UPN`!=d2.`UPN`;");
				break;
				
				case "business":
						$q = mysql_query("SELECT d1.`UPN` as NewUPN, d2.`UPN` as OldUPN, (Select d3.`upn` FROM `business` d3 where d3.`upn`=d2.`UPN`  group BY d3.`upn` ) as BusinessUPN FROM `KML_from_LUPMIS` d1 inner join `KML_from_LUPMIS_copy_2014Oct28` d2 on d1.`boundary`=d2.`boundary`  where d1.`districtid` = '".$districtid."' AND d1.`UPN`!=d2.`UPN`;");
				break;
			 
				default:
		}
			while ($r = mysql_fetch_assoc($q)) 
			{
				$rdata = array();
				$rdata 	= trim($r['NewUPN']);
				$rd[] 		= $rdata;

			}
			return $rd; 
		} // end of checkOnDuplicateUPNInKML function


	}	//end of Revenue class
?>