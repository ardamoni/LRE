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
	require_once( 	"../../lib/Revenue.php"			);
	require_once( 	"../../lib/System.php"			);
	
	$Data = new Revenue;
	$System = new System;
	
	$PDF = new PDF('P','mm','A4');
	
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
	
	$districtId 	= $_GET['districtid'];
	$upn 			= $_GET["upn"];	
	$subupn 		= $_GET["subupn"];	
	
// 												****FOR TESTING****
//	$districtId 	= '130';
//	$upn 			= '610-0615-0802';	
//	$subupn 		= '610-0615-0802A';	
	
	
	//get the districts logo
	if (
		file_exists('../../uploads/logo-'.$districtId.'.gif')) 
		{
			$file= '../../uploads/logo-'.$districtId.'.gif';
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
	


	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->Ln();
	$districtName = $Data->getDistrictInfo( $districtId, "district_name" );
	$districtType = $Data->getDistrictInfo( $districtId, "coa-disttypeid" );
		
	$q = mysql_query("SELECT * 	FROM  	`property` 
								WHERE 	`upn` = '".$upn."' AND 
										`subupn` = '".$subupn."' AND 
										`districtid` = '".$districtId."' ");
	
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
		$PDF->Cell(70,5,'Bill Year: '.$currentYear,0,0,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, '',0,0,'C');	
		$PDF->Cell(40,5,'Bill Year: '.$currentYear,0,1,'R');	
		
		$PDF->Ln();
		
		// Arreas - the last years balance holds all the previous years arreas
		$arreas = 	$Data->getEndBalance( $r['upn'], $r['subupn'], $districtid, $currentYear-1 );
		
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
		// name of the property_use
		$PDF->Cell(90,5,  $r['property_use'].' / '.$Data->getFeeFixingClassInfo( $districtId, $r['property_use']),1,0,'C');  
		
		$PDF->Ln(10);
		
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
		$PDF->Cell(16,5, number_format( $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "feefi_value" ),2,'.','' ) ,1,0,'R');	
		$PDF->Cell(19,5, number_format( $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ) +
										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ) +
										$arreas + 
										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "feefi_value" ) ,2,'.','' ) ,1,1,'R');
		
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
		$PDF->Cell(17,5, number_format( $Data->getAnnualBalance( $upn, $subupn, $currentYear - 1 ) ,2,'.','') ,1,0,'R');
		
		$counter++;

		$PDF->Ln(15);
	}
	
	$PDF->Ln();
	$PDF->Output();

?>