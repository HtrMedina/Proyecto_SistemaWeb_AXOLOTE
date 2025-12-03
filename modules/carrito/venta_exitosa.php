<?php
session_start();
require_once('../../config/db.php');
include_once('../../includes/header.php');

if (!isset($_GET['id'])) {
    header("Location: ../catalogo/catalogo.php");
    exit;
}

$venta_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT v.ID_Venta, v.Fecha_Venta, v.Total, u.Usuario
                        FROM Ventas v
                        JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario
                        WHERE v.ID_Venta = :id");
$stmt->execute([':id' => $venta_id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

$detalleStmt = $conn->prepare("SELECT d.*, p.Producto
                               FROM Detalle_Venta d
                               JOIN Productos p ON d.ID_Producto = p.ID_Producto
                               WHERE d.ID_Venta = :id");
$detalleStmt->execute([':id' => $venta_id]);
$detalles = $detalleStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-body text-center p-5">
            <i class="fa-solid fa-circle-check fa-4x text-success mb-3"></i>
            <h2 class="card-title mb-3">¡Compra realizada con éxito!</h2>
            <p class="text-muted mb-4">Venta ID: <strong><?= $venta['ID_Venta'] ?></strong> | Fecha: <strong><?= $venta['Fecha_Venta'] ?></strong></p>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['Producto']) ?></td>
                            <td><?= $d['Cantidad'] ?></td>
                            <td>$<?= number_format($d['Precio_Unitario'],2) ?></td>
                            <td>$<?= number_format($d['Precio_Unitario'] * $d['Cantidad'],2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th>$<?= number_format($venta['Total'],2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <a href="../catalogo/catalogo.php" class="btn btn-success btn-lg mt-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver al catálogo
            </a>
        </div>
    </div>
</div>

<style>
    body {
        background: #f8f9fa;
    }
    .card {
        border-radius: 1rem;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>

<?php include_once('../../includes/footer.php');