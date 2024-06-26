<?php
session_start();
include 'db.php';

$shiftId = $_POST['shiftId'];

$sql = "SELECT s.Kod_Sotrudnika, s.Fam + ' ' + s.Imya + ' ' + s.Otch AS Name
        FROM Sotrudniki s
        WHERE s.Kod_Smeni = ?";

$params = array($shiftId);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
}

$employees = array();

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $employees[] = array(
    'id' => $row['Kod_Sotrudnika'],
    'name' => $row['Name']
  );
}

echo json_encode(array('employees' => $employees));

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
