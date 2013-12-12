<?php
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
	//$districtId = $_GET['districtid'];
	$districtId = 130;
	
	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->Ln();
	
	$districtName = $Data->getDistrictInfo( $district, "district_name" );
	
	$q = mysql_query("SELECT * 	FROM  `property` WHERE 	`year` = '".$currentYear."' AND `districtid` = '".$districtId."' ORDER BY `id` ASC LIMIT 10 ");
	
	$counter = 0;
	while( $r = mysql_fetch_array($q) )
	{
		$PDF->Ln();
		// District emblem
		
		// District Left and Right side of bill
		$PDF->SetFont('Arial','B',16);
		$PDF->Cell(40,5, '',0,0,'C'); 			
		$PDF->Cell(70,5, 'Municipal Assembly',0,0,'R');
		$PDF->SetFont('Arial','B',10);
		$PDF->Cell(30,5, '',0,0,'C');		
		$PDF->Cell(40,5, 'Municipal Assembly',0,1,'R');	
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
		
		// Calculations
		$arreas = 	$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 1 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 2 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 3 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 4 ) +
					$Data->getAnnualBalance( $r['upn'], $r['subupn'], $currentYear - 5 );
		
		// Data
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Owner: ',0,0,'L');	
		$PDF->Cell(90,5, $Data->getOwnerInfo( $r['ownerid'], "name" ) ,1,1,'C');	
//		$PDF->Cell(90,5, $r['owner'],1,1,'C');	
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
		$PDF->Cell(30,5, 'Total Due: ',0,0,'R');
		$PDF->Cell(30,5, number_format( $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ) +
										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ) +
										$arreas + 
										$Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "feefi_value" ) ,2,'.','' ),1,1,'R');
		$PDF->SetFont('Arial','',8);
		$PDF->Cell(30,5, 'Usage: ',0,0,'L');
		$PDF->Cell(90,5, $r['property_use'],1,0,'C'); //property_use_title
		
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
		$PDF->Cell(23,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "prop_value" ),1,0,'R');
		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_value" ),1,0,'R');
		$PDF->Cell(17,5, $Data->getPropertyDueInfo( $r['upn'], $r['subupn'], $currentYear, "rate_impost_value" ),1,0,'R');
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