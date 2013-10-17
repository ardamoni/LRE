<?php
	require_once( "../lib/configuration.php" );

	$upn = $_GET["upn"];
	$subupn = $_GET["subupn"];	
		
	$options = explode(",",$subupn);	// array
	$name = 'subupn_dropDown';		 
	$selected = -1; 					// default selection is first on the list	
	$choice = dropdown( $name, $options, $selected );
	
?>

	
	<form id="form1" name="form1" method="post" action="RevenueCollection.php">	
	
	<script type="text/javascript" src="../js/jquery_validation.js"></script>
	<script type="text/javascript">
		$(document).ready(function() 
		{   			
			//the min chars for treceipt
			var min_chars = 1;
			
			//result texts
			var characters_error = 'Minimum amount of chars is 1';
			var checking_html = '<img src="images/loading.gif" /> Checking...';
			
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
			var treceipt = $('#treceipt').val();
			
			//use ajax to run the check
			$.post("formValidation.php", { upn: upn, subupn: subupn, treceipt: treceipt },
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
						$('#treceipt_availability_result').html('<span class="is_not_available"><b>' + treceipt + '</b> is not Available</span>');
					}
			});
		}  
	</script>
	
		<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
			<tr>
				<td colspan="3" bgcolor="#E6E6E6"><center><strong>Payment Form</strong></center></td>
			</tr>					
			<tr>
				<td width="14%">UPN</td>
				<td width="2%">:</td>
				<td width="84%"><input name="upn" type="hidden" id="upn" value = "<?php echo $upn;?>"><?php echo $upn;?></td>
			</tr>					
			<tr>
				<td width="14%">SUB-UPN</td>
				<td width="2%">:</td>						
				
				<!-- TODO: fix the dropdown choice -->
				<!--<td width="84%"><select name="subupn" id="subupn">
				<option value="<?php //echo $choice;?>">Please make your selection</option></select></td> -->
				<!-- TEMP: until the drop down is fixed -->
				<td width="84%"><input name="subupn" type="text" id="subupn" size="50">
			</tr>				
			<tr>
				<td width = "30">Payment date</td>
				<td width = "2">:</td>
				<td width = "68"><input type="hidden" name="paymentdate" id="paymentdate" size="50">
					<script language="JavaScript">
						today = new Date();
						//document.write("(YYYY-MM-DD) ", today.getFullYear(), "-", today.getMonth()+1, "-", today.getDate() );								
						document.write(today.toLocaleDateString());
					</script>
				</td>						
			</tr>			
			<tr>
				<td>Payed by</td>
				<td>:</td>
				<td><input name="payedby" type="text" id="payedby" size="50"></td>
			</tr>
			<tr>
				<td>Value</td>
				<td>:</td>
				<td><input name="payedvalue" type="text" id="payedvalue" size="50"></td>
			</tr>
			<tr>
				<td>Type of payment</td>
				<td>:</td>
				<td><input name="paymenttype" type="text" id="paymenttype" size="50"></td>
			</tr>
			<tr>
				<td>Ticketing receipt</td>
				<td>:</td>
				<td><input name="treceipt" type="text" id="treceipt" size="50"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><input type="button" id="Submit" name="Submit" value="Submit" /> 
					<input type="reset" id="Reset" name="Reset" value="Reset" /></td>
			</tr>
			<tr>
				<td> <div id="treceipt_availability_result">Availability Message</div> </td>
			</tr>
		</table>
	</form>

<?php
	// drop down 
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

		/*** and return the completed dropdown ***/				
		return $dropdown;
	}	
?>