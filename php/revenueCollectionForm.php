<?php
	$upn = $_GET["upn"];
	$subupn = $_GET["subupn"];	
		
	$options = explode(",",$subupn);	// array
	$name = 'subupn_dropDown';		 
	$selected = -1; 					// default selection is first on the list	
	$choice = dropdown( $name, $options, $selected );
	
?>

	<table width="400" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
		<tr>
			<form id="form1" name="form1" method="post" action="RevenueCollection.php">
				<td>
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
						<td><input type="submit" name="Submit" value="Submit" /> 
							<input type="reset" name="Reset" value="Reset" /></td>
					</tr>
				</table>
				</td>
			</form>
		</tr>
	</table>

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