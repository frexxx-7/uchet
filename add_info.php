<?php
session_start();
include './php/db.php';

$sqlEmployees = "SELECT Kod_Sotrudnika, Fam, Imya, Otch FROM Sotrudniki";
$resultEmployees = sqlsrv_query($conn, $sqlEmployees);

$sqlPositions = "SELECT Kod_Dolzhnosti, Imya FROM Dolzhnosti";
$resultPositions = sqlsrv_query($conn, $sqlPositions);

$sqlShifts = "SELECT Kod_Smeni, Imya FROM Smeni";
$resultShifts = sqlsrv_query($conn, $sqlShifts);

$sqlDepartments = "SELECT Kod_Otdela, Imya FROM Otdeli";
$resultDepartments = sqlsrv_query($conn, $sqlDepartments);

$sqlAbsenceReasons = "SELECT Kod_Prichini_Otsutstvia, Imya FROM Prichini_Otsutstvia";
$resultAbsenceReasons = sqlsrv_query($conn, $sqlAbsenceReasons);

if ($resultEmployees === false || $resultPositions === false || $resultShifts === false || $resultDepartments === false || $resultAbsenceReasons === false) {
  die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Внести данные</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="./CSS/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      display: flex;
    }

    .sidebar {
      width: 200px;
      background-color: #f8f9fa;
      padding: 15px;
      border-right: 1px solid #dee2e6;
      height: 100vh;
      position: fixed;
      right: -200px;
      transition: all 0.3s ease;
    }

    .sidebar.open {
      right: 0;
    }

    .sidebar a {
      display: block;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #007bff;
      color: white;
      text-align: center;
      text-decoration: none;
      border-radius: 5px;
    }

    .toggle-btn {
      position: fixed;
      right: 10px;
      top: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px;
      cursor: pointer;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    .toggle-btn.open {
      right: 210px;
    }

    .toggle-btn i {
      font-size: 20px;
    }

    .exit-button {
      position: absolute;
      width: calc(100% - 30px);
      bottom: 0;
      left: 15px;
    }

    @media screen and (max-width: 600px) {
      .sidebar {
        width: 85%;
        right: -85%;
      }

      .toggle-btn.open {
        right: 90%;
      }
    }
  </style>
</head>

<body>
  <div class="sidebar" id="sidebar">
    <a href="./add_info.php">Добавить</a>
    <a href="./edit_info.php">Редактировать</a>
    <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
      <a class="exit-button" href="./index.php" id="exitButton">Выход</a>
    <?php endif; ?>
  </div>

  <button class="toggle-btn" id="toggleBtn">
    <i id="menuIcon" class="fas fa-bars"></i>
  </button>

  <div class="container d-flex justify-content-center align-items-center min-vh-100" id="mainContent">
    <div class="auth-box">
      <h2 class="auth-header">Внести данные</h2>
      <div class="auth-divider"></div>
      <div class="form-group">
        <select class="form-control" id="employee">
          <option selected disabled>Выберите сотрудника</option>
          <?php
          while ($row = sqlsrv_fetch_array($resultEmployees, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['Kod_Sotrudnika'] . '">' . $row['Fam'] . ' ' . $row['Imya'] . ' ' . $row['Otch'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="position">
          <option selected disabled>Выберите должность</option>
          <?php
          while ($row = sqlsrv_fetch_array($resultPositions, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['Kod_Dolzhnosti'] . '">' . $row['Imya'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="shift">
          <option selected disabled>Выберите смену</option>
          <?php
          while ($row = sqlsrv_fetch_array($resultShifts, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['Kod_Smeni'] . '">' . $row['Imya'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="department">
          <option selected disabled>Выберите отдел</option>
          <?php
          while ($row = sqlsrv_fetch_array($resultDepartments, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['Kod_Otdela'] . '">' . $row['Imya'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <input type="date" class="form-control" id="date">
      </div>
      <div class="form-group">
        <input type="time" class="form-control" id="arrival-time" placeholder="Время прибытия">
        <button type="button" class="btn btn-secondary" id="btnArrivalTime">Текущее время</button>
      </div>
      <div class="form-group">
        <input type="time" class="form-control" id="departure-time" placeholder="Время ухода">
        <button type="button" class="btn btn-secondary" id="btnDepartureTime">Текущее время</button>
      </div>

      <div class="form-group">
        <select class="form-control" id="presence">
          <option>Пребывание</option>
          <option>Да</option>
          <option>Нет</option>
        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="absence-reason">
          <option selected disabled>Выберите причину отсутствия</option>
          <?php
          while ($row = sqlsrv_fetch_array($resultAbsenceReasons, SQLSRV_FETCH_ASSOC)) {
            echo '<option value="' . $row['Kod_Prichini_Otsutstvia'] . '">' . $row['Imya'] . '</option>';
          }
          ?>
        </select>
      </div>
      <button type="button" class="btn btn-primary btn-block">Сохранить</button>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#btnArrivalTime').click(function () {
        if ($('#absence-reason').val() !== '') {
          $('#absence-reason').prop('disabled', true);
          $('#absence-reason').val(null);
          $('#presence').val('Да');
          $('#presence').prop('disabled', true);
        } else {
          $('#absence-reason').prop('disabled', false);
          $('#presence').val('Нет');
          $('#presence').prop('disabled', false);
        }
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        $('#arrival-time').val(hours + ':' + minutes);
      });

      $('#btnDepartureTime').click(function () {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        $('#departure-time').val(hours + ':' + minutes);
      });

      $('#employee').change(function () {
        var employeeId = $(this).val();
        $.ajax({
          type: 'POST',
          url: './php/get_employee_details_forAdd.php',
          data: { employeeId: employeeId },
          success: function (response) {
            var data = JSON.parse(response);
            $('#position').html('<option value="' + data.positionId + '">' + data.position + '</option>');
            $('#shift').html('<option value="' + data.shiftId + '">' + data.shift + '</option>');
            $('#department').html('<option value="' + data.departmentId + '">' + data.department + '</option>');
            $('#position').prop('disabled', true);
            $('#shift').prop('disabled', true);
            $('#department').prop('disabled', true);
          },
          error: function (xhr, status, error) {
            console.error('Ошибка при получении данных сотрудника:', error);
            alert('Произошла ошибка при получении данных сотрудника. Пожалуйста, попробуйте снова.');
          }
        });
      });

      $('#position').change(function () {
        var positionId = $(this).val();
        $.ajax({
          type: 'POST',
          url: './php/get_employees_by_position.php',
          data: { positionId: positionId },
          success: function (response) {
            var data = JSON.parse(response);
            $('#employee').html('<option selected disabled>Выберите сотрудника</option>');
            data.employees.forEach(function (employee) {
              $('#employee').append('<option value="' + employee.id + '">' + employee.name + '</option>');
            });
          },
          error: function (xhr, status, error) {
            console.error('Ошибка при получении данных сотрудников:', error);
            alert('Произошла ошибка при получении данных сотрудников. Пожалуйста, попробуйте снова.');
          }
        });
      });

      $('#department').change(function () {
        var departmentId = $(this).val();
        $.ajax({
          type: 'POST',
          url: './php/get_employees_and_positions_by_department.php',
          data: { departmentId: departmentId },
          success: function (response) {
            var data = JSON.parse(response);
            $('#employee').html('<option selected disabled>Выберите сотрудника</option>');
            data.employees.forEach(function (employee) {
              $('#employee').append('<option value="' + employee.id + '">' + employee.name + '</option>');
            });
            $('#position').html('<option selected disabled>Выберите должность</option>');
            data.positions.forEach(function (position) {
              $('#position').append('<option value="' + position.id + '">' + position.name + '</option>');
            });
          },
          error: function (xhr, status, error) {
            console.error('Ошибка при получении данных сотрудников и должностей:', error);
            alert('Произошла ошибка при получении данных сотрудников и должностей. Пожалуйста, попробуйте снова.');
          }
        });
      });

      $('#shift').change(function () {
        var shiftId = $(this).val();
        $.ajax({
          type: 'POST',
          url: './php/get_employees_by_shift.php',
          data: { shiftId: shiftId },
          success: function (response) {
            var data = JSON.parse(response);
            $('#employee').html('<option selected disabled>Выберите сотрудника</option>');
            data.employees.forEach(function (employee) {
              $('#employee').append('<option value="' + employee.id + '">' + employee.name + '</option>');
            });
          },
          error: function (xhr, status, error) {
            console.error('Ошибка при получении данных сотрудников:', error);
            alert('Произошла ошибка при получении данных сотрудников. Пожалуйста, попробуйте снова.');
          }
        });
      });

      $('#arrival-time').change(function () {
        if ($(this).val() !== '') {
          $('#absence-reason').prop('disabled', true);
          $('#presence').val('Да');
          $('#absence-reason').val(null);
          $('#presence').prop('disabled', true);
        } else {
          $('#absence-reason').prop('disabled', false);
        }
      });


      $('button.btn-primary').click(function () {
        var arrivalTime = $('#arrival-time').val();
        var departureTime = $('#departure-time').val();
        if (!arrivalTime) {
          $('#arrival-time').val(null);
        }
        if (!departureTime) {
          $('#departure-time').val(null);
        }
        if (!arrivalTime && !departureTime) {
          $('#presence').val('Нет');
        }
        var employeeId = $('#employee').val();
        var date = $('#date').val();
        var arrivalTime = $('#arrival-time').val();
        var departureTime = $('#departure-time').val();
        var presence = $('#presence').val();
        var absenceReason = $('#absence-reason').val();

        $.ajax({
          type: 'POST',
          url: './php/save_data.php',
          data: {
            employeeId: employeeId,
            date: `'${date}'`,
            arrivalTime: arrivalTime ? `'${arrivalTime}'` : null,
            departureTime: departureTime ? `'${departureTime}'` : null,
            presence: `'${presence}'`,
            absenceReason: absenceReason ? `'${absenceReason}'` : null
          },
          success: function (response) {
            console.log(response);
            alert('Данные успешно сохранены!');
            window.location.reload()
          },
          error: function (xhr, status, error) {
            console.error('Ошибка при сохранении данных:', error);
            alert('Произошла ошибка при сохранении данных. Пожалуйста, попробуйте снова.');
          }
        });
      });

      var toggleBtn = document.getElementById('toggleBtn');
      var sidebar = document.getElementById('sidebar');
      var mainContent = document.getElementById('mainContent');
      var menuIcon = document.getElementById('menuIcon');

      if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
          sidebar.classList.toggle('open');
          mainContent.classList.toggle('shifted');
          toggleBtn.classList.toggle('open');

          if (sidebar.classList.contains('open')) {
            menuIcon.className = 'fas fa-times';
          } else {
            menuIcon.className = 'fas fa-bars';
          }
        });
      }

      $("#exitButton").click(function () {
        localStorage.removeItem('selectedUser');
        window.location.href = './index.php';
      })
    });
  </script>
</body>

</html>