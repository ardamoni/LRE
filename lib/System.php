<?php
 	require_once( "../lib/configuration.php"	);

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
		$st = $conn->prepare(" SELECT * FROM `system_config` WHERE `variable` = :id");
		if (!$st->execute(array('id' => $id))) 
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
		$count = $st->rowCount();
//		echo $count.' count';
	} 
	
	catch(PDOException $e) {
			echo 'Error:<br/>' . $e->getMessage();
	}
			 if ($count==0)		//table system_config is missing a row with the needed information, i.e. table is outdated
				{
					return 'empty';
				}
			elseif ($count==1)  //table system_config contains the needed information
				{
					$r = $st->fetchAll(PDO::FETCH_ASSOC);
					return $r[0]['value'];
// 			return $r['value'];
				}
			
		}
		
	}
?>
