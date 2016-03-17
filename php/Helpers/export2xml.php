<?php
require_once("../../lib/configuration.php");

$districtid = '130';

	try {
		$conn = new db(cDsn, cUser, cPass);
	} catch(PDOException $e) {
		die('Could not connect to the database:<br/>' . $e->getMessage());
	}
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
//			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bind = array(
		":districtid" => $districtid
	);
	$st = $pdo->prepare('SELECT *  FROM `area_district`  WHERE `districtid`=:districtid;');
    $res =	$st->execute($bind);

    $area_district = $st->fetchAll();

	$st = $pdo->prepare('SELECT `district_name` as "District", year as "Year", code, class, Units, round(sum(sumvalues)) as "erev" FROM

		 ((select d3.`district_name`, d2.year, d2.`code`, d2.`class`,d2.`rate`, count(d2.`rate`) as Units, round(sum(d1.`feefi_value`),2) as sumvalues from `property_due` d1 JOIN `KML_from_LUPMIS` d4 ON d1.`upn` = d4.`upn`, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=:districtid AND d1.`rate_value`=0  AND d1.`districtid`=d2.`districtid` AND d1.`feefi_code`=d2.`code` AND d2.`districtid`=d3.`districtid` GROUP BY d1.`districtid`, d2.`year`, d2.`code`)

		 UNION

		 (select d3.`district_name`, d2.year, d2.`code`, d2.`class`,d2.`rate`, count(d2.`rate`) as Units, round(sum(d1.`rate_value`),2) as sumvalues from `property_due` d1 JOIN `KML_from_LUPMIS` d4 ON d1.`upn` = d4.`upn`, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=:districtid AND d1.`rate_value`>0 AND d1.`districtid`=d2.`districtid` AND d1.`feefi_code`=d2.`code` AND d2.`districtid`=d3.`districtid` GROUP BY d1.`districtid`, d2.`year`, d2.`code`))

		 t  group by code, year;');
    $res =	$st->execute($bind);
    $records = $st->fetchAll();
//   var_dump($records);
  $doc = new DOMDocument('1.0', 'utf-8');
  $doc->formatOutput = true;
  $doc->preserveWhiteSpace = false;

  $district = $records[0]['District'];

  $r = $doc->createElement( "District");
   $doc->appendChild( $r );


  $dName = $doc->createElement("DistrictName");
  $dName->appendChild(
	 	 $doc->createTextNode( $district)
	  );
	$r->appendChild( $dName );

  $dId = $doc->createElement("DistrictId");
  $dId->appendChild(
	 	 $doc->createTextNode( $area_district[0]['districtid'])
	  );
	$r->appendChild( $dId );

  $dNameCoA = $doc->createElement("DistrictNameCoA");
  $dNameCoA->appendChild(
	 	 $doc->createTextNode( $area_district[0]['districtnameCoA'])
	  );
	$r->appendChild( $dNameCoA );

  $dCoARegionId = $doc->createElement("DistrictRegionCoA");
  $dCoARegionId->appendChild(
	 	 $doc->createTextNode( $area_district[0]['coa-regionid'])
	  );
	$r->appendChild( $dCoARegionId );

  $dCoAdistrictId = $doc->createElement("DistrictIdCoA");
  $dCoAdistrictId->appendChild(
	 	 $doc->createTextNode( $area_district[0]['coa-districtid'])
	  );
	$r->appendChild( $dCoAdistrictId );

  $dCoAdisttypeId = $doc->createElement("DistrictTypeIdCoA");
  $dCoAdisttypeId->appendChild(
	 	 $doc->createTextNode( $area_district[0]['coa-disttypeid'])
	  );
	$r->appendChild( $dCoAdisttypeId );

  $dCoAsubmetroId = $doc->createElement("SubmetroIdCoA");
  $dCoAsubmetroId->appendChild(
	 	 $doc->createTextNode( $area_district[0]['coa-submetroid'])
	  );
	$r->appendChild( $dCoAsubmetroId );

  foreach( $records as $record )
  {
  $b = $doc->createElement( "Code" );
  $cNr = $doc->createElement("CodeID");
  $cNr->appendChild(
	 	 $doc->createTextNode( $record['code'])
	  );
	$b->appendChild( $cNr );
		$c = $doc->createElement( "Details" );


				  $r->appendChild( $b );

	$year = $doc->createElement( "Year" );
		  $year->appendChild(
			$doc->createTextNode( $record['year'] )
		  );
		  $c->appendChild( $year );
		  $units = $doc->createElement( "Units" );
		  $units->appendChild(
			  $doc->createTextNode( $record['Units'] )
		  );
		  $c->appendChild( $units );

		  $revenue = $doc->createElement( "Revenue" );
		  $revenue->appendChild(
			  $doc->createTextNode( $record['erev'] )
		  );
	      $c->appendChild( $revenue );

	$code = $record['code'];
			$b->appendChild( $c );

  }

	echo 'Wrote: ' . $doc->save("../../tmp/test2.xml") . ' bytes'; // Wrote: 72 bytes
?>