<?php 
if(isset($_SESSION['user']['name']))
	$_SESSION['user']['name']=$_SESSION['user']['name']+1;
else
	$_SESSION['user']['name']='ekke - no isset';

echo json_encode($_SESSION['user']['name']);
?>
