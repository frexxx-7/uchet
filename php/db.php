<?php
$serverName = "DESKTOP-JRK2B6D";
$database = "uchet";
$uid = "";
$pass = "";

$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
    "CharacterSet" => "UTF-8"
];

$conn = sqlsrv_connect($serverName, $connection);
if(!$conn)
    die(print_r(sqlsrv_errors(),true));