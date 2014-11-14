<?
  function get_sub_upn($arg_1)
  {
	$con=mysqli_connect("localhost","root","root","LUPMIS");
	// Check connection
	if (mysqli_connect_errno())
	  {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	  }
	$subReturn='';  
	$qrun = "SELECT d2.* from `property` d2 WHERE d2.`upn` ='".$arg_1."';";
//	echo $qrun;
	$qSub=mysqli_query($con,$qrun, MYSQLI_USE_RESULT) or die ('Error updating database: ' . mysqli_error());
	while ($qrow = mysqli_fetch_assoc($qSub)) {
	   $subReturn .= $qrow['subupn']."<br>"
	   ."<b>Payment Status: </b>".$qrow['pay_status']."<br>"
	   ."<b>Revenue Due: </b>".$qrow['revenue_due']."<br>"
	   ."<b>Revenue Collected: </b>".$qrow['revenue_collected']."<br>"
	   ."<b>Revenue Balance: </b>".$qrow['revenue_balance']."<br>"
	   ."<b>Payment Date: </b>".$qrow['date_payment']."<br>"
	   ."------------------------------<br>";
	}
	return $subReturn;
  }
?>