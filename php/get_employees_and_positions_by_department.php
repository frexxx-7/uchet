<?php
session_start();
include 'db.php';

$departmentId = $_POST['departmentId'];

$sqlEmployees = "SELECT s.Kod_Sotrudnika, s.Fam + ' ' + s.Imya + ' ' + s.Otch AS Name
                 FROM Sotrudniki s
                 WHERE s.Kod_Otdela = ?";

$params = array($departmentId);
$stmtEmployees = sqlsrv_query($conn, $sqlEmployees, $params);

if ($stmtEmployees === false) {
  die(print_r(sqlsrv_errors(), true));
}

$employees = array();

while ($row = sqlsrv_fetch_array($stmtEmployees, SQLSRV_FETCH_ASSOC)) {
  $employees[] = array(
    'id' => $row['Kod_Sotrudnika'],
    'name' => $row['Name']
  );
}

$sqlPositions = "SELECT d.Kod_Dolzhnosti, d.Imya
                 FROM Dolzhnosti d
                 WHERE EXISTS (
                   SELECT 1
                   FROM Sotrudniki s
                   WHERE s.Kod_Dolzhnosti = d.Kod_Dolzhnosti
                   AND s.Kod_Otdela = ?
                 )";

$stmtPositions = sqlsrv_query($conn, $sqlPositions, $params);

if ($stmtPositions === false) {
  die(print_r(sqlsrv_errors(), true));
}

$positions = array();

while ($row = sqlsrv_fetch_array($stmtPositions, SQLSRV_FETCH_ASSOC)) {
  $positions[] = array(
    'id' => $row['Kod_Dolzhnosti'],
    'name' => $row['Imya']
  );
}

echo json_encode(array('employees' => $employees, 'positions' => $positions));

sqlsrv_free_stmt($stmtEmployees);
sqlsrv_free_stmt($stmtPositions);
sqlsrv_close($conn);
?>
