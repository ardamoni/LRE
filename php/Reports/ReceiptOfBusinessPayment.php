<?php
	// libraries
	require_once(	"../../lib/configuration.php"	);	
	require_once(	"../../lib/System.PDF.php"		);
	require_once( 	"../../lib/Revenue.php"			);
	require_once( 	"../../lib/System.php" 			);
	
	$Data = new Revenue;
	$System = new System;
	
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
	$previousYear = $currentYear - 1;
	
	
	$upn = $_GET['upn'];
	$subupn = $_GET['subupn'];
	$districtid = $_GET['districtid'];
	
	$districtName = $Data->getDistrictInfo( $districtid, "district_name" );
	$type = "business";

	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	
	
	$PDF = new PDF('P','mm','A4');
	
	$PDF->AddPage();
	
	$PDF->SetFont('Arial','B',16);
	$PDF->Cell(195,5,"Payment Balance",0,0,'C');
	$PDF->Ln();	
	$PDF->SetFont('Arial','B',14);
	$PDF->Cell(195,10,"(all the payments for the current year)",0,0,'C');
	$PDF->Ln(20);
	
	// 1st row
	$PDF->SetFont('Arial','B',10);
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, "UPN",1,0,'C',true);
	$PDF->Cell(45,5, "SUBUPN",1,0,'C',true);
	$PDF->Cell(45,5, "TICKET RECEIPT",1,0,'C',true);
	$PDF->Cell(45,5, "ELECTRONIC RECEIPT",1,0,'C',true);
	$PDF->Ln();
	
	$lastPaidValue 		= $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $currentYear, $type, "payment_value" );
	$lastTicketReceipt 	= $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $currentYear, $type, "receipt_payment" );
	$lastSystemReceipt 	= $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $currentYear, $type, "id" );
	
	$PDF->SetFont('Arial','',10);	
	$PDF->SetFillColor(255,255,255);		
	$PDF->Cell(45,5, $upn, 1,0,'C',true);
	if( $subupn == "" ) 
		$subupnDisp = ' - ';
	else
		$subupnDisp = $subupn;
	$PDF->Cell(45,5, $subupnDisp, 1,0,'C',true);
	$PDF->Cell(45,5, $lastTicketReceipt, 1,0,'C',true);
	$PDF->Cell(45,5, $lastSystemReceipt, 1,0,'C',true);
	$PDF->Ln(10);				
	
	$streetname 	= $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "streetname" );
	$housenumber 	= $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "housenumber" );
	$owner 			= $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "owner" );
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, "DISTRICT", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $districtName, 1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, "ADDRESS", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $streetname." ".$housenumber ,1,0,'C',true); $PDF->Ln();
		
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, "OWNER", 1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $owner, 1,0,'C',true); $PDF->Ln();
	
	// previous year balance
	$revenueBalancePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "balance" );
	if( !$revenueBalancePrevious ) $revenueBalancePrevious = 0;
	// current year balance
	$revenueDue 			= $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "due" );
	$revenueBalance 		= $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "balance" );
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, $previousYear." Balance *", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $revenueBalancePrevious, 1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, $currentYear." Due *", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $revenueDue, 1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->SetFont('Arial','B',10);
	$PDF->Cell(45,5, "Order of Payments", 1,0,'C',true);
	$PDF->Cell(45,5, "All ".$currentYear." Payments ", 1,0,'C',true);
	$PDF->Cell(45,5, "Automatic System Receipt", 1,0,'C',true);
	$PDF->Cell(45,5, "Payment value (GHC) ", 1,0,'C',true);
	$PDF->Ln();
	
	$PDF->SetFont('Arial','',10);
	$qall = mysql_query("SELECT * 	FROM	`business_payments` 
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' AND
											`districtid` = '".$districtid."' AND
											`payment_date` > '".$currentYear."'
									ORDER BY `id` ASC");
	
	$i=1;								
	while( $rall = mysql_fetch_array($qall) )
	{		
		$PDF->SetFillColor(225,225,225);
		$PDF->Cell(45,5, $i, 1,0,'C',true);
		$PDF->SetFillColor(255,255,255);
		$PDF->Cell(45,5, $rall['payment_date'], 1,0,'C',true);
		$PDF->Cell(45,5, $rall['id'], 1,0,'C',true); 
		$PDF->Cell(45,5, $rall['payment_value'], 1,0,'C',true); 
		$PDF->Ln();
		$i++;
	}
	
	$PDF->Ln();
	$PDF->SetFont('Arial','B',10);
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(45,5, $currentYear." Balance *", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(135,5, $revenueBalance, 1,0,'C',true); $PDF->Ln();

	$PDF->Ln(20);
	$PDF->Cell(180,5, "* negative value indicates credit",0,0,'L',false);
	$PDF->Ln();
	$PDF->Output();

?>