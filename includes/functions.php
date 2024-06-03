<?php
session_start();

function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
}

function isManager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Manajer';
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function isOperator() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Operator';
}

function redirectIfNotAuthorized() {
    if (!isAdmin() && !isOperator() && !isManager()) {
        header("Location: login.php");
        exit();
    }
}


?>

