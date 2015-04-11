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
// $pdf = new FPDF2File('P','mm','A4');
// $pdf->Open('doc.pdf');
 	$currentYear 	= $System->GetConfiguration("RevenueCollectionYear");
// 	$currentYear 	= '2014';
	$districtId 	= $_GET['districtid'];
	$type 			= "property";

	set_time_limit(0);

	//get the districts logo
	if (file_exists('../../uploads/logo-'.$districtId.'.gif')) {
		$file= '../../uploads/logo-'.$districtId.'.gif';
	}
	elseif (file_exists('../../uploads/logo-'.$districtId.'.jpg'))
	{
		$file= '../../uploads/logo-'.$districtId.'.jpg';
	}
	else {
		$file='../../uploads/logo-0.JPG';}
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	//get the districts signature
	if (file_exists('../../uploads/sig-'.$districtId.'.jpg')) {
		$filesig= '../../uploads/sig-'.$districtId.'.jpg';
	} else {
//  $filesig='../../uploads/sig-125.jpg';}
		$filesig='';}
	$extsig = pathinfo($filesig, PATHINFO_EXTENSION);
	$note = 'Kindly pay the amount involved to the District Finance Officer or to any Revenue Collector appointed ';
	$note1 = 'by the Assembly ON OR BEFORE March 31, '.$currentYear.'.';
	$note2 =  'Should you fail to do so, proceedings will be taken for the purpose of exacting Sale or Entry into possession such Rate and the expenses incurred.';
// 	$note = 'This bill must be paid by March 31st, in accordance with the districts regulations. Legal Actions shall be taken against defaulters 52 days after March 31st.';
// 	$note2 =  "Payments should be made by banker's Draft/Payment Order or by Cash Only.";
// 	$note3 =  "It is an offence to deface the property number, and change ownership without informing the district Assembly";

//	var_dump($qdistrictname);

//	$districtId = 130;



// 	$districtName = $Data->getDistrictInfo( $district, "district_name" );

	$districtName = $Data->getDistrictInfo( $districtId, "district_name" );
	$districtType = $Data->getDistrictInfo( $districtId, "coa-disttypeid" );

$q = mysql_query("SELECT t1.*, t1.`colzone_id` FROM  `property` t1 WHERE t1.`districtid` = '".$districtId."'  ORDER BY t1.`colzone_id`, t1.`streetname`, LENGTH(t1.`housenumber`), t1.`housenumber`, t1.`upn`");
//$q = mysql_query("SELECT t1.*, t1.`colzone_id` FROM  `property` t1 WHERE t1.`districtid` = '".$districtId."'  ORDER BY t1.`colzone_id`, t1.`streetname`, t1.`housenumber`, t1.`upn` LIMIT 10");

	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	$PDF->AddPage();

	$PDF->Ln();
	$counter = 0;
	while( $r = mysql_fetch_array($q) )
	{
		if (substr_count($r['upn'],'-')==2)
		{
		$PDF->Ln();
		// District emblem
// 		$PDF->Cell(30,5, $PDF->Image($file, null, null, 15, 15, 'gif', ''),0,0,'C');
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
		$PDF->Cell(70,5,'Bill Date: '.date('d-m-Y').' / Collector Zone:'.$r['colzone_id'],0,0,'R');
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

		//Calc the total Due
		// Obsolete - 15.07.2014 - Arben
		//$dueAll=$Data->getPropertyDueInfoAll( $r['upn'], $r['subupn'], $currentYear);
		$duePropertyValue 	= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "prop_value" );
		$dueRateValue 		= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "rate_value" );
		$dueRateImpostValue = $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "rate_impost_value" );
		$dueFeeFixValue 	= $Data->getDueInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "feefi_value" );
		$paidCurrentYear	= $Data->getBalanceInfo( $r['upn'], $r['subupn'], $districtId, $currentYear, $type, "paid" );

//!!! needs to go away quickly !!! Was asked by Prestea and will only work there
        if ($districtId=='130'){
        	if ($r['rooms']>0){
        $dueFeeFixValue 	= $dueFeeFixValue * $r['rooms'];
        }}

		if ($duePropertyValue > 0) {
			$value = $dueRateValue + $arreas;
			$adjustment = $dueRateValue;}
			else{
			$value = $dueRateValue + $arreas + $dueFeeFixValue;
						$adjustment = $dueFeeFixValue;}



		//Obsolete - 15.07.2014 - Arben
		/*$PDF->Cell(30,5, number_format( $dueAll["rate_value"] +
										$dueAll["rate_impost_value"] +
										$arreas +
										$dueAll["feefi_value" ] ,2,'.','' ),1,1,'R');
		*/
		$PDF->Cell(30,5, number_format( $value-$paidCurrentYear ,2,'.','' ),1,1,'R');

		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Usage: ',0,0,'L');
		// OBSOLETE - 15.07.2014 - Arben
		//$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingClassInfo( $districtId, $r['property_use']),1,0,'C'); //property_use_title
		$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingInfo( $districtId, $r['property_use'], $currentYear, $type, "class" ),1,0,'C');

		$PDF->Ln(9);

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
		$PDF->Cell(18,5, $duePropertyValue,1,0,'R');
		$PDF->Cell(16,5, $dueRateValue,1,0,'R');
		$PDF->Cell(16,5, $dueRateImpostValue,1,0,'R');

// 		$PDF->Cell(23,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "prop_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ),1,0,'R');
// 		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ),1,0,'R');
		$PDF->Cell(12,5, number_format( $arreas,2,'.','' ),1,0,'R');
		if ($duePropertyValue==0) {
		$PDF->Cell(16,5, number_format( $dueFeeFixValue,2,'.','' ) ,1,0,'R');
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

		if ($counter==2) {
	//		$PDF->Ln(10);
			$PDF->AddPage();
			$counter=0;
		} else {
			$counter++;
			$PDF->Ln();
	$PDF->Cell(0,5, str_repeat("-", 200),0,0,'C');
			$PDF->Ln();
		}
// 		$counter++;
// 		$PDF->Ln(15);

		// Obsolete - 15.07.2014 Arben
		//$Data->setDemandNoticeRecord( $r['districtid'], $r['upn'], $r['subupn'], $currentYear, $value, 'property' );
		$Data->setDemandNoticeRecord( $r['upn'], $r['subupn'], $r['districtid'], $currentYear, $type, $value );

	} //end if substr_count
	} //fetch_array

	$PDF->Ln();
// $pdf->Output();
// 	$savefile = "../tmp/output.pdf";
// $pdf->Output($savefile,'I');
	$PDF->Output('PropertyAnnualBill.pdf','I');

?>