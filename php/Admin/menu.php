<?php
 
	/*
	 *	Include the Library Code
	 *	-----------------------------------------------------------------------
	 */
	require_once("../../lib/configuration.php");
	
?>
<html>
	<head>
	<title>Menu Execution</title>
    
    <link rel = "stylesheet" type = "text/css" href = "Admin.css">
	<script type = "text/javascript" language = "JavaScript1.2" src = "dtree.js"></script>
	<script type = "text/javascript" language = "JavaScript1.2" src = "menu.js"></script>
      
	<script language = "JavaScript1.2">
	  
	  var tmenuItems =
	  [
<?php 
		if($_GET['id'] == "1")
		{ 
?>
		["+Districts", "", "icon3.gif", "icon3o.gif", "", "District",,"1","0"],
<?php	
			$qr = mysql_query("SELECT * FROM `area_region` WHERE `id` > '0' ORDER BY `id`");		
			
			while( $rr = mysql_fetch_array($qr) )
			{
				echo '["|'.$rr['regionid'].' - '.$rr['region_name'].'", "content.php?mod=edit.region&id='.$rr['regionid'].'", "icon3.gif", "icon3o.gif", "", "'.$rr['regionid'].' - '.$rr['region_name'].'", "content"],';
				
				$qd = mysql_query("SELECT * FROM `area_district` WHERE `id` > '0' AND `regionid` = '".$rr['regionid']."' ORDER BY `districtid`");		
			
				while( $rd = mysql_fetch_array($qd) )
				{
					echo '["||'.$rd['districtid'].' - '.$rd['district_name'].'", "content.php?mod=edit.district&id='.$rd['districtid'].'", "", "", "", "'.$rd['districtid'].' - '.$rd['district_name'].'", "content"],';
				}
			}
		}
?>
		
<?php 
		if($_GET['id'] == "2")
		{ 
?>
		["+Users", "", "", "", "", "Users",,"1","0"],
<?php		
			echo '["|Users by District", "", "icon3.gif", "icon3o.gif", "", "Users by District"],';
			
			$qr = mysql_query("SELECT * FROM `area_region` WHERE `id` > '0' ORDER BY `id`");		
			
			while( $rr = mysql_fetch_array($qr) )
			{
				echo '["||'.$rr['regionid'].' - '.$rr['region_name'].'", "", "icon3.gif", "icon3o.gif", "", "'.$rr['regionid'].' - '.$rr['region_name'].'", "content"],';
				
				$qd = mysql_query("SELECT * FROM `area_district` WHERE `id` > '0' AND `regionid` = '".$rr['regionid']."' ORDER BY `districtid`");		
			
				while( $rd = mysql_fetch_array($qd) )
				{
					echo '["|||'.$rd['districtid'].' - '.$rd['district_name'].'", "", "icon3.gif", "icon3o.gif", "", "'.$rd['districtid'].' - '.$rd['district_name'].'", "content"],';
					
					$que = mysql_query("SELECT * FROM 	`usr_user_district` `b`, `usr_users` `s` 
												WHERE 	`b`.`username` = `s`.`username` AND 
														`regionid` = '".$rd['regionid']."' AND
														`districtid` = '".$rd['districtid']."' 
												ORDER BY `s`.`username` ASC");	
					
					while( $rue = mysql_fetch_array($que) )
					{
						echo '["||||'.$rue['name'].'", "content.php?mod=user-management&id='.$rue['username'].'", "icon3.gif", "icon3o.gif", "", "'.$rue['districtid'].' - '.$rue['username'].'", "content"],';				
					}
				}
			}		
		}			
?>
		
<?php 
		if($_GET['id'] == "3")
		{ 
?>
		["+Functions", "", "", "", "", "Functions",,"1","0"],
<?php
			echo '["|Functions", "", "icon3.gif", "icon3o.gif", "", "Functions"],';			
		}	
?>

<?php 
		if($_GET['id'] == "4")
		{ 
?>
		["+Labels", "", "", "", "", "Labels",,"1","0"],
			["|Edit Labels", "content.php?mod=edit.labels", "icon3.gif", "icon3o.gif", "", "", "content"],
<?php 
		} 
?>

	  ];

	  dtree_init();
	  
	</script>
<link rel = "stylesheet" href = "style.css" type = "text/css">