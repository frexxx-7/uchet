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

  <div class="container d-flex justify-content-center align-items-center min-vh-100" id="updateForm">
    <div class="auth-box">
      <h2 class="auth-header">Редатировать данные</h2>
      <div class="auth-divider"></div>

      <div class="form-group">
        <label for="date">Выберите дату:</label>
        <input type="date" class="form-control" id="datePicker">
      </div>

      <div class="form-group">
        <select class="form-control" id="employee">

        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="position">

        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="shift">

        </select>
      </div>
      <div class="form-group">
        <select class="form-control" id="department">

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
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    function loadEmployeesData(selectedDate) {
      $.ajax({
        type: 'POST',
        url: './php/get_employee_data.php',
        data: {
          date: selectedDate
        },
        dataType: 'json',
        success: function (response) {
          console.log(response);

          if (response.employees && response.employees.length > 0) {
            $('#employee').empty();

            response.employees.forEach(function (employee) {
              $('#employee').append('<option value="' + employee.Kod_Sotrudnika + '">' + employee.Fam + ' ' + employee.Imya + ' ' + employee.Otch + '</option>');
            });

            $('#employee').prop('disabled', false);
            $('button.btn-primary').prop('disabled', true);

            if (response.employees.length === 1) {
              var employee = response.employees[0];
              $('#employee').val(employee.Kod_Sotrudnika).change();
            }
          } else {
            $('#employee').empty().append('<option selected disabled>Данных о сотрудниках за эту дату нет</option>');
            $('#employee').prop('disabled', true);
            $('button.btn-primary').prop('disabled', true);
          }
        },
        error: function (xhr, status, error) {
          console.error('Ошибка при получении данных о сотрудниках:', error);
          alert('Произошла ошибка при получении данных о сотрудниках. Пожалуйста, попробуйте снова.');
        }
      });
    }

    $(document).ready(function () {
      $('#btnArrivalTime').click(function () {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        $('#arrival-time').val(`${hours}:${minutes}`);
      });

      $('#btnDepartureTime').click(function () {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        $('#departure-time').val(`${hours}:${minutes}`);
      });
    });
    $('button.btn-primary').click(function () {
      var employeeId = $('#employee').val();
      var date = $('#date').val();
      var arrivalTime = $('#arrival-time').val();
      var departureTime = $('#departure-time').val();
      var presence = $('#presence').val();
      var absenceReason = $('#absence-reason').val();

      $.ajax({
        type: 'POST',
        url: './php/update_data.php',
        data: {
          employeeId: employeeId,
          date: date,
          arrivalTime: arrivalTime ? arrivalTime : null,
          departureTime: departureTime ? departureTime : null,
          presence: presence,
          absenceReason: absenceReason ? absenceReason : null
        },
        success: function (response) {
          console.log(response);
          alert('Данные успешно редактированы!');
          window.location.reload();
        },
        error: function (xhr, status, error) {
          console.error('Ошибка при сохранении данных:', error);
          alert('Произошла ошибка при сохранении данных. Пожалуйста, попробуйте снова.');
        }
      });
    });


    $(document).ready(function () {
      $('#datePicker').change(function () {
        var selectedDate = $(this).val();
        loadEmployeesData(selectedDate);
      });

      $(document).ready(function () {
        $('#position').prop('disabled', true);
        $('#shift').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#date').prop('disabled', true);
        $('button.btn-primary').prop('disabled', false);

        $('#datePicker').change(function () {
          var selectedDate = $(this).val();

          $.ajax({
            type: 'POST',
            url: './php/get_employee_data.php',
            data: {
              date: selectedDate
            },
            dataType: 'json',
            success: function (response) {
              console.log(response);

              if (response.employees && response.employees.length > 0) {
                $('#employee').empty();

                response.employees.forEach(function (employee) {
                  $('#employee').append('<option value="' + employee.Kod_Sotrudnika + '">' + employee.Fam + ' ' + employee.Imya + ' ' + employee.Otch + '</option>');
                });

                $('#employee').prop('disabled', false);
                $('button.btn-primary').prop('disabled', true);
              } else {
                $('#employee').empty().append('<option selected disabled>Данных о сотрудниках за эту дату нет</option>');
                $('#employee').prop('disabled', true);
                $('button.btn-primary').prop('disabled', true);
              }
            },
            error: function (xhr, status, error) {
              console.error('Ошибка при получении данных о сотрудниках:', error);
              alert('Произошла ошибка при получении данных о сотрудниках. Пожалуйста, попробуйте снова.');
            }
          });
        });

        $('#employee').change(function () {
          var selectedEmployeeId = $(this).val();

          $.ajax({
            type: 'POST',
            url: './php/get_employee_details.php',
            data: {
              employeeId: selectedEmployeeId,
              date: $('#datePicker').val()
            },
            dataType: 'json',
            success: function (response) {
              console.log(response);

              $('#position').val(response.position);
              $('#shift').val(response.shift);
              $('#department').val(response.department);
              $('#date').val(response.date);
              $('#arrival-time').val(response.arrivalTime);
              $('#departure-time').val(response.departureTime);
              $('#presence').val(response.presence);
              $('#absence-reason').val(response.absenceReason);

              $('#shift').append('<option >' + response.shift + '</option>');
              $('#position').append('<option >' + response.position + '</option>');
              $('#department').append('<option >' + response.department + '</option>');

              var dateOnly = response.date.date.split(' ')[0];
              $('#date').val(dateOnly);
              $('#arrival-time').val(response.arrival);
              $('#departure-time').val(response.departure);

              $('#presence').val(response.presence == 'Да' ? 'Да' : 'Нет');

              if ($('#arrival-time').val() !== '') {
                $('#absence-reason').prop('disabled', true);
                $('#absence-reason').val(null);
                $('#presence').val('Да');
                $('#presence').prop('disabled', true);
              } else {
                $('#absence-reason').prop('disabled', false);
              }

              $('#absence-reason option').each(function () {
                if ($(this).text() === response.absence) {
                  $(this).prop('selected', true);
                }
              });
              if ($('#absence-reason').val() !== '') {
                $('#absence-reason').prop('disabled', true);
                $('#absence-reason').val(null);
              } else {
                $('#absence-reason').prop('disabled', false);
              }
              $('button.btn-primary').prop('disabled', false);
            },
            error: function (xhr, status, error) {
              console.error('Ошибка при получении данных о сотруднике:', error);
              alert('Произошла ошибка при получении данных о сотруднике. Пожалуйста, попробуйте снова.');
            }
          });
        });
      });

      $("#exitButton").click(function () {
        localStorage.removeItem('selectedUser');
        window.location.href = './index.php';
      })
    });

    $(document).ready(function () {

      $('#arrival-time').change(function () {
        if ($(this).val() !== '') {
          $('#absence-reason').prop('disabled', true);
          $('#absence-reason').val(null);
          $('#presence').val('Да');
          $('#presence').prop('disabled', true);
        } else {
          $('#absence-reason').prop('disabled', false);
        }
      });

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

      var toggleBtn = document.getElementById('toggleBtn');
      var sidebar = document.getElementById('sidebar');
      var mainContent = document.getElementById('updateForm');
      var menuIcon = document.getElementById('menuIcon');

      if (toggleBtn && sidebar && mainContent && menuIcon) {
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
      } else {
        console.error('One or more elements could not be found');
      }
    });
  </script>
  <?php
  sqlsrv_free_stmt($resultEmployees);
  sqlsrv_free_stmt($resultPositions);
  sqlsrv_free_stmt($resultShifts);
  sqlsrv_free_stmt($resultDepartments);
  sqlsrv_close($conn);
  ?>
</body>

</html>