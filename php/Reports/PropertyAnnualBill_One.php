<?php
	if( session_status() != 2 )
	{
		session_start();
	}


	// Individual Property Annual Bill
	// Printed and issued to property owner

	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);
	require_once(	"../../lib/System.PDF.AnnualBill.php"		);
	require_once( 	"../../lib/System.php"			);
	require_once( 	"../../lib/Revenue.php"			);

	$Data = new Revenue;
 	$System = new System;

	$PDF = new PDF('P','mm','A4');

 	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
// 	$currentYear = '2015';

	$districtId 	= $_GET['districtid'];
	$upn 			= $_GET["upn"];
	$subupn 		= $_GET["subupn"];
	$type			= $_GET["type"];
// var_dump($_GET);
// 												****FOR TESTING****
//	$districtId 	= '130';
//	$upn 			= '610-0615-0802';
//	$subupn 		= '610-0615-0802A';


	//get the districts logo
	if (file_exists('../../uploads/logo-'.$districtId.'.gif'))
		{
			$file= '../../uploads/logo-'.$districtId.'.gif';
		}
	elseif (file_exists('../../uploads/logo-'.$districtId.'.jpg'))
		{
			$file= '../../uploads/logo-'.$districtId.'.jpg';
		}
	else
	{
		$file='../../uploads/logo-0.JPG';
	}

	$ext = pathinfo($file, PATHINFO_EXTENSION);

	//get the districts signature
	if (file_exists('../../uploads/sig-'.$districtId.'.jpg'))
	{
		$filesig= '../../uploads/sig-'.$districtId.'.jpg';
	}
	else
	{
		$filesig='';
	}

	$extsig = pathinfo($filesig, PATHINFO_EXTENSION);

	$note = 'Kindly pay the amount involved to the District Finance Officer or to any Revenue Collector appointed ';
	$note1 = 'by the Assembly ON OR BEFORE March 31, '.$currentYear.'.';
	$note2 =  'Should you fail to do so, proceedings will be taken for the purpose of exacting Sale or Entry into possession such Rate and the expenses incurred.';


	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */

	$PDF->AddPage();

	$PDF->Ln();
	$districtName = $Data->getDistrictInfo( $districtId, "district_name" );
	$districtType = $Data->getDistrictInfo( $districtId, "coa-disttypeid" );

// 	$q = mysql_query("SELECT * 	FROM  	`property`
// 								WHERE 	`upn` = '".$upn."' AND
// 										`subupn` = '".$subupn."' AND
// 										`districtid` = '".$districtId."' ");

	$q = mysql_query("SELECT t1.*, t2.`colzonenr` FROM  `property` t1, `collectorzones` t2
						WHERE t1.`upn` = '".$upn."' AND
							  	t1.`subupn` = '".$subupn."' AND
								t1.`districtid` = '".$districtId."'
								AND t2.`id`= t1.`colzone_id` ORDER BY t2.`colzonenr` ");
	$counter = 0;
	while( $r = mysql_fetch_array($q) )
	{
		$PDF->Ln();
		// District emblem
//		$PDF->Cell(30,5, $PDF->Image($file, null, null, 15, 15, 'gif', ''),0,0,'C');
		if (!empty($file))
		{
			$PDF->Image($file, $PDF->GetX()+5, $PDF->GetY()-4, 20, 20, $ext, '');
		}
		if (!empty($filesig))
		{
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
		if( $districtType == '1' )
		{
			$PDF->Cell(40,5, 'District Assembly',0,1,'R');
		}
		elseif( $districtType == '2' )
		{
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
		$PDF->Cell(70,5,'Bill Date: '.date('d-m-Y').' / Collector Zone:'.$r['colzonenr'].' / Copy',0,0,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, '',0,0,'C');
		$PDF->Cell(40,5,'Bill Date: '.date('d-m-Y'),0,1,'R');

		$PDF->Ln();

		// Arreas - the last years balance holds all the previous years arreas
		$arreas = $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtId, $currentYear-1, $type, "balance" );

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
		// Obsolete - 15.07.2014 - Arben
		//$dueAll=$Data->getPropertyDueInfoAll( $r['upn'], $r['subupn'], $currentYear);
		$duePropertyValue 	= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "prop_value" );
		$dueRateValue 		= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "rate_value" );
		$dueRateImpostValue = $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "rate_impost_value" );
		$dueFeeFixValue 	= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "feefi_value" );
		$paidCurrentYear	= $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "paid" );

//!!! needs to go away quickly !!! Was asked by Prestea and will only work there
        if ($districtId==130){
        	if ($r['rooms']>0){
        		$dueFeeFixValue 	= $dueFeeFixValue * $r['rooms'];
        	}
        }

//		$value = $dueRateValue + $dueRateImpostValue + $arreas + $dueFeeFixValue;
		if ($duePropertyValue > 0) {
			$value = $dueRateValue + $arreas;}
			else{
			$value = $dueRateValue + $arreas + $dueFeeFixValue;}

		// Obsolete - 15.07.2014 - Arben
		//$PDF->Cell(30,5, number_format( $dueAll["rate_value"] +
		//								$dueAll["rate_impost_value"] +
		//								$arreas +
		//								$dueAll["feefi_value" ] ,2,'.','' ),1,1,'R');
		$PDF->Cell(30,5, number_format( $value-$paidCurrentYear ,2,'.','' ),1,1,'R');


		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Usage: ',0,0,'L');
		// name of the property_use
		// OBSOLETE - 15.07.2014 - Arben
		//$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingClassInfo( $districtId, $r['property_use']),1,0,'C');
		$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingInfo( $districtId, $r['property_use'], $currentYear, $type, "class" ),1,0,'C');

		$PDF->Ln(10);

		$PDF->SetFont('Arial','B',6);
		$PDF->Cell(16,5, 'Current Year',1,0,'C');
		$PDF->Cell(18,5, 'Property Value',1,0,'C');
		$PDF->Cell(16,5, 'Rate Charged',1,0,'C');
		$PDF->Cell(16,5, 'Rate Impost',1,0,'C');
		$PDF->Cell(12,5, 'Arrears',1,0,'C');
		$PDF->Cell(16,5, 'Adjustments',1,0,'C');
		$PDF->Cell(14,5, 'Payments',1,0,'C');
		$PDF->Cell(12,5, 'Total Due',1,1,'C');

		$PDF->SetFont('Arial','',7);
		$PDF->Cell(16,5, $currentYear,1,0,'R');
		$PDF->Cell(18,5, $duePropertyValue,1,0,'R'); //Property Value
		$PDF->Cell(16,5, $dueRateValue,1,0,'R'); // Rate Charged
		$PDF->Cell(16,5, $dueRateImpostValue,1,0,'R'); //Rate Impost

// 		$PDF->Cell(23,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "prop_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ),1,0,'R');
		$PDF->Cell(12,5, number_format( $arreas,2,'.','' ),1,0,'R'); // Arrears
		if ($duePropertyValue==0) {
		$PDF->Cell(16,5, number_format( $dueFeeFixValue,2,'.','' ) ,1,0,'R'); //Adjustment
		}else{
			$PDF->Cell(16,5, number_format( '0',2,'.','' ) ,1,0,'R');
		}

		$PDF->Cell(14,5, number_format( $paidCurrentYear,2,'.','') ,1,0,'R');
		$PDF->Cell(12,5, number_format( $value-$paidCurrentYear,2,'.','' ) ,1,1,'R');

		$PDF->SetFont('Arial','B',6);
		$PDF->Cell(16,5, 'Previous Year',1,0,'C');
		$PDF->Cell(18,5, 'Total Due',1,0,'C');
		$PDF->Cell(16,5, 'Total Paid',1,0,'C');

		$PDF->SetFont('Arial','B',7);
		$PDF->Cell(80,5, '',0,0,'R');
		$PDF->Cell(80,5, 'Please present this bill when making a payment',0,1,'L');

		$PDF->SetFont('Arial','',7);
		$PDF->Cell(16,5, $currentYear - 1,1,0,'R');
		$PDF->Cell(18,5, number_format( $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtId, $currentYear-1, $type, "due" ),2,'.','') ,1,0,'R');
		$PDF->Cell(16,5, number_format( $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtId, $currentYear-1, $type, "paid" ),2,'.','') ,1,0,'R');

		$PDF->SetFont('Arial','',4);
		$PDF->Write(2,$note.chr(10));
		$PDF->Write(2,str_repeat(" ", 175).$note1);

		$counter++;

		$PDF->Ln(15);
	}

	$PDF->Ln();
	$PDF->Output();

?>