<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Table view</title>
<link rel="stylesheet" href="css/ex.css" type="text/css" />
<style type="text/css">
table.demoTbl {
    border-collapse: collapse;
    border-spacing: 0;
}

#tblCap {
    font-weight:bold;
    margin:1em auto .4em;
}

table.demoTbl .title {
    width:200px;
}
table.demoTbl .prices {
    width:120px;
}

table.demoTbl td, table.demoTbl th {
    padding: 6px;
}

table.demoTbl th.first {
    text-align:left;
    }
table.demoTbl td.num {
    text-align:right;
    }
    
table.demoTbl td.foot {
    text-align: center;
}

</style>
</head>
<body>
    

<?php
	// DB connection
	require_once( "../lib/configuration.php"	);
	
session_start();
	// match UPN
	$query = mysql_query( "SELECT * FROM property");	
	
	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
			<tr>
			<th>UPN</th>
			<th>Subupn</th>
			<th>Year</th>
			<th>Town</th>
			<th>Locpl</th>
			<th>pay_status</th>
			<th>revenue_due</th>
			<th>revenue_collected</th>
			<th>revenue_balance</th>
			<th>collector</th>
			<th>collector_id</th>
			<th>date_payment</th>
			<th>regnumber</th>
			<th>streetname</th>
			<th>housenumber</th>
			<th>floor</th>
			<th>unit_planning</th>
			<th>zone_revenue</th>
			<th>locality_code</th>
			<th>business</th>
			<th>structurecode</th>
			<th>owner</th>
			<th>owneraddress</th>
			<th>owner_tel</th>
			<th>owner_email</th>
			<th>rooms</th>
			<th>year_construction</th>
			<th>property_type</th>
			<th>property_use</th>
			<th>persons</th>
			<th>roofing</th>
			<th>ownership_type</th>
			<th>constr_material</th>
			<th>storeys</th>
			<th>value_prop</th>
			<th>prop_descriptor</th>
			<th>planningpermit</th>
			<th>planningpermit_no</th>
			<th>buildingpermit</th>
			<th>buildingpermit_no</th>
			<th>comments</th>
			<th>utm_x</th>
			<th>utm_y</th>
			<th>area_m2</th>
			<th>district</th>
			<th>lastentry_person</th>
			<th>lastentry_date</th>
			<th>districtid</th>
			<th>subdistrictid</th>
			<th>zoneid</th>
			<th>doornumber</th>
			<th>ownerid</th>
			<th>prop_descriptor_title</th>
			<th>rate_code</th>
			<th>rate_impost_code</th>
			<th>property_type_title</th>
			<th>property_use_title</th>
			<th>ownership_type_title</th>
			<th>constr_material_title</th>
			<th>roofing_type_title</th>
			<th>date_start</th>
			<th>date_end</th>
			<th>activestatus</th>
			<th>assessed</th>

			</tr>";


	while( $row = mysql_fetch_array( $query,MYSQLI_NUM ) ) 
	{
	 echo "<tr>";
	 for ($x=1; $x<=sizeof($row); $x++)
  		{
		  echo "<td>" . $row[$x] . "</td>";
		 } 
	  echo "</tr>";
	  }
	
   echo "</table>";
?>
<p>Back to <a href="index.html">Index</a></p>

</body>
</html>