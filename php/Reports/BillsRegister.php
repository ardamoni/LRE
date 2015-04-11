<?php
	// UPN or SUBUPN History
	// if SUBUPN exists it is usually more than one and therefore get's in as Array

	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);
//	require_once(	"../../lib/System.PDF.php"		);
	require_once(	"../../lib/System.PDF.AnnualBill.php"		);
	require_once( 	"../../lib/Revenue.php"			);
//	require_once( 	"../../lib/BusinessRevenueClass.php"			);
	require_once( 	"../../lib/System.php"			);


 	$System = new System;
	$Data = new Revenue;
	$districtid = $_GET['districtid'];
	$target = $_GET['target'];

	// was used to check an updated local plan and to see whether one upn was used at two or more locations
	// and to mark it with a * in the print
	$dupUPNinKML = $Data->checkOnDuplicateUPNInKML($districtid, $target);

// 	$dupUPNchecked = array_search('557-0688-0083',$dupUPNinKML);
// 	echo array_search('556-0692-0011',$dupUPNinKML, true);
// 	$dupUPNchecked = array_search('556-0688-0013',$dupUPNinKML);
// 	echo array_search('556-0688-0013',$dupUPNinKML, true);

//   var_dump($dupUPNchecked);
//   if ( $dupUPNchecked == FALSE ){
//   echo $dupUPNchecked.' $dupUPNchecked<br>';
//   }else
//   { echo 'not false Ekke'; }
//    var_dump($dupUPNinKML);
//    break;

	$PDF = new PDF('P','mm','A4');

	$year = $System->GetConfiguration("RevenueCollectionYear");
	$districtid = $_GET['districtid'];

	$districtName = $Data->getDistrictInfo( $districtid, "district_name" );



	//var_dump($_GET);


	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */

	$PDF->AddPage();

	$PDF->Ln();

	$PDF->SetFont('Arial','B',16);
	if ($target=='business'){
		$PDF->Cell(0,5,'Bills Register for Business Operating Permits',0,1,'C');		$PDF->Ln();
	}elseif ($target=='property'){
		$PDF->Cell(0,5,'Bills Register for Property Rates',0,1,'C');		$PDF->Ln();
	}
	$PDF->SetFont('Arial','',12);
	$PDF->Ln(10);

			// 1st row
			$PDF->SetFont('Arial','B',8);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(34,5, "District",1,0,'C',true);
			$PDF->SetFont('Arial','',8);
			$PDF->SetFillColor(255,255,255);
			$PDF->Cell(155,5, $districtName, 1,0,'C',true);
			$PDF->Ln();
			$PDF->SetFont('Arial','B',8);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(34,5, "Sub-District",1,0,'C',true);
			$PDF->SetFont('Arial','',8);
			$PDF->SetFillColor(255,255,255);
			$PDF->Cell(155,5, '', 1,0,'C',true);
			$PDF->Ln();

			// 2nd row
			$PDF->SetFont('Arial','B',7);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(16,5, "UPN", 1,0,'C',true);
			$PDF->Cell(18,5, "SUBUPN", 1,0,'C',true);
// 			$PDF->Cell(18,5, "Print Date", 1,0,'C',true);
			if ($target=='business'){
			$PDF->Cell(50,5, "BUSINESSNAME", 1,0,'C',true);
			$PDF->Cell(40,5, "OWNER", 1,0,'C',true);
			$PDF->Cell(30,5, "ADDRESS", 1,0,'C',true);
			$PDF->Cell(15,5, "PHONE", 1,0,'C',true);
			$PDF->Cell(20,5, "REMARKS", 1,0,'C',true);
			}elseif ($target=='property'){
			$PDF->Cell(45,5, "OWNER", 1,0,'C',true);
			$PDF->Cell(45,5, "ADDRESS", 1,0,'C',true);
			$PDF->Cell(10,5, "DUE", 1,0,'C',true);
			$PDF->Cell(15,5, "PHONE", 1,0,'C',true);
			$PDF->Cell(40,5, "REMARKS", 1,0,'C',true);
			}
			$PDF->Ln();

			// Data from Tables
			$n = 0;
			$PDF->SetFont('Arial','',6);
			$PDF->SetFillColor(255,255,255);
			if ($target=='business'){
				$q = mysql_query("SELECT business.id,
								business.`upn`,
								business.`subupn`,
								business.`streetname`,
								business.`housenumber`,
								business.`business_name`,
								business.`owner_tel`,
								business.`owner`,
								business.`colzone_id`,
								demand_notice_record.`upn`,
								demand_notice_record.`subupn`,
								demand_notice_record.`value`,
								demand_notice_record.`billprintdate`
							FROM business INNER JOIN demand_notice_record
								ON business.`upn` = demand_notice_record.`upn` AND business.`subupn` = demand_notice_record.`subupn`
							WHERE business.`districtid`='".$districtid."' AND demand_notice_record.`comments`='".$target."' AND demand_notice_record.`year`='".$year."'
							ORDER BY business.`colzone_id`, business.`streetname`, LENGTH(business.`housenumber`), business.`housenumber`, business.`upn`, business.`subupn`;");
			} elseif ($target=='property'){
				$q = mysql_query("SELECT property.id,
								property.`upn`,
								property.`subupn`,
								property.`streetname`,
								property.`housenumber`,
								property.`owner_tel`,
								property.`owner`,
								property.`colzone_id`,
								demand_notice_record.`upn`,
								demand_notice_record.`subupn`,
								demand_notice_record.`value`,
								demand_notice_record.`billprintdate`
							FROM property INNER JOIN demand_notice_record
								ON property.`upn` = demand_notice_record.`upn` AND property.`subupn` = demand_notice_record.`subupn`
							WHERE property.`districtid`='".$districtid."' AND demand_notice_record.`comments`='".$target."' AND demand_notice_record.`year`='".$year."'
							ORDER BY property.`colzone_id`, property.`streetname`, LENGTH(property.`housenumber`), property.`housenumber`, property.`upn`, property.`subupn`;");
			}

			$counter = 0;
			$cznr='';
			while( $r = mysql_fetch_array($q) )
			{
			// was used to check an updated local plan and to see whether one upn was used at two or more locations
			// and to mark it with a * in the print

				$dupUPNchecked = array_search($r['upn'],$dupUPNinKML);
				//	$dupUPNchecked = array_search('556-0686-0002',$dupUPNinKML);

				$address = $r['housenumber'].' '.$r['streetname'];
				if ($target=='business'){
// could not get the linebreak to work
					if (strlen(trim($businessname))>=50){
						$businessname = substr($r['business_name'],0,50).'...';
					} else {
						$businessname = $r['business_name'];
					}
 				}
 				if ($cznr!=$r['colzone_id']){
					$PDF->SetFont('Arial','',4);
					$PDF->Write(2,'* = Please check this address explicitly.');

					$PDF->AddPage();
					$PDF->SetFont('Arial','B',8);
					$PDF->SetFillColor(225,225,225);
					$PDF->Cell(34,5, "Collector Zone",1,0,'C',true);
// 					$PDF->SetFont('Arial','',8);
// 					$PDF->SetFillColor(255,255,255);
					if ($target=='business'){
					$PDF->Cell(50,5, $r['colzone_id'], 1,0,'C',true);
					$PDF->Cell(105,5, 'Bills Register for Business Operating Permits', 1,0,'C',true);
					}elseif ($target=='property'){
					$PDF->Cell(45,5, $r['colzone_id'], 1,0,'C',true);
					$PDF->Cell(110,5, 'Bills Register for Property Rates', 1,0,'C',true);
					}
					$PDF->Ln();
			// 2nd row
					$PDF->SetFont('Arial','B',7);
					$PDF->SetFillColor(225,225,225);
					$PDF->Cell(16,5, "UPN", 1,0,'C',true);
					$PDF->Cell(18,5, "SUBUPN", 1,0,'C',true);
		// 			$PDF->Cell(18,5, "Print Date", 1,0,'C',true);
					if ($target=='business'){
					$PDF->Cell(50,5, "BUSINESSNAME", 1,0,'C',true);
					$PDF->Cell(40,5, "OWNER", 1,0,'C',true);
					$PDF->Cell(30,5, "ADDRESS", 1,0,'C',true);
					$PDF->Cell(15,5, "PHONE", 1,0,'C',true);
					$PDF->Cell(20,5, "REMARKS", 1,0,'C',true);
					}elseif ($target=='property'){
					$PDF->Cell(45,5, "OWNER", 1,0,'C',true);
					$PDF->Cell(45,5, "ADDRESS", 1,0,'C',true);
					$PDF->Cell(10,5, "DUE", 1,0,'C',true);
					$PDF->Cell(15,5, "PHONE", 1,0,'C',true);
					$PDF->Cell(40,5, "REMARKS", 1,0,'C',true);
					}
					$PDF->Ln();

				}
				if ($target=='business'){
					$PDF->SetFont('Arial','',6);
					$PDF->SetFillColor(255,255,255);
					$PDF->Cell(16,5, $r['upn'],1,0,'C',true);
					$PDF->Cell(18,5, $r['subupn'],1,0,'C',true);
	// 				$PDF->Cell(18,5, $r['billprintdate'],1,0,'C',true);
					$PDF->Cell(50,5, $businessname,1,0,'C',true);
					$PDF->Cell(40,5, $r['owner'], 1,0,'C',true);
					$PDF->Cell(30,5, $address, 1,0,'C',true);
					$PDF->Cell(15,5, $r['owner_tel'], 1,0,'C',true);

						IF ($dupUPNchecked == FALSE) {
						$PDF->Cell(20,5, '', 1,0,'C',true);
						}else{
						$PDF->Cell(20,5, '*', 1,0,'L',true);
						}
					}elseif ($target=='property'){
					$PDF->SetFont('Arial','',6);
					$PDF->SetFillColor(255,255,255);
					$PDF->Cell(16,5, $r['upn'],1,0,'C',true);
					$PDF->Cell(18,5, $r['subupn'],1,0,'C',true);
					$PDF->Cell(45,5, $r['owner'], 1,0,'C',true);
					$PDF->Cell(45,5, $address, 1,0,'C',true);
					$PDF->Cell(10,5, number_format( $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtid, $year, $target, "due" ),2,'.',''), 1,0,'C',true);
					$PDF->Cell(15,5, $r['owner_tel'], 1,0,'C',true);
						IF ($dupUPNchecked == FALSE) {
						$PDF->Cell(40,5, '', 1,0,'C',true);
						}else{
						$PDF->Cell(40,5, '*', 1,0,'L',true);
						}
				}
				$PDF->Ln();
				$n = $n + 1;
				$cznr=$r['colzone_id'];
			}

			$PDF->Ln();
			$PDF->SetFont('Arial','',4);
			$PDF->Write(2,'* = Please check this address explicitly.');
			$PDF->Ln(10);


	$PDF->Ln();
	$PDF->Output();

?>