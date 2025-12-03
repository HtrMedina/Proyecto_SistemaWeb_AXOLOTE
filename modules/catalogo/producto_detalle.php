<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');

// Validar ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "ID inválido";
    exit;
}

// Traer producto
$stmt = $conn->prepare("SELECT p.*, c.Categoria AS NombreCategoria
                        FROM Productos p
                        LEFT JOIN Categoria c ON p.ID_Categoria = c.ID_Categoria
                        WHERE p.ID_Producto = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no encontrado";
    exit;
}

include_once('../../includes/header.php');
?>

<div class="container mt-4">

    <!-- Botón regresar -->
    <a href="catalogo.php" class="btn btn-secondary mb-3">
        <i class="fa-solid fa-arrow-left"></i> Volver al catálogo
    </a>

    <div class="row">

        <!-- Imagen -->
        <div class="col-md-5">
            <?php if ($producto['Imagen']): ?>
                <img src="/AXOLOTE/<?= htmlspecialchars($producto['Imagen']) ?>" 
                     class="img-fluid rounded shadow-sm">
            <?php else: ?>
                <div class="bg-secondary text-white text-center p-5 rounded">
                    Sin imagen
                </div>
            <?php endif; ?>
        </div>

        <!-- Información -->
        <div class="col-md-7">
            <h2><?= htmlspecialchars($producto['Producto']) ?></h2>

            <p class="text-muted">
                Categoría: <strong><?= $producto['NombreCategoria'] ?? 'Sin categoría' ?></strong>
            </p>

            <h3 class="text-primary mb-3" style="font-weight: bold;">
                $<?= number_format($producto['Precio'], 2) ?>
            </h3>

            <p><?= nl2br(htmlspecialchars($producto['Descripcion'])) ?></p>

            <p><strong>Stock disponible: </strong><?= $producto['Stock'] ?></p>

            <a href="../carrito/agregar.php?id=<?= $producto['ID_Producto'] ?>" 
               class="btn btn-lg mt-3" style="background-color: #5b0285ff; color: #ffffff; border-color: #2b003f;">
                <i class="fa-solid fa-cart-plus"></i> Agregar al carrito
            </a>
        </div>

    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>
