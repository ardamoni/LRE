<?php

	if( session_status() != 2 )
	{
		session_start();
	}
	//echo " post: ", $_SESSION['user']['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<?php echo "<title>".$_GET["title"]."</title>"; ?>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<!--<link rel="stylesheet" href="../style.css" type="text/css">-->
<link rel="stylesheet" href="../css/styles.css" type="text/css">
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css">

<style type="text/css">
form.demoForm fieldset {
    width: 900px;
    margin-bottom: 1em;
	border-color:#ffcc00;
}

table.formTblContainer {
    border-collapse: collapse;
    border-spacing: 0;
//    border-color:#ffcc00;
//	border: 1px solid #ccc;
	position:fixed;
	top:10%;
	left:7.25%;
	width:85%
}

table.formTblContainer tr {
	border: 1px solid #ccc;
	border-color:#ffcc00;
	font-size:1em;
	padding:2px;
//	width: 2em;
}

table.formTblContainer td{
	font-size:1em;
	text-align:left;
	padding:5px;
	left:5px;
//	width: 100%;
	border-color:#ffcc00;
}

table.formTblContainer td.c {
	text-align:left;
	padding:10px;
}

form.demoForm p {font-size:0.875em;}

form.demoForm submit {
	font-size:1em;
	float:right;}

form.demoForm lable {font-size:0.5em;}

</style>


<script type="text/javascript">
function checkBeforeSubmit(frm) {
    // JavaScript validation here
    // return false if error(s)

    //alert('This is just a demo form with no place to go.');
    //return false;

    return true; // to submit
}

</script>
</head>
<body>



<?php
	require_once( "../lib/configuration.php" );
	require_once("../lib/Revenue.php");
	require_once("../lib/System.php");


	$Data = new Revenue;

	$upn 			= $_GET["upn"];
	$subupn 		= $_GET["subupn"];
	$districtid 	= $_SESSION['user']['districtid'];  //$_GET['districtid'];
	$ifproperty 	= $_GET['ifproperty'];

     $districtinfo 	= $Data->getDistrictInfo( $districtid, 'district_name' ).' ('.$districtid.')';
//var_dump($_GET);

	if ($ifproperty == 'property'){
		echo "<h1><center>Enter revenue for this property</center></h1>";
	} else {
		if ($ifproperty == 'feesandfines'){
			echo "<h1><center>Enter revenue for collected Fee & Fine</center></h1>";
		}else{
			echo "<h1><center>Enter revenue for this business</center></h1>";
		}
	}
	if ($subupn == "" || $subupn == NULL || $subupn == 'null' || $subupn == "0")
//		if ($subupn == 'null')
	{ $subupn = ' - '; }

	$options = explode(",",$subupn);	// array
	$name = 'subupn_dropDown';
	$selected = -1; 					// default selection is first on the list
	$choice = dropdown( $name, $options, $selected );

//Options for type of payment selection
$System = new System;

$currentYear = $System->GetConfiguration("RevenueCollectionYear");

if ($System->GetConfiguration("PaymentType")!='empty')
{
$paymentType = explode(",",$System->GetConfiguration("PaymentType"));
}else{
	$paymentType = array(
		'cash' => 'cash',
		'cheque' => 'cheque',
		'bank transfer' => 'bank transfer'
		);
}

	if ($ifproperty == 'feesandfines'){
		$bind = array(
			":districtid" => $districtid,
			":year" => $currentYear);
		$result = $pdo->select("fee_fixing_feesfines", "districtid = :districtid AND year = :year", $bind);
		$feefidata = array();
		$i=0;
		foreach($result as $temp){
		foreach($temp as $key => $value) {
		$feeficodes = array();
		   if ($key=='code'){		$tcode = $value;}
		   if ($key=='class'){		$tclass = $value;}
		   if ($key=='category'){	$tcategory = $value;}
		   if ($key=='rate'){		$trate = $value;}

			$feeficodes['$i'] = $tcode.', '.$tclass.', '.$tcategory.', rate: '.$trate;
			$i++;
		}
			$feefidata[] = $feeficodes['$i'];
		}
// 		var_dump($data);
	}

//get the collector information
		$bind = array(
			":districtid" => $districtid);
		$result = $pdo->select("col_collectors", "districtid = :districtid", $bind);
		$coldata = array();
		$nooption = 'No collector selected';
		$i=1;
		$colcodes = array();
		$colcodes[0] = '0: '.$nooption;
		foreach($result as $temp){
		foreach($temp as $key => $value) {
		   if ($key=='LastName'){		$tlastname = $value;}
		   if ($key=='FirstName'){		$tfirstname = $value;}
		   if ($key=='id')		{		$tcollectorid = $value;}

			$colcodes['$i'] = $tcollectorid.': '.$tlastname.', '.$tfirstname;
			$i++;
		}
			$coldata[] = $colcodes['$i'];
		}


	if ($ifproperty == 'property'){
		echo '<form id="form1" name="form1" method="post" action="PropertyRevenueCollection.php">';
	} else {
		if ($ifproperty == 'feesandfines'){
			echo '<form id="form1" name="form1" method="post" action="FeesandFinesRevenueCollection.php">';
		}else{
			echo '<form id="form1" name="form1" method="post" action="BusinessRevenueCollection.php">';
		}
	}
?>
	<script type="text/javascript" src="../js/jquery_validation.js"></script>
	<script type="text/javascript">
		$(document).ready(function()
		{
			//the min chars for treceipt
			var min_chars = 1;

			//result texts
			var characters_error = 'Minimum amount of chars is 1';
			var checking_html = '<img src="../img/loading.gif" /> Checking...';

			//when button is clicked
			$('#Submit').click(function()
			{
				//run the character number check
				if($('#treceipt').val().length < min_chars){
					//if it's bellow the minimum show characters_error text
					$('#treceipt_availability_result').html(characters_error);
				}else{
					//else show the cheking_text and run the function to check
					$('#treceipt_availability_result').html(checking_html);
					check_availability();
				}
			});
		});

		//function to check treceipt availability
		function check_availability()
		{
			//get the treceipt
			var upn = $('#upn').val();
			var subupn = $('#subupn').val();
			if (subupn == ' - '){subupn='';}
			var collectorids = $('#collectorlist').val().split(":");
			var collectorid = collectorids[0];
			var treceipt = $('#treceipt').val();
			var districtid = $('#districtid').val();
			var type = $('#type').val();
			//use ajax to run the check
			$.post("formValidation.php", { upn: upn, subupn: subupn, collectorid: collectorid, treceipt: treceipt, type: type },
				function(result)
				{
					if( result == 1 )
					{
						//show that the treceipt is available
						$('#form1').submit();
					}
					else
					{
						//show that the treceipt is NOT available
						$('#treceipt_availability_result').html('<span class="is_not_available"><b>' + treceipt + '</b> is NOT available or was Used previously </span>');
						if (confirm('Do you still want to safe the payment?')) {
							// Save it!
							$('#form1').submit();
						} else {
							// Do nothing!
						}
					}
			});
		}
	</script>
<!--		<table class='formTblContainer'>
		<tr>
		<td>-->
<!--		<table class='formTbl' width="90%"><!-- border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">-->
		<table class='formTblContainer'><!-- border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">-->
			<tr>
				<td colspan="3" bgcolor="#E6E6E6"><center><strong>Payment Form</strong></center></td>
			</tr>
			<tr>
				<td width="25%">UPN</td>
				<td width="2%">:</td>
				<td class='c' width="50%"><input name="upn" type="hidden" id="upn" value = "<?php echo $upn;?>"><?php echo $upn;?><input name="ifproperty" type="hidden" id="ifproperty" value = "<?php echo $ifproperty;?>"></td>
			</tr>
			<tr>
			<?php
				if ($ifproperty != 'feesandfines'){
				echo '<td width="20%">SUB-UPN</td>';
				echo '<td width="2%">:</td>';
				echo '<td class="c" width="50%"><input name="subupn" type="hidden" id="subupn" size="50" value = "'.$subupn.'">'.$subupn.'</td>';
				echo '</tr>';
				echo '<tr>';
			}
			?>
				<td width="25%">District ID</td>
				<td width="2%">:</td>
				<td class='c' width="50%"><input name="did" type="hidden" id="did" value = "<?php echo $districtid;?>"><?php echo $districtinfo;?></td>
			</tr>
			<tr>
				<td width = "25%">Payment date</td>
				<td width = "2%">:</td>
				<td class='c' width = "50%"><input type="hidden" name="paymentdate" id="paymentdate" size="50">
					<script language="JavaScript">
						today = new Date();
						//document.write("(YYYY-MM-DD) ", today.getFullYear(), "-", today.getMonth()+1, "-", today.getDate() );
						document.write(today.toLocaleDateString());
					</script>
				</td>
			</tr>
			<tr>
			<?php
				if ($ifproperty != 	'feesandfines'){
					echo '<td>Paid by</td>';
					echo '<td>:</td>';
					echo '<td class="c"><input name="paidby" type="text" id="paidby" size="30"></td>';
				}else{
				echo '<td>Fee Fixing Class</td>';
				echo '<td>:</td>';
				echo '<td class="c">'.generateSelect("feeficode", $feefidata).'</td>';
				}
			?>
			</tr>
			<tr>
				<td>Value</td>
				<td>:</td>
				<td class='c'><input name="paidvalue" type="text" id="paidvalue" size="10"></td>
			</tr>
			<tr>
				<td>Type of payment</td>
				<td>:</td>
				<td class='c'><?php echo generateSelect('paymenttype', $paymentType);?></td>
				<!--<input name="paymenttype" type="text" id="paymenttype" size="10"></td>-->
			</tr>
			<tr>
				<td>Collector</td>
				<td>:</td>
				<td class='c'><?php echo generateSelect('collectorlist', $colcodes);?></td>
				<!--<input name="collector" type="text" id="collector" size="20"></td>-->
			</tr>
			<tr>
				<td>Ticketing receipt</td>
				<td>:</td>
				<td class='c'><input name="treceipt" type="text" id="treceipt" size="10"></td>
			</tr>
			<tr>
<!--				<td>&nbsp;</td>
				<td>&nbsp;</td>
-->				<td colspan="3" style="background-color:#E6E6E6;text-align:center;">
			<?php
					if ($ifproperty == 'property'){
						echo '<input name="type" type="hidden" id="type" size="50" value = "property"></input>';
					}else{
						if (!$ifproperty=='feesandfines'){
							echo '<input name="type" type="hidden" id="type" size="50" value = "feesandfines"></input>';
						} else {
							echo '<input name="type" type="hidden" id="type" size="50" value = "business"></input>';
						}
					}
			?>

					<input type="button" id="Submit" name="Submit" value="Submit"  class='orange-flat-small'/>
					<input type="reset" id="Reset" name="Reset" value="Reset" class='orange-flat-small' />
				</td>
			</tr>
			<tr>
				<td> <div id="treceipt_availability_result">Availability Message</div> </td>
			</tr>
		</table>
<!--		</td>
		</tr>
		</table>-->
	</form>

<?php


function generateSelect($name = '', $options = array()) {
	$html = '<select name="'.$name.'" id="'.$name.'" style="width:175px">';
	foreach ($options as $option => $value) {
		$html .= '<option value='.$value.'>'.$value.'</option>';
	}
	$html .= '</select>';
	return $html;
}

// drop down not used at the moment
function dropdown( $name, array $options, $selected=null )
	{
		/*** begin the select ***/
		$dropdown = '<select name="'.$name.'" id="'.$name.'">'."\n";

		$selected = $selected;
		/*** loop over the options ***/
		foreach( $options as $key=>$option )
		{
			/*** assign a selected value ***/
			$select = $selected==$key ? ' selected' : null;

			/*** add each option to the dropdown ***/
			$dropdown .= '<option value="'.$key.'"'.$select.'>'.$option.'</option>'."\n";
		}

		/*** close the select ***/
		$dropdown .= '</select>'."\n";
		if (strlen($dropdown)>50){
			$dropdown=substr($dropdown,0,50);
		}
		/*** and return the completed dropdown ***/
		return $dropdown;
	}
?>
</body>
</html>