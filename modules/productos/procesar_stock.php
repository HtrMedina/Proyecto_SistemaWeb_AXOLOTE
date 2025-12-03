<?php
session_start();
require_once('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);

    if ($cantidad <= 0) {
        $_SESSION['error'] = "Cantidad inválida";
        header("Location: listar_productos.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE Productos SET Stock = Stock + :cantidad WHERE ID_Producto = :id");
    $stmt->execute([
        ':cantidad' => $cantidad,
        ':id' => $id
    ]);

    $_SESSION['success'] = "Stock actualizado correctamente";
    header("Location: listar_productos.php");
}
