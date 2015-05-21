<?php
	/*
	 *	Class that gets the data from DB tables
	 *	PROPERTY AND BUSINESS
	 */
	class Revenue
	{

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

				case "feesandfines":
						$q = mysql_query("SELECT * 	FROM 	`fees_fines`
													WHERE 	`upn` = '".$upn."' AND
															`districtid` = '".$districtid."' ");
					$r = mysql_fetch_array($q);
					return $r[$f];
				break;
				default:
					return "Your type of entity is not set!";
			}
		} // end of function getBasicInfo

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

				case "feesandfines":
						$q = mysql_query("SELECT * FROM 	`fees_fines_payments`
													WHERE 	`upn` = '".$upn."' AND
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."'
													ORDER BY `id` DESC LIMIT 1 ");
					$r = mysql_fetch_array($q);
					return $r[$f];
				break;

				default:
					return "Your type of entity is not set!";
			}
		} // end of getLastPaymentInfo function

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

				case "feesandfines":
						$q = mysql_query("SELECT SUM(`payment_value`) AS `val`
													FROM 	`fees_fines_payments`
													WHERE 	`upn` = '".$upn."' AND
															`districtid` = '".$districtid."' AND
															YEAR(`payment_date`) = '".$year."' ");
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
		function getTicketsPaymentInfo( $upn = "", $subupn = "", $year = "2013", $f = "", $type = "" )
		{
			switch( $type )
			{
				case "property":

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
						return '0';//false;
					}
					else
					{
						return '1';//true;
					}
				break;

				case "business":
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

				break;

				case "feesandfines":
					// the newest entry in the table
					$q = mysql_query("SELECT * 	FROM `fees_fines_payments`
												WHERE 	`upn` = '".$upn."' AND
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

				break;

				default:
					return "Your type of entity is not set!";
			}
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

				case "feesandfines":
					$q = mysql_query(" SELECT 	*
										FROM 	`fee_fixing_feesfines`
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
		 *  was used to check an updated locla plan and to see whether one locations was used at two or more upns
		 *  at the moment produces and SQL error if the old KML_from_LUPMIS table does not exist anymore
		 *  It is used in BillsRegister.php line 22
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