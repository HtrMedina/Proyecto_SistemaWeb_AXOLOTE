<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Solo usuarios logueados pueden comprar (opcional)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');
include_once('../../includes/header.php');
?>

<?php if (isset($_GET['agregado'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
  <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="fa-solid fa-cart-plus text-success me-2"></i>
      <strong class="me-auto">Carrito</strong>
      <small>Justo ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
      Producto agregado correctamente al carrito
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toastElement = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastElement, { delay: 2500 });
    toast.show();
});
</script>
<?php endif; ?>

<?php
// ---------------------------
// Filtros
// ---------------------------
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

// ---------------------------
// Traer categorías
// ---------------------------
$catStmt = $conn->query("SELECT * FROM Categoria ORDER BY Categoria ASC");
$categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Construcción dinámica del filtro SQL
// ---------------------------
$sql = "SELECT p.*, c.Categoria AS NombreCategoria
        FROM Productos p
        LEFT JOIN Categoria c ON p.ID_Categoria = c.ID_Categoria
        WHERE p.Stock > 0"; // Excluir productos sin stock

$params = [];

if ($busqueda !== "") {
    $sql .= " AND p.Producto LIKE :buscar";
    $params[':buscar'] = "%$busqueda%";
}

if ($categoria > 0) {
    $sql .= " AND p.ID_Categoria = :categoria";
    $params[':categoria'] = $categoria;
}

$sql .= " ORDER BY p.Fecha_Creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ==================== FILTROS ==================== -->
<div class="container mt-4">

    <h2 class="mb-4"><i class="fa-solid fa-store"></i> Catálogo de Productos</h2>

    <form class="row mb-4" method="GET">

        <!-- Buscador -->
        <div class="col-md-6 mb-2">
            <input type="text" name="buscar" class="form-control"
                   placeholder="Buscar producto..."
                   value="<?= htmlspecialchars($busqueda) ?>">
        </div>

        <!-- Categorías -->
        <div class="col-md-4 mb-2">
            <select name="categoria" class="form-select">
                <option value="0">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['ID_Categoria'] ?>"
                        <?= $categoria == $cat['ID_Categoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botón -->
        <div class="col-md-2 mb-2">
            <button class="btn btn-primary w-100">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </div>

    </form>

    <hr>

    <!-- ==================== PRODUCTOS ==================== -->
    <div class="row g-4">

        <?php if (count($productos) === 0): ?>
            <div class="text-center text-muted">
                <i class="fa-solid fa-circle-exclamation fa-2x mb-2"></i>
                <p>No se encontraron productos.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($productos as $p): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                <div class="card shadow-sm h-100 card-click"
                     onclick="window.location='producto_detalle.php?id=<?= $p['ID_Producto'] ?>'">

                    <!-- Imagen -->
                    <?php if ($p['Imagen']): ?>
                        <img src="/AXOLOTE/<?= htmlspecialchars($p['Imagen']) ?>" 
                             class="card-img-top" style="height:180px; object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white text-center py-5">
                            Sin imagen
                        </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">

                        <h5 class="card-title"><?= htmlspecialchars($p['Producto']) ?></h5>

                        <p class="text-muted mb-1"><?= $p['NombreCategoria'] ?? 'Sin categoría' ?></p>

                        <h4 class="text-primary" style="font-weight: bold;">$<?= number_format($p['Precio'],2) ?></h4>

                        <p class="small"><?= htmlspecialchars(substr($p['Descripcion'],0,60)) ?>...</p>

                        <?php if ($p['Stock'] < 15): ?>
                            <span class="badge bg-info text-black mb-2">Últimas disponibles</span>
                        <?php endif; ?>

                        <div class="mt-auto">

                            <!-- Botón que sí funciona -->
                            <a href="../carrito/agregar.php?id=<?= $p['ID_Producto'] ?>" 
                                class="btn w-100 position-relative z-3"
                                style="background-color: #680596ff; color: #ffffff; border-color: none;"
                                onclick="event.stopPropagation();">
                                <i class="fa-solid fa-cart-plus"></i> Agregar al carrito
                            </a>

                        </div>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

</div>

<style>
.card-click {
    display: block;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.card-click:hover {
    transform: scale(1.04);
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
}

.card-click img {
    transition: transform 0.4s ease;
}

.card-click:hover img {
    transform: scale(1.12);
}
</style>

<?php include_once('../../includes/footer.php'); ?>
