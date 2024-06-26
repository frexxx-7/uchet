<?php
session_start();
include './php/db.php';

$sql = "
SELECT s.[Kod_Sotrudnika]
      ,s.[Fam]
      ,s.[Imya]
      ,s.[Otch]
FROM [dbo].[Sotrudniki] s
JOIN [dbo].[Dolzhnosti] d ON s.[Kod_Dolzhnosti] = d.[Kod_Dolzhnosti]
WHERE LOWER(d.[Imya]) LIKE LOWER('руководитель%')
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
  die(print_r(sqlsrv_errors(), true));
}

$sotrudniki = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $sotrudniki[] = $row;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Авторизация</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="./CSS/styles.css">
</head>

<body>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-box">
      <h2 class="auth-header">Авторизация</h2>
      <div class="auth-divider"></div>

      <?php
      if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
      }
      ?>

      <form action="./php/auth.php" method="POST" id="loginForm">
        <div class="form-group">
          <input type="text" class="form-control" id="role" name="role" placeholder="Выберите сотрудника..." required>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" id="login" name="login" placeholder="Логин" required>
        </div>
        <div class="form-group">
          <input type="password" class="form-control" id="password" name="password" placeholder="Пароль" required>
        </div>
        <div class="form-group d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label ml-3" for="remember">Запомнить меня</label>
          </div>
          <button type="submit" class="btn btn-primary">Вход</button>
        </div>
      </form>

    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function () {
      var employees = <?php echo json_encode($sotrudniki); ?>;
      var employeeNames = employees.map(function (emp) {
        return {
          label: emp.Fam + ' ' + emp.Imya + ' ' + emp.Otch,
          value: emp.Kod_Sotrudnika
        };
      });

      $("#role").autocomplete({
        source: employeeNames,
        select: function (event, ui) {
          $("#role").val(ui.item.label);
          $("#role-id").val(ui.item.value);
          return false;
        }
      }).focus(function (event) {
        $(this).autocomplete("search", " ");
      });

      var selectedUser = localStorage.getItem('selectedUser');
      if (selectedUser) {
        window.location.href = './add_info.php';
      }

      $("#loginForm").on('submit', function (event) {
        var rememberMe = $("#remember").is(':checked');
        if (rememberMe) {
          localStorage.setItem('selectedUser', $("#role").val());
        } else {
          localStorage.removeItem('selectedUser');
        }
      });
    });
  </script>
</body>

</html>