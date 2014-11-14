<?php
	// OBSOLETE FILE - 15.07.2014 Arben
	// use Receipt of Property payment and 
	// receipt of Business Payment
	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);	
	require_once(	"../../lib/System.PDF.php"		);
	require_once( 	"../../lib/Revenue.php"				);
	
	$Data = new Revenue;
	
	$PDF		= new PDF('P','mm','A4');
	
	$upn = $_GET['upn'];
	$subupn = $_GET['subupn'];

	$rsys_config = mysql_query("SELECT * 	FROM	`system_config`");	
	$sys_config_content = array();
	//now we put the result into a multi dimensional array
	while ($rasys_config = mysql_fetch_assoc($rsys_config)) { //get the content of our query and store it in an array
		$sys_config_content[] = array ( $rasys_config['variable'] => $rasys_config['value'] );
	};
	
//	now get the corresponding value out of the multidimensional array
	foreach($sys_config_content as $temp) {
		foreach($temp as $key => $value) {
			if ($key == 'RevenueCollectionYear') {
				$currentYear = (int) $value;
			}
		}
	}
	
	//echo "upn: ", $upn, ", subupn: ", $subupn, "</br>";
	
	
	
	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->SetFont('Arial','B',16);
	$PDF->Cell(195,5,'Payment Receipt Title',0,0,'C');
	$PDF->Ln(10);	
	$PDF->SetFont('Arial','B',14);
	$PDF->Cell(195,10,"sub title if needed",0,0,'C');
	$PDF->Ln(15);
	
	// 1st row
	$PDF->SetFont('Arial','B',8);
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(43,5, "UPN",1,0,'C',true);
	$PDF->Cell(43,5, "SUBUPN",1,0,'C',true);
	$PDF->Cell(42,5, "TICKET RECEIPT",1,0,'C',true);
	$PDF->Cell(42,5, "ELECTRONIC RECEIPT",1,0,'C',true);
	$PDF->Ln();
	
	$qqq = mysql_query("SELECT * 	FROM	`property_payments` 
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."'  
									ORDER BY `id` DESC LIMIT 1");
	
	//$ropay = mysql_num_rows($qqq);  $PDF->Cell(85,5, $ropay, 1,0,'C',true); $PDF->Ln(10);
	$rqus = mysql_fetch_array($qqq);
				
	$PDF->SetFont('Arial','',8);	
	$PDF->SetFillColor(255,255,255);		
	$PDF->Cell(43,5, $upn, 1,0,'C',true);
	$PDF->Cell(43,5, $subupn, 1,0,'C',true);
	$PDF->Cell(42,5, $rqus['receipt_payment'], 1,0,'C',true);
	$PDF->Cell(42,5, $rqus['id'], 1,0,'C',true);
	$PDF->Ln(10);				
	
	$que = mysql_query("SELECT * FROM	`property` 
								WHERE 	`upn` = '".$upn."' AND
										`subupn` = '".$subupn."' ");	
	
	//$rows = mysql_num_rows($que);  $PDF->Cell(85,5, $rows, 1,0,'C',true); $PDF->Ln(10);
	$rq = mysql_fetch_array($que);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "ADDRESS", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $rq['streetname']." ".$rq['housenumber'] ,1,0,'C',true); $PDF->Ln();
	//$PDF->MultiCell(0,5,$r2['streetname']." ".$r2['housenumber'],1,0,'C',true);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "OWNER", 1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $rq['owner'], 1,0,'C',true); $PDF->Ln();
	
	$revenueBalancePrevious = 0.0;
	for( $years = "2012"; $years<$currentYear; $years++ )
	{
		$revenueBalancePrevious += $Data->getPropertyBalanceInfo( $upn, $subupn, $years, "balance" );
	}	
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "Pre ".$currentYear." Balance *", 1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $revenueBalancePrevious, 1,0,'C',true); $PDF->Ln();
	
	$query = mysql_query("SELECT * 	FROM	`property_balance` 
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' AND
											`year` = '".$currentYear."' ");
	
	//$robal = mysql_num_rows($query);  $PDF->Cell(85,5, $robal, 1,0,'C',true); $PDF->Ln(10);
	$results = mysql_fetch_array($query);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, $currentYear, 1,0,'C',true); $PDF->Ln();
	$PDF->Cell(30,5, "DUE", 1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $results['due'], 1,0,'C',true); $PDF->Ln();
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "FEES & FINES", 1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
// not correct needs ATTENTION !!!	$PDF->Cell(140,5, $results['feefi_value'], 1,0,'C',true); $PDF->Ln();
	
	$qqq = mysql_query("SELECT * 	FROM	`property_payments` 
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."'  
									ORDER BY `id` DESC LIMIT 1");
	
	//$ropay = mysql_num_rows($qqq);  $PDF->Cell(85,5, $ropay, 1,0,'C',true); $PDF->Ln(10);
	$rqus = mysql_fetch_array($qqq);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "LAST COLLECTION",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $rqus['payment_value'],1,0,'C',true); $PDF->Ln();
	
	$query = mysql_query("SELECT * 	FROM	`property_balance` 
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' AND
											`year` = '".$currentYear."' ");
	
	//$robal = mysql_num_rows($query);  $PDF->Cell(85,5, $robal, 1,0,'C',true); $PDF->Ln(10);
	$results = mysql_fetch_array($query);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "COLLECTED",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $results['paid'],1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "BALANCE *",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $results['balance'],1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "OVERALL BALANCE *",1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(140,5, $results['balance']+$revenueBalancePrevious, 1,0,'C',true); $PDF->Ln();
	

	$PDF->Ln(20);
	$PDF->Cell(180,5, "* negative value indicates credit",0,0,'L',false);
	$PDF->Ln();
	$PDF->Output();

?>