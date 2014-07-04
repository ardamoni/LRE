<?php
if( session_status() != 2 )
	{
		session_start();
	}


	// All Properties Annual Bill
	// Printed  and issued to property owners at the beggining of the year

	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);	
	require_once(	"../../lib/System.PDF.AnnualBill.php"		);
	require_once( 	"../../lib/Revenue.php"			);
	require_once( 	"../../lib/System.php"			);
	
	$Data = new Revenue;
	$System = new System;
	
	$PDF = new PDF('P','mm','A4');
	
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
	
	// TODO: Session on District
	$districtId = $_GET['districtid'];
	//get the districts logo
	if (file_exists('../../uploads/logo-'.$districtId.'.gif')) {
		$file= '../../uploads/logo-'.$districtId.'.gif';
	} else {
		$file='../../uploads/logo-0.JPG';}
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	//get the districts signature
	if (file_exists('../../uploads/sig-'.$districtId.'.jpg')) {
		$filesig= '../../uploads/sig-'.$districtId.'.jpg';
	} else {
//  $filesig='../../uploads/sig-125.jpg';}
		$filesig='';}
	$extsig = pathinfo($filesig, PATHINFO_EXTENSION);
	$note = 'Kindly pay the amount involved to the District Finance Officer or to any Revenue Collector appointed by the Assembly ON OR BEFORE March 31, '.$currentYear.'.';
	$note2 =  'Should you fail to do so, proceedings will be taken for the purpose of exacting Sale or Entry into possession such Rate and the expenses incurred.';
// 	$note = 'This bill must be paid by March 31st, in accordance with the districts regulations. Legal Actions shall be taken against defaulters 52 days after March 31st.';
// 	$note2 =  "Payments should be made by banker's Draft/Payment Order or by Cash Only.";
// 	$note3 =  "It is an offence to deface the property number, and change ownership without informing the district Assembly";
		
//	var_dump($qdistrictname);
	
//	$districtId = 130;
	
	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->Ln();
	
// 	$districtName = $Data->getDistrictInfo( $district, "district_name" );
	
	$districtName = $Data->getDistrictInfo( $districtId, "district_name" );
	$districtType = $Data->getDistrictInfo( $districtId, "coa-disttypeid" );
		
// 	$q = mysql_query("SELECT * 	FROM  `property` WHERE 	`districtid` = '".$districtId."' ORDER BY `upn` ASC LIMIT 10 "); //`year` = '".$currentYear."' AND 
// $q = mysql_query("SELECT * 	FROM  `property` WHERE 	`districtid` = '".$districtId."' ORDER BY `upn` ASC ");
$q = mysql_query("SELECT t1.*, t2.`colzonenr` FROM  `property` t1, `collectorzones` t2 WHERE t1.`districtid` = '".$districtId."' AND t2.`id`= t1.`colzone_id` ORDER BY t2.`colzonenr` ");
	
	$counter = 0;
	while( $r = mysql_fetch_array($q) )
	{
		$PDF->Ln();
		// District emblem
//		$PDF->Cell(30,5, $PDF->Image($file, null, null, 15, 15, 'gif', ''),0,0,'C'); 			
		if (!empty($file)){
		$PDF->Image($file, $PDF->GetX()+5, $PDF->GetY()-4, 20, 20, $ext, '');
		}
		if (!empty($filesig)){
		$PDF->Image($filesig, $PDF->GetX()+27, $PDF->GetY()+2, 25, 0, $extsig, '');
		}
		// District Left and Right side of bill
		$PDF->SetFont('Arial','B',16);
		$PDF->Cell(40,5, '',0,0,'C'); 			
		$PDF->Cell(70,5, $districtName,0,0,'R');
		$PDF->SetFont('Arial','B',10);
		$PDF->Cell(30,5, '',0,0,'C');		
		$PDF->Cell(40,5, $districtName,0,1,'R');	
		$PDF->SetFont('Arial','B',16);

		$PDF->Cell(40,5, '',0,0,'C'); 			
		if ($districtType=='1'){
		$PDF->Cell(70,5, 'District Assembly',0,0,'R');
		}elseif ($districtType=='2'){
		$PDF->Cell(70,5, 'Municipal Assembly',0,0,'R');
		}
		$PDF->SetFont('Arial','B',10);
		$PDF->Cell(30,5, '',0,0,'C');		
		if ($districtType=='1'){
		$PDF->Cell(40,5, 'District Assembly',0,1,'R');
		}elseif ($districtType=='2'){
		$PDF->Cell(40,5, 'Municipal Assembly',0,1,'R');
		}	
//		$PDF->Cell(40,5, 'Municipal Assembly',0,1,'R');	
		$PDF->SetFont('Arial','I',12);
		$PDF->Cell(40,5, '',0,0,'C');	
		$PDF->Cell(70,5,'Property Rate Bill',0,0,'R'); 	
		$PDF->SetFont('Arial','I',8);
		$PDF->Cell(30,5, '',0,0,'C');	
		$PDF->Cell(40,5,'Property Rate Bill',0,1,'R'); 	
		$PDF->SetFont('Arial','',10);
		$PDF->Cell(40,5, '',0,0,'C');	
		$PDF->Cell(70,5,'Bill Year: '.$currentYear.' / Collector Zone:'.$r['colzonenr'],0,0,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, '',0,0,'C');	
		$PDF->Cell(40,5,'Bill Year: '.$currentYear,0,1,'R');	
		
		$PDF->Ln();
		
		// Calculations
		$arreas = 	$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 1 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 2 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 3 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 4 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 5 );
		
		// Data
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Owner: ',0,0,'L');	
//		$PDF->Cell(90,5, $Data->getOwnerInfo( $r['ownerid'], "name" ) ,1,1,'C');	
		$PDF->Cell(90,5, $r['owner'],1,1,'C');	
		$PDF->Cell(30,5, 'UPN: ',0,0,'L');
		$PDF->Cell(90,5, $r['upn'],1,0,'C');
		$PDF->SetFont('Arial','',6);
		$PDF->Cell(30,5, 'UPN: ',0,0,'R');
		$PDF->Cell(30,5, $r['upn'],1,1,'C');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'SUBUPN: ',0,0,'L');
		$PDF->Cell(90,5, $r['subupn'],1,0,'C');
		$PDF->SetFont('Arial','',6);
		$PDF->Cell(30,5, 'SUBUPN: ',0,0,'R');
		$PDF->Cell(30,5, $r['subupn'],1,1,'C');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Address: ',0,0,'L');
		$PDF->Cell(90,5, $r['housenumber']." ".$r['streetname'],1,0,'C');
		$PDF->SetFont('Arial','',6);
		$PDF->Cell(30,5, 'Bill Year: ',0,0,'R');
		$PDF->Cell(30,5, $currentYear,1,1,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Area Zone: ',0,0,'L');
		$PDF->Cell(90,5, $r['zoneid'],1,0,'C');
		$PDF->SetFont('Arial','',6);
		$PDF->Cell(30,5, 'Total Amount Due: ',0,0,'R');
		//Calc the total Due
		$dueAll=$Data->getPropertyDueInfoAll( $r['upn'], $r['subupn'], $currentYear);
		$PDF->Cell(30,5, number_format( $dueAll["rate_value"] +
										$dueAll["rate_impost_value"] +
										$arreas + 
										$dueAll["feefi_value" ] ,2,'.','' ),1,1,'R');
// 		$PDF->Cell(30,5, number_format( $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ) +
// 										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ) +
// 										$arreas + 
// 										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "feefi_value" ) ,2,'.','' ),1,1,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Usage: ',0,0,'L');
		$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingClassInfo( $districtId, $r['property_use']),1,0,'C'); //property_use_title
		
		$PDF->Ln(9);
		
		$PDF->SetFont('Arial','B',6);
		$PDF->Cell(16,5, 'Current Year',1,0,'C');	
		$PDF->Cell(23,5, 'Base Property Value',1,0,'C');
		$PDF->Cell(17,5, 'Rate Charged',1,0,'C');
		$PDF->Cell(17,5, 'Rate Impost',1,0,'C');
		$PDF->Cell(12,5, 'Arreas',1,0,'C');
		$PDF->Cell(16,5, 'Adjustments',1,0,'C');
		$PDF->Cell(19,5, 'Total Due',1,1,'C');
		
		$PDF->SetFont('Arial','',7);
		$PDF->Cell(16,5, $currentYear,1,0,'R');	
		$PDF->Cell(23,5, $dueAll["prop_value"],1,0,'R');
		$PDF->Cell(17,5, $dueAll["rate_value"],1,0,'R');
		$PDF->Cell(17,5, $dueAll["rate_impost_value"],1,0,'R');
// 		$PDF->Cell(23,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "prop_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ),1,0,'R');
		$PDF->Cell(12,5, number_format( $arreas ,2,'.','' ),1,0,'R');
		$PDF->Cell(16,5, number_format( $dueAll["feefi_value"],2,'.','' ) ,1,0,'R');	
		$PDF->Cell(19,5, number_format( $dueAll["rate_value"] +
										$dueAll["rate_impost_value"] +
										$arreas + 
										$dueAll["feefi_value"] ,2,'.','' ) ,1,1,'R');
		
		$PDF->SetFont('Arial','B',6);
		$PDF->Cell(16,5, 'Previous Year',1,0,'C');	
		$PDF->Cell(23,5, 'Total Owed',1,0,'C');
		$PDF->Cell(17,5, 'Total Payed',1,0,'C');

		$PDF->SetFont('Arial','B',7);
		$PDF->Cell(70,5, '',0,0,'R');
		$PDF->Cell(65,5, 'Please present this bill when making a payment',0,1,'L');
		
		$PDF->SetFont('Arial','',7);
		$PDF->Cell(16,5, $currentYear - 1,1,0,'R');	
		$PDF->Cell(23,5, number_format( $Data->getAnnualDueSum( $upn, $subupn, $currentYear - 1 ) ,2,'.','') ,1,0,'R');
		$PDF->Cell(17,5, number_format( $Data->getAnnualBalance( $upn, $subupn, $currentYear - 1 ) ,2,'.','') ,1,1,'R');
		
		if ($counter==2) {
//		$PDF->Ln(10);
		$PDF->AddPage();
		$counter=0;
		} else {
		$counter++;
		$PDF->Cell(0,5, str_repeat("-", 200),0,0,'C');
		$PDF->Ln();
		}
// 		$counter++;
// 		$PDF->Ln(15);

	$Data->setDemandNoticeRecord( $r['districtid'], $r['upn'], $r['subupn'], $currentYear, $value, 'property' );
		
	} //fetch_array
	
	$PDF->Ln();
	$PDF->Output();

?>