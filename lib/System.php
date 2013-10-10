<?php

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
			$q = mysql_query("SELECT * FROM `system_config` WHERE `variable` = '".$id."'");
			$r = mysql_fetch_array($q);
			
			return $r['value'];
		}
		
	}
?>
