<?php
session_start();

$id = intval($_GET['id']);

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id] = 1;
} else {
    $_SESSION['carrito'][$id]++;
}

header("Location: ../catalogo/catalogo.php?agregado=1");
exit;
