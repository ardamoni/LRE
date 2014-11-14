<?php

	/*
	 *	Library
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
// 	require('System.FPDF.php');
	require('fpdf.php');

	/*
	 *	PDF Class
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 */
 	class PDF extends FPDF
	{	
		/*
		 *	PDF Report: Header
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */
		function Header()
		{
			$this->SetY(4);
			//$this->Image('Icons/Gov.Logo.png',10,4,18);
			
			$this->SetFont('Arial','',8);
			$this->Cell(0,3,'Date: '.date('d.m.Y').' ('.date('H:i:s').')'.'   ',0,1,'R');
			$this->Ln(5);
		}

		/*
		 *	PDF Report: Footer
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */
		function Footer()
		{
			$this->SetY(-10);			
			$this->SetFont('Arial','I',6);
			$this->SetTextColor(128);
			$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
		}		
		
		/*
		 *	PDF Report: Report Title
	 	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */
		function ReportTitle($title = "")
		{	
			$this->SetFont('Arial','B',18);
			$this->SetFillColor(239,239,239);
			$this->SetTextColor(0, 0, 0);
			$this->MultiCell(0,10,$title,0,'C',true);						
		}
	}
?>
