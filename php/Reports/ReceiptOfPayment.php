<?php

	

	/*
	 *	Include the Library Code
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
	require_once(	"../../lib/configuration.php"	);	
	require_once(	"../../lib/System.PDF.php"		);
	
	$PDF		= new PDF('P','mm','A4');
	
	$upn = $_GET['upn'];
	$subupn = $_GET['subupn'];
	
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
	$PDF->Cell(95,5, "UPN",1,0,'C',true);
	$PDF->Cell(95,5, "SUBUPN",1,0,'C',true);			
	$PDF->Ln();
	
	
	$q = mysql_query("SELECT DISTINCT( `p`.`upn`) AS `upn`, 
										`p`.`subupn` AS `subupn`
		
								FROM	`property` `p`,
										`payments_property` `v`
								
								WHERE	`p`.`upn` = '".$upn."' AND
										`p`.`subupn` = '".$subupn."' AND
										`v`.`upn` = '".$upn."' 
									
								ORDER BY `p`.`upn` ASC");
	
	while($r = mysql_fetch_array($q))
	{			
		$PDF->SetFont('Arial','',8);	
		$PDF->SetFillColor(255,255,255);		
		$PDF->Cell(95,5, $upn,1,0,'C',true);
		$PDF->Cell(95,5, $subupn,1,0,'C',true);																		
		$PDF->Ln();			
	}

	$PDF->Ln(10);			
	
	$q2 = mysql_query("SELECT * FROM `property` WHERE `upn` = '".$upn."' LIMIT 1");		
	$r2 = mysql_fetch_array($q2);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "ADDRESS",1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r2['streetname']." ".$r2['housenumber'] ,1,0,'C',true); $PDF->Ln();
	//$PDF->MultiCell(0,5,$r2['streetname']." ".$r2['housenumber'],1,0,'C',true);
	
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "OWNER",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r2['owner'] ,1,0,'C',true); $PDF->Ln();
	
	$q3 = mysql_query("SELECT * FROM `payments_property` WHERE `upn` = '".$upn."' ORDER BY DESC ");		
	$r3 = mysql_fetch_array($q3);	
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "Pre 2013",1,0,'C',true);
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r3['balance_old'],1,0,'C',true); $PDF->Ln(10);
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(190,5, "2013",1,0,'C',true); $PDF->Ln();
	$PDF->Cell(30,5, "DUE",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r2['revenue_due'],1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "COLLECTED",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r2['revenue_collected'],1,0,'C',true); $PDF->Ln();
	
	$PDF->SetFillColor(225,225,225);
	$PDF->Cell(30,5, "BALANCE",1,0,'C',true); 
	$PDF->SetFillColor(255,255,255);
	$PDF->Cell(160,5, $r2['revenue_balance'],1,0,'C',true); $PDF->Ln();
	
	
	

	$PDF->Ln();
	$PDF->Output();

?>