<?php

	class ExcelImport
	{

		/*
		 *	Getting Data from Excel for CBA
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 */
		function CBAtoMySQL( $p = "", $v = "", $b = "", $c = "" ) // p - proposal, v - version, b - baseyear, c - cbafile.xls
		{	
			if($c == "")
			{
				$q = mysql_query("SELECT * FROM `pro_proposal_versions` WHERE `pid` = '".$p."' AND `verid` = '".$v."' AND `baseyear` = '".$b."'");
				$r = mysql_fetch_array($q);
				$c = $r['cba_file'];
			}
			
			if($c != "")
			{
				require_once 'excel/reader.php';
				
				$data = new Spreadsheet_Excel_Reader();
				
				$data->setOutputEncoding('CP1251');
	
				$data->read('Proposals/'.$p.'/'.$c);   // this is the folder where the CBA is stored
			
				$q = mysql_query("SELECT * FROM `fin_option_cost` WHERE `pid` = '".$p."' AND `verid` = '".$v."' AND `baseyear` = '".$b."' ORDER BY `id` ASC");
				$i = 4;
				$x = 1;
			
				while($row = mysql_fetch_array($q))
				{		
					// update the table in the DB.
					mysql_query("UPDATE `fin_option_cost` 
										SET `total` 			= '".@$data->sheets[5]['cells'][$i - 1][5]."', 	
											`FinancialNPV` 		= '".@$data->sheets[5]['cells'][$i + 0][2]."', 	
											`EconomicNPV` 		= '".@$data->sheets[5]['cells'][$i + 1][2]."', 	
											`FinancialIRR`		= '".@$data->sheets[5]['cells'][$i + 0][8]."',		
											`EconomicIRR`		= '".@$data->sheets[5]['cells'][$i + 1][8]."',
											`EconomicRatio`		= '".@$data->sheets[5]['cells'][$i + 1][5]."'
										WHERE `id` = '".$row['id']."'");
					
					$i += 8;
					$x++;
				}
			}
		}

	}
?>