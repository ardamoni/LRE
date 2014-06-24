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
//	require_once( 	"../../lib/Revenue.php"			);
	require_once( 	"../../lib/BusinessRevenueClass.php"			);
	require_once( 	"../../lib/System.php"			);
	
	
	$PDF = new PDF('P','mm','A4');
 	$System = new System;
	$Data = new BusinessRevenue;
	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	$target = $_GET['target'];
	$districtId = $_GET['districtid'];
	
	$districtName = $Data->getDistrictInfo( $districtId, "district_name" );


// 	if ($target = 'property') {
// 		$Data = new Revenue;}
// 	if ($target = 'business') {
// 		$Data = new BusinessRevenue;}

//var_dump($_GET);		

	
	/*
	 * PDF Generation
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */	

	$PDF->AddPage();
	
	$PDF->Ln();
	
	$PDF->SetFont('Arial','B',16);
	$PDF->Cell(0,5,'Bills Register',0,1,'C');		$PDF->Ln();
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
			$PDF->Cell(50,5, "BUSINESSNAME", 1,0,'C',true);
			$PDF->Cell(40,5, "OWNER", 1,0,'C',true);
			$PDF->Cell(30,5, "ADDRESS", 1,0,'C',true);
			$PDF->Cell(15,5, "PHONE", 1,0,'C',true);
			$PDF->Cell(20,5, "REMARKS", 1,0,'C',true);
			$PDF->Ln();
			
			// Data from Tables
			$n = 0;
			$PDF->SetFont('Arial','',6);
			$PDF->SetFillColor(255,255,255);

			$q = mysql_query("SELECT business.id, 
								business.`upn`, 
								business.`subupn`, 
								business.`streetname`, 
								business.`housenumber`, 
								business.`business_name`, 
								business.`owner_tel`, 
								business.`owner`, 
								demand_notice_record.`upn`, 
								demand_notice_record.`subupn`, 
								demand_notice_record.`value`,
								demand_notice_record.`billprintdate`
							FROM business INNER JOIN demand_notice_record 
								ON business.`upn` = demand_notice_record.`upn` AND business.`subupn` = demand_notice_record.`subupn`
							WHERE demand_notice_record.`comments`='".$target."'");
							
			$counter = 0;
			while( $r = mysql_fetch_array($q) )
			{
				$address = $r['housenumber'].' '.$r['streetname'];

// could not get the linebreak to work
 				if (strlen(trim($businessname))>=50){
 					$businessname = substr($r['business_name'],0,50).'...';
 				}else{
 					$businessname = $r['business_name'];
 				}
				$PDF->Cell(16,5, $r['upn'],1,0,'C',true);
				$PDF->Cell(18,5, $r['subupn'],1,0,'C',true);	
// 				$PDF->Cell(18,5, $r['billprintdate'],1,0,'C',true);	
				$PDF->Cell(50,5, $businessname,1,0,'C',true);			
				$PDF->Cell(40,5, $r['owner'], 1,0,'C',true);
				$PDF->Cell(30,5, $address, 1,0,'C',true);
				$PDF->Cell(15,5, $r['owner_tel'], 1,0,'C',true);
				$PDF->Cell(20,5, '', 1,0,'C',true);
				$PDF->Ln();
				$n = $n + 1;
			}
						
			$PDF->Ln(10);			
				
	$PDF->Ln();
	$PDF->Output();

?>