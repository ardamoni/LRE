<?php
 	require_once( "configuration.php"	);

	/*
	 *	System Class
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	class System
	{
		/*
		 *	Manage: YEAR
		 ************************************************
		 */
		function GetConfiguration($id = "")
		{
// 			$q = mysql_query("SELECT * FROM `system_config` WHERE `variable` = '".$id."'");
// 			$r = mysql_fetch_array($q);
// 			$count = mysql_num_rows($q);

		 try {
				$conn = new PDO(cDsn, cUser, cPass);
				$stmt = $conn->prepare(" SELECT * FROM `system_config` WHERE `variable` = :id");
				if (!$stmt->execute(array('id' => $id)))
				  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
				$count = $stmt->rowCount();
		//		echo $count.' count';
			}
		//
			catch(PDOException $e) {
					echo 'Error:<br/>' . $e->getMessage();
			}
			 if ($count==0)		//table system_config is missing a row with the needed information, i.e. table is outdated
				{
					return 'empty';
				}
			elseif ($count==1)  //table system_config contains the needed information
				{
					$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $r[0]['value'];
// 			return $r['value'];
				}

		}

			/*
		 *	SYSTEM FEE FIXING PROPERTY THRESHOLD
		 *	Tables: system_fee_fixing_property_thresholf
		 */
		function getFeeFixingPropertyThreshold( $districtid = "", $year = "2013", $f = "" )
		{
		 try {
				$conn = new PDO(cDsn, cUser, cPass);
				$stmt = $conn->prepare(" SELECT 	*
										FROM 	`system_fee_fixing_property_threshold`
										WHERE 	`districtid` = :districtid AND
												`year` = :year");
				if (!$stmt->execute(array('districtid' => $districtid,
											'year'=>$year)))
				  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
				$count = $stmt->rowCount();
		//		echo $count.' count';
			}
		//
			catch(PDOException $e) {
					echo 'Error:<br/>' . $e->getMessage();
			}
					 if ($count==0)		//table system_fee_fixing_property_threshold is missing a row with the threshold information for this district
						{
							return 0;
						}
					elseif ($count==1)  //table system_config contains the needed information
						{
							$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
						return $r[0]['rate'];
						}

		} // end of getFeeFixingPropertyThreshold

	} //end of System Class
?>
