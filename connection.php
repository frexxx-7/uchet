<?php
$serverName = "DESKTOP-JRK2B6D";
$database = "uchet";
$uid = "";
$pass = "";

$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass
];

$conn = sqlsrv_connect($serverName, $connection);
if(!$conn)
    die(print_r(sqlsrv_errors(),true));
else
echo 'connection established';

?>
