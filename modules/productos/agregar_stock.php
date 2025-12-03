<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

if (!isset($_GET['id'])) exit('Producto no especificado');
$id = intval($_GET['id']);

// Traer datos del producto
$stmt = $conn->prepare("SELECT Producto, Stock FROM Productos WHERE ID_Producto = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) exit('Producto no encontrado');
?>

<form action="procesar_stock.php" method="POST" id="formAgregarStock">
    <input type="hidden" name="id_producto" value="<?= $id ?>">
    <p><strong>Producto:</strong> <?= htmlspecialchars($producto['Producto']) ?></p>
    <p><strong>Stock actual:</strong> <?= $producto['Stock'] ?></p>

    <div class="mb-3">
        <label class="form-label">Cantidad a agregar</label>
        <input type="number" name="cantidad" class="form-control" min="1" required>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success">Agregar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </div>
</form>
