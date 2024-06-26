<?php
session_start();
include 'db.php';

$employeeId = $_POST['employeeId'];
$date = $_POST['date'];
$arrivalTime = $_POST['arrivalTime'];
$departureTime = $_POST['departureTime'];
$presence = $_POST['presence'];
$absenceReason = $_POST['absenceReason'];

$columns = [];
$values = [];
$params = [];

if ($employeeId) {
  $columns[] = 'Kod_Sotrudnika';
  $values[] = '?';
  $params[] = $employeeId;
}
if ($date) {
  $columns[] = 'Dataa';
  $values[] = '?';
  $params[] = $date;
}
if ($arrivalTime) {
  $columns[] = 'Vremia_Pribitia';
  $values[] = '?';
  $params[] = $arrivalTime;
}
if ($departureTime) {
  $columns[] = 'Vremia_Uhoda';
  $values[] = '?';
  $params[] = $departureTime;
}
if ($presence) {
  $columns[] = 'Prebivanie';
  $values[] = '?';
  $params[] = $presence;
}
if ($absenceReason) {
  $columns[] = 'Kod_Prichini_Otsutstvia';
  $values[] = '?';
  $params[] = $absenceReason;
}

$sqlInsert = "INSERT INTO Uchet_Rabochego_Vremeni (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $params) . ")";
$stmt = sqlsrv_query($conn, $sqlInsert);
if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
} else {
  echo "Данные успешно сохранены!";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>