<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'crear';

try {
  if ($action === 'crear') {
    $categoria = trim($_POST['categoria'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if (!$categoria) throw new Exception('El nombre de la categoría es obligatorio');

    $stmt = $conn->prepare("INSERT INTO Categoria (Categoria, Descripcion) VALUES (:categoria, :descripcion)");
    $stmt->execute([':categoria' => $categoria, ':descripcion' => $descripcion]);
    echo json_encode(['status' => 'success', 'message' => 'Categoría registrada correctamente']);
  }

  elseif ($action === 'editar') {
    $id = intval($_POST['id_categoria'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if (!$id || !$categoria) throw new Exception('Datos incompletos');

    $stmt = $conn->prepare("UPDATE Categoria SET Categoria=:categoria, Descripcion=:descripcion WHERE ID_Categoria=:id");
    $stmt->execute([':categoria' => $categoria, ':descripcion' => $descripcion, ':id' => $id]);
    echo json_encode(['status' => 'success', 'message' => 'Categoría actualizada correctamente']);
  }

  elseif ($action === 'eliminar') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) throw new Exception('ID no válido');
    $stmt = $conn->prepare("DELETE FROM Categoria WHERE ID_Categoria=:id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['status' => 'success', 'message' => 'Categoría eliminada correctamente']);
  }

} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
