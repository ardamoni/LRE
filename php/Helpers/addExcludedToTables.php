<?php
// DB connection
require_once("../../lib/configuration.php");

$st = $pdo->prepare("ALTER TABLE `property` ADD `excluded` tinyint(1) DEFAULT NULL;");
    $st->execute();  

/* Return number of rows that were affected */
print("Number of rows in 'property' affected:\n");
$count = $st->rowCount();
print("Affected $count rows.\n<br>");

// $st = $pdo->prepare("ALTER TABLE `business` ADD `excluded` tinyint(1) DEFAULT NULL;");
//     $st->execute();  
// 
// /* Return number of rows that were affected */
// print("Number of rows in 'business' affected:\n");
// $count = $st->rowCount();
// print("Affected $count rows.\n");
    
?>