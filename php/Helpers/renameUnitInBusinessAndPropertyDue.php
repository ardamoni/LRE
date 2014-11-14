<?php
// DB connection
require_once("../../lib/configuration.php");

//alter property_due
$st = $pdo->prepare("ALTER TABLE `property_due` CHANGE `unit` `feefi_unit` varchar(30) DEFAULT NULL;");
    $st->execute();  

/* Return number of rows that were affected */
$count = $st->rowCount();
print("Number of rows in 'property_due' affected: $count\n");

//alter business_due
$st = $pdo->prepare("ALTER TABLE `business_due` CHANGE `unit` `feefi_unit` varchar(30) DEFAULT NULL;");
    $st->execute();  

/* Return number of rows that were affected */
$count = $st->rowCount();
print("Number of rows in 'business_due' affected: $count\n");

// $st = $pdo->prepare("ALTER TABLE `business` ADD `excluded` tinyint(1) DEFAULT NULL;");
//     $st->execute();  
// 
// /* Return number of rows that were affected */
// print("Number of rows in 'business' affected:\n");
// $count = $st->rowCount();
// print("Affected $count rows.\n");
    
?>