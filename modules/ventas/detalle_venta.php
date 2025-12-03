<?php
session_start();
require_once('../../config/db.php');

// Solo usuarios logueados pueden acceder
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listar_ventas.php");
    exit;
}

$venta_id = intval($_GET['id']);

// Traer información de la venta y usuario
$stmt = $conn->prepare("
    SELECT v.ID_Venta, v.Fecha_Venta, v.Total, u.Usuario
    FROM Ventas v
    JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario
    WHERE v.ID_Venta = :id
");
$stmt->execute([':id' => $venta_id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    echo "<div class='alert alert-danger'>Venta no encontrada.</div>";
    exit;
}

// Traer detalle de productos de la venta
$detalleStmt = $conn->prepare("
    SELECT d.Cantidad, d.Precio_Unitario, p.Producto
    FROM Detalle_Venta d
    JOIN Productos p ON d.ID_Producto = p.ID_Producto
    WHERE d.ID_Venta = :id
");
$detalleStmt->execute([':id' => $venta_id]);
$detalles = $detalleStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once('../../includes/header.php'); ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-eye"></i> Detalle de la Venta N°<?= $venta['ID_Venta'] ?></h2>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($venta['Usuario']) ?></p>
            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['Fecha_Venta'])) ?></p>

            <?php if(count($detalles) === 0): ?>
                <div class="alert alert-warning mt-3">No hay productos registrados en esta venta.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($detalles as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['Producto']) ?></td>
                                <td><?= $d['Cantidad'] ?></td>
                                <td>$<?= number_format($d['Precio_Unitario'],2) ?></td>
                                <td>$<?= number_format($d['Cantidad'] * $d['Precio_Unitario'],2) ?></td>
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
            <?php endif; ?>

            <a href="listar_venta.php" class="btn btn-secondary mt-3">
                <i class="fa-solid fa-arrow-left"></i> Volver a ventas
            </a>
        </div>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>

