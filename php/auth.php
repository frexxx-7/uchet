<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $_SESSION['error'] = "Login and password are required.";
        header('Location: ../index.php');
        exit();
    }

    // Prepare the SQL query to avoid SQL injection
    $sql = "SELECT * FROM Avtorizacia WHERE Login = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array($login));
    
    if ($stmt === false) {
        $_SESSION['error'] = "Database error: " . print_r(sqlsrv_errors(), true);
        header('Location: ../index.php');
        exit();
    }

    if (sqlsrv_execute($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        
        if ($user && $password === $user['Pass']) {
            $_SESSION['login'] = $login;
            header('Location: ../add_info.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid login or password.";
            header('Location: ../index.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Database error: " . print_r(sqlsrv_errors(), true);
        header('Location: ../index.php');
        exit();
    }
}
?>
