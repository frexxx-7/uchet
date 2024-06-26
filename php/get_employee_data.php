<?php
session_start();
include 'db.php';

if (isset($_POST['date']) && !empty($_POST['date'])) {
  $selectedDate = $_POST['date'];

  $sql = "SELECT sr.Kod_Sotrudnika, sr.Fam, sr.Imya, sr.Otch 
          FROM Sotrudniki sr
          WHERE sr.Kod_Sotrudnika IN (
              SELECT urv.Kod_Sotrudnika
              FROM Uchet_Rabochego_Vremeni urv
              WHERE urv.Dataa = ?
          )";

  $params = array($selectedDate);
  $stmt = sqlsrv_query($conn, $sql, $params);

  if ($stmt === false) {
      die(print_r(sqlsrv_errors(), true));
  }

  $employees = array();
  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      $employees[] = $row;
  }

  sqlsrv_free_stmt($stmt);

  sqlsrv_close($conn);

  if (count($employees) > 0) {
      echo json_encode(array('employees' => $employees));
  } else {
      echo json_encode(array('message' => 'Данных о сотрудниках за эту дату не найдены'));
  }

} else {
  // Обрабатываем случай, когда параметр date не установлен или пуст
  echo json_encode(array('error' => 'Параметр date отсутствует или пуст.'));
}
?>