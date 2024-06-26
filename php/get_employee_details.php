<?php
session_start();
include 'db.php';

$employeeId = $_POST['employeeId'];
$date = $_POST['date'];

$sql = "SELECT 
            r.Imya AS Dolzhnost, 
            m.Imya AS Smena, 
            o.Imya AS Otdel,
            u.Dataa, 
            u.Vremia_Pribitia, 
            u.Vremia_Uhoda, 
            u.Prebivanie, 
            p.Imya as Prichini_Otsutstvia
        FROM 
            Uchet_Rabochego_Vremeni u
            JOIN Sotrudniki s ON u.Kod_Sotrudnika = s.Kod_Sotrudnika
            LEFT JOIN Dolzhnosti r ON s.Kod_Dolzhnosti = r.Kod_Dolzhnosti
            LEFT JOIN Smeni m ON s.Kod_Smeni = m.Kod_Smeni
            LEFT JOIN Otdeli o ON s.Kod_Otdela = o.Kod_Otdela
            LEFT JOIN Prichini_Otsutstvia p ON u.Kod_Prichini_Otsutstvia = p.Kod_Prichini_Otsutstvia
        WHERE 
            u.Kod_Sotrudnika = ?
            AND CONVERT(DATE, u.Dataa) = CONVERT(DATE, ?)";

$params = array($employeeId, $date);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
}

$employeeDetails = array();

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $employeeDetails = array(
    'position' => $row['Dolzhnost'],
    'shift' => $row['Smena'],
    'department' => $row['Otdel'],
    'date' => $row['Dataa'],
    'arrival' => $row['Vremia_Pribitia'] ? $row['Vremia_Pribitia']->format('H:i:s') : null,
    'departure' => $row['Vremia_Uhoda'] ? $row['Vremia_Uhoda']->format('H:i:s') : null,
    'presence' => $row['Prebivanie'] ? $row['Prebivanie'] : null, 
    'absence' => $row['Prichini_Otsutstvia']
  );
}

echo json_encode($employeeDetails);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>