<?php
// DB connection
require_once("../../lib/configuration.php");

$st = $pdo->prepare("ALTER TABLE `usr_users` DROP PRIMARY KEY;");
    $st->execute();
/* Return number of rows that were affected */
print("Number of rows in 'usr_users' affected:\n");
$count = $st->rowCount();
print("Affected $count rows.\n<br>");

$st = $pdo->prepare("ALTER TABLE `usr_users` ADD `id` INT(11) UNSIGNED  NOT NULL  AUTO_INCREMENT  PRIMARY KEY  FIRST;");
    $st->execute();

/* Return number of rows that were affected */
print("Number of rows in 'usr_users' affected:\n");
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