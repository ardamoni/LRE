<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Table view</title>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
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
	require_once('../lib/html_table.class.php');

	
session_start();
	// match UPN
	$query = mysql_query( "SELECT * FROM property");	
	
echo "inside showtable2";	
$tbl = new HTML_Table('', 'demoTbl', 1);

$tbl->addCaption('This is a tabular view on the data used for the map', 'cap', array('id'=> 'tblCap') );

/*$tbl->addColgroup();
  // span, class
   $tbl->addCol(1, 'title');
   $tbl->addCol(2, 'prices');
*/
// thead
$tbl->addTSection('thead');
$tbl->addRow();
    // arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
    // can include associative array of optional additional attributes
    		$tbl->addCell('UPN', 'first', 'header');
			$tbl->addCell('Subupn', '', 'header');
			$tbl->addCell('Year', '', 'header');
			$tbl->addCell('Town', '', 'header');
			$tbl->addCell('Locpl', '', 'header');
			$tbl->addCell('pay_status', '', 'header');
			$tbl->addCell('revenue_due', '', 'header');
			$tbl->addCell('revenue_collected', '', 'header');
			$tbl->addCell('revenue_balance', '', 'header');
			$tbl->addCell('collector', '', 'header');
			$tbl->addCell('collector_id', '', 'header');
			$tbl->addCell('date_payment', '', 'header');
			$tbl->addCell('regnumber', '', 'header');
			$tbl->addCell('streetname', '', 'header');
			$tbl->addCell('housenumber', '', 'header');
			$tbl->addCell('floor', '', 'header');
			$tbl->addCell('unit_planning', '', 'header');
			$tbl->addCell('zone_revenue', '', 'header');
			$tbl->addCell('locality_code', '', 'header');
			$tbl->addCell('business', '', 'header');
			$tbl->addCell('structurecode', '', 'header');
			$tbl->addCell('owner', '', 'header');
			$tbl->addCell('owneraddress', '', 'header');
			$tbl->addCell('owner_tel', '', 'header');
			$tbl->addCell('owner_email', '', 'header');
			$tbl->addCell('rooms', '', 'header');
			$tbl->addCell('year_construction', '', 'header');
			$tbl->addCell('property_type', '', 'header');
			$tbl->addCell('property_use', '', 'header');
			$tbl->addCell('persons', '', 'header');
			$tbl->addCell('roofing', '', 'header');
			$tbl->addCell('ownership_type', '', 'header');
			$tbl->addCell('constr_material', '', 'header');
			$tbl->addCell('storeys', '', 'header');
			$tbl->addCell('value_prop', '', 'header');
			$tbl->addCell('prop_descriptor', '', 'header');
			$tbl->addCell('planningpermit', '', 'header');
			$tbl->addCell('planningpermit_no', '', 'header');
			$tbl->addCell('buildingpermit', '', 'header');
			$tbl->addCell('buildingpermit_no', '', 'header');
			$tbl->addCell('comments', '', 'header');
			$tbl->addCell('utm_x', '', 'header');
			$tbl->addCell('utm_y', '', 'header');
			$tbl->addCell('area_m2', '', 'header');
			$tbl->addCell('district', '', 'header');
			$tbl->addCell('lastentry_person', '', 'header');
			$tbl->addCell('lastentry_date', '', 'header');
			$tbl->addCell('districtid', '', 'header');
			$tbl->addCell('subdistrictid', '', 'header');
			$tbl->addCell('zoneid', '', 'header');
			$tbl->addCell('doornumber', '', 'header');
			$tbl->addCell('ownerid', '', 'header');
			$tbl->addCell('prop_descriptor_title', '', 'header');
			$tbl->addCell('rate_code', '', 'header');
			$tbl->addCell('rate_impost_code', '', 'header');
			$tbl->addCell('property_type_title', '', 'header');
			$tbl->addCell('property_use_title', '', 'header');
			$tbl->addCell('ownership_type_title', '', 'header');
			$tbl->addCell('constr_material_title', '', 'header');
			$tbl->addCell('roofing_type_title', '', 'header');
			$tbl->addCell('date_start', '', 'header');
			$tbl->addCell('date_end', '', 'header');
			$tbl->addCell('activestatus', '', 'header');
			$tbl->addCell('assessed', '', 'header');

    
// tfoot
$tbl->addTSection('tfoot');
$tbl->addRow();
        // span all 3 columns
    $tbl->addCell('End of table view!', 'foot', 'data', array('colspan'=>3) );
    
// tbody
$tbl->addTSection('tbody');
    while ($row = mysql_fetch_array($query)) {
        $tbl->addRow();
	        $tbl->addCell($row[1]);
	        $tbl->addCell($row[2]);
	        $tbl->addCell($row[3]);
	        $tbl->addCell($row[5]);
/*	        $tbl->addCell('ekke');
	        $tbl->addCell($row[6]);
	        $tbl->addCell($row[7]);
	        $tbl->addCell($row[8]);
	        $tbl->addCell($row[9]);
	        $tbl->addCell($row[10]);
	        $tbl->addCell($row[11]);
	        $tbl->addCell($row[12]);
*//*      	 for ($x=1; $x<=10; $x++) //sizeof($row)
  		{
  		  $content=$row[$x];
	        $tbl->addCell($content);
//    print($content);
		 } 
*/	  }

    
echo $tbl->display();
	
?>
<p>Back to <a href="index.html">Index</a></p>

</body>
</html>