<?php
session_start();

$id = intval($_GET['id']);

if (isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header("Location: carrito.php");
exit;
