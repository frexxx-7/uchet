<?php
session_start();
include 'db.php';

$employeeId = $_POST['employeeId'];

$sql = "SELECT 
            s.Kod_Sotrudnika,
            d.Kod_Dolzhnosti, 
            d.Imya AS Dolzhnost, 
            m.Kod_Smeni,
            m.Imya AS Smena, 
            o.Kod_Otdela,
            o.Imya AS Otdel
        FROM 
            Sotrudniki s
            LEFT JOIN Dolzhnosti d ON s.Kod_Dolzhnosti = d.Kod_Dolzhnosti
            LEFT JOIN Smeni m ON s.Kod_Smeni = m.Kod_Smeni
            LEFT JOIN Otdeli o ON s.Kod_Otdela = o.Kod_Otdela
        WHERE 
            s.Kod_Sotrudnika = ?";

$params = array($employeeId);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
}

$employeeDetails = array();

if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $employeeDetails = array(
    'positionId' => $row['Kod_Dolzhnosti'],
    'position' => $row['Dolzhnost'],
    'shiftId' => $row['Kod_Smeni'],
    'shift' => $row['Smena'],
    'departmentId' => $row['Kod_Otdela'],
    'department' => $row['Otdel']
  );
}

echo json_encode($employeeDetails);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
