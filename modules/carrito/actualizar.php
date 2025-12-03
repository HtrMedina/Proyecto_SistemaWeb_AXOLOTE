<?php
session_start();

$id = intval($_GET['id']);
$op = $_GET['op'];

if (!isset($_SESSION['carrito'][$id])) {
    header("Location: carrito.php");
    exit;
}

if ($op === "mas") {
    $_SESSION['carrito'][$id]++;
}

if ($op === "menos") {
    $_SESSION['carrito'][$id]--;

    if ($_SESSION['carrito'][$id] <= 0) {
        unset($_SESSION['carrito'][$id]);
    }
}

header("Location: carrito.php");
exit;
