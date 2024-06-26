<?php
session_start();
include 'db.php';

$employeeId = $_POST['employeeId'];
$date = $_POST['date'];

$arrivalTime = $_POST['arrivalTime'];
$departureTime = $_POST['departureTime'];
$presence = $_POST['presence'];
$absenceReason = $_POST['absenceReason'];

$setClauses = [];
$params = [];

if ($arrivalTime) {
  $setClauses[] = 'Vremia_Pribitia = ?';
  $params[] = $arrivalTime;
}
if ($departureTime) {
  $setClauses[] = 'Vremia_Uhoda = ?';
  $params[] = $departureTime;
}
if ($presence) {
  $setClauses[] = 'Prebivanie = ?';
  $params[] = $presence;
}
if ($absenceReason) {
  $setClauses[] = 'Kod_Prichini_Otsutstvia = ?';
  $params[] = $absenceReason;
}

$params[] = $employeeId;
$params[] = $date;

$sqlUpdate = "UPDATE Uchet_Rabochego_Vremeni 
              SET " . implode(', ', $setClauses) . " 
              WHERE Kod_Sotrudnika = ? AND Dataa = ?";

$stmt = sqlsrv_query($conn, $sqlUpdate, $params);
if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
} else {
  echo "Данные успешно обновлены!";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
