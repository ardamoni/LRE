<?php
	// UPN or SUBUPN History
	// if SUBUPN exists it is usually more than one and therefore get's in as Array

	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);	
	require_once(	"../../lib/System.PDF.php"		);
	require_once( 	"../../lib/Revenue.php"			);
	require_once( 	"../../lib/System.php"			);
	
	$Data = new Revenue;
	$System = new System;
	
	$PDF = new PDF('P','mm','A4');
	
	$upn = $_GET['upn'];
	$subupn = $_GET['subupn'];	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	
	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->Ln();
	
	$PDF->SetFont('Arial','B',16);
	$PDF->Cell(0,5,'UPN History',0,1,'C');		$PDF->Ln();
	$PDF->SetFont('Arial','',12);
	$PDF->Cell(0,5,'(Last 5 years)',0,1,'C'); 	$PDF->Ln(10);	
	
	if( count($subupn) > 0 ) 	// with SUB-UPN's
	{
		$subupn = explode(',', $subupn);
		$arrlength = count($subupn);
		for( $x = 0; $x < $arrlength; $x++ )
		{			
			// 1st row
			$PDF->SetFont('Arial','B',8);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(20,5, "UPN",1,0,'C',true);
			$PDF->SetFont('Arial','',8); 
			$PDF->SetFillColor(255,255,255);
			$PDF->Cell(155,5, $upn, 1,0,'C',true);
			$PDF->Ln();
			$PDF->SetFont('Arial','B',8);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(20,5, "SUBUPN",1,0,'C',true);
			$PDF->SetFont('Arial','',8); 
			$PDF->SetFillColor(255,255,255);
			$PDF->Cell(155,5, $subupn[$x], 1,0,'C',true);
			$PDF->Ln();

			// 2nd row
			$PDF->SetFont('Arial','B',8);
			$PDF->SetFillColor(225,225,225);
			$PDF->Cell(20,5, "YEAR", 1,0,'C',true);
			$PDF->Cell(30,5, "OWED", 1,0,'C',true);
			$PDF->Cell(30,5, "PAID", 1,0,'C',true);
			$PDF->Cell(30,5, "BALANCE", 1,0,'C',true);
			$PDF->Cell(65,5, "OWNER", 1,0,'C',true);
			$PDF->Ln();
			
			// Data from Tables
			$n = 0;
			$PDF->SetFont('Arial','',8);
			$PDF->SetFillColor(255,255,255);
			while( $n <= 5 )
			{
				$ownerid = $Data->getPropertyInfo( $upn, $subupn[$x], $year - $n, "ownerid" );
				$owner = $Data->getOwnerInfo( $ownerid, 'name' );
				
				$PDF->Cell(20,5, $year - $n,1,0,'C',true);
				$PDF->Cell(30,5, number_format( $Data->getAnnualDueSum( $upn, $subupn[$x], $year - $n ),2,'.','' ),1,0,'R',true);	
				$PDF->Cell(30,5, number_format( $Data->getAnnualPaymentSum( $upn, $subupn[$x], $year - $n ),2,'.','' ),1,0,'R',true);	
				$PDF->Cell(30,5, number_format( $Data->getAnnualBalance( $upn, $subupn[$x], $year - $n ),2,'.','' ),1,0,'R',true);			
				$PDF->Cell(65,5, "  ".$owner, 1,0,'L',true);
				$PDF->Ln();
				$n = $n + 1;
			}
						
			$PDF->Ln(10);			
			
		} // end of looping through sub-upn's		
	}
	else	// CASE WITH NO SUB-UPN
	{
		// clear subupn
		//$subupn = '';
		// 1st row
		$PDF->SetFont('Arial','B',8);
		$PDF->SetFillColor(225,225,225);
		$PDF->Cell(20,5, "UPN",1,0,'C',true);
		$PDF->SetFont('Arial','',8);
		$PDF->SetFillColor(255,255,255);
		$PDF->Cell(155,5, $upn, 1,0,'C',true);
		$PDF->Ln();
		$PDF->SetFont('Arial','B',8);
		$PDF->SetFillColor(225,225,225);
		$PDF->Cell(20,5, "SUBUPN",1,0,'C',true);
		$PDF->SetFont('Arial','',8); 
		$PDF->SetFillColor(255,255,255);
		$PDF->Cell(155,5, $subupn, 1,0,'C',true);
		$PDF->Ln();

		// 2nd row
		$PDF->SetFont('Arial','B',8);
		$PDF->SetFillColor(225,225,225);
		$PDF->Cell(20,5, "YEAR", 1,0,'C',true);
		$PDF->Cell(30,5, "OWED", 1,0,'C',true);
		$PDF->Cell(30,5, "PAID", 1,0,'C',true);
		$PDF->Cell(30,5, "BALANCE", 1,0,'C',true);
		$PDF->Cell(65,5, "OWNER", 1,0,'C',true);
		$PDF->Ln();
		
		// Data from Tables
		$n = 0;
		$PDF->SetFont('Arial','',8);
		$PDF->SetFillColor(255,255,255);
		while( $n <= 5 )
		{
			$ownerid = $Data->getPropertyInfo( $upn, $subupn, $year - $n, "ownerid" );
			$owner = $Data->getOwnerInfo( $ownerid, 'name' );
			
			$PDF->Cell(20,5, $year - $n,1,0,'C',true);
			$PDF->Cell(30,5, number_format( $Data->getAnnualDueSum( $upn, $subupn, $year - $n ),2,'.','' ),1,0,'R',true);	
			$PDF->Cell(30,5, number_format( $Data->getAnnualPaymentSum( $upn, $subupn, $year - $n ),2,'.','' ),1,0,'R',true);	
			$PDF->Cell(30,5, number_format( $Data->getAnnualBalance( $upn, $subupn, $year - $n ),2,'.','' ),1,0,'R',true);			
			$PDF->Cell(65,5, "  ".$owner, 1,0,'L',true);
			$PDF->Ln();
			$n = $n + 1;
		}	
	}
	
	$PDF->Ln();
	$PDF->Output();

?>