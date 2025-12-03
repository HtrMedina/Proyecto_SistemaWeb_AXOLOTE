<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');
include_once('../../includes/header.php');

// Carrito vacío
$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    $productos = [];
} else {

    // Obtener IDs para consulta
    $ids = implode(",", array_keys($carrito));

    // Traer información de los productos
    $stmt = $conn->query("SELECT * FROM Productos WHERE ID_Producto IN ($ids)");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calcular total
$total = 0;
foreach ($productos as $p) {
    $total += $p['Precio'] * $carrito[$p['ID_Producto']];
}
?>

<div class="container mt-5">

    <h2><i class="fa-solid fa-cart-shopping"></i> Carrito de Compras</h2>
    <hr>

    <?php if (empty($carrito)): ?>

        <div class="alert alert-warning text-center">
            Tu carrito está vacío.
        </div>

    <?php else: ?>

        <div class="card shadow">
            <div class="card-body">

                <!-- Tabla productos -->
                <table class="table align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($p['Imagen']): ?>
                                        <img src="/AXOLOTE/<?= $p['Imagen'] ?>" 
                                             class="me-3" width="60" height="60" style="object-fit:cover;">
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($p['Producto']) ?></strong>
                                </div>
                            </td>

                            <td class="fw-bold">
                                $<?= number_format($p['Precio'], 2) ?>
                            </td>

                            <td style="width:150px;">
                                <div class="d-flex">

                                    <!-- Botón restar -->
                                    <a href="actualizar.php?id=<?= $p['ID_Producto'] ?>&op=menos"
                                       class="btn btn-outline-secondary btn-sm">−</a>

                                    <input type="text" 
                                           value="<?= $carrito[$p['ID_Producto']] ?>" 
                                           class="form-control text-center mx-2" 
                                           disabled style="width:45px;">

                                    <!-- Botón sumar -->
                                    <a href="actualizar.php?id=<?= $p['ID_Producto'] ?>&op=mas"
                                       class="btn btn-outline-secondary btn-sm">+</a>

                                </div>
                            </td>

                            <td class="fw-bold text-success">
                                $<?= number_format($p['Precio'] * $carrito[$p['ID_Producto']], 2) ?>
                            </td>

                            <td>
                                <a href="eliminar.php?id=<?= $p['ID_Producto'] ?>" 
                                   class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <hr>

                <!-- Total -->
                <div class="text-end">
                    <h3>Total: 
                        <span class="text-success">$<?= number_format($total, 2) ?></span>
                    </h3>

                    <a href="procesar_venta.php" class="btn btn-success btn-lg mt-3">
                        <i class="fa-solid fa-cash-register"></i> Proceder Venta
                    </a>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php include_once('../../includes/footer.php'); ?>
