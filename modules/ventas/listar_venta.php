<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');
include_once('../../includes/header.php');

// Parámetros de búsqueda y paginación
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 15;
$offset = ($pagina - 1) * $por_pagina;

// Preparar la consulta base con filtro
$where = '';
$params = [];
if ($buscar !== '') {
    $where = "WHERE v.ID_Venta LIKE :buscar OR u.Usuario LIKE :buscar";
    $params[':buscar'] = "%$buscar%";
}

// Contar total de ventas para paginación
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM Ventas v JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario $where");
$stmtCount->execute($params);
$total_registros = $stmtCount->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

// Traer ventas con límite y filtro
$stmt = $conn->prepare("
    SELECT v.ID_Venta, v.Fecha_Venta, v.Total, u.Usuario
    FROM Ventas v
    JOIN Usuarios u ON v.ID_Usuario = u.ID_Usuario
    $where
    ORDER BY v.Fecha_Venta DESC
    LIMIT :offset, :por_pagina
");
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val, PDO::PARAM_STR);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fa-solid fa-chart-line"></i> Lista de Ventas</h3>
        <form class="d-flex" method="get" action="">
            <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por ID o cliente" value="<?= htmlspecialchars($buscar) ?>">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        </form>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Fecha de Venta</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><?= $v['ID_Venta'] ?></td>
                            <td><?= htmlspecialchars($v['Usuario']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($v['Fecha_Venta'])) ?></td>
                            <td>$<?= number_format($v['Total'], 2) ?></td>
                            <td>
                                <a href="detalle_venta.php?id=<?= $v['ID_Venta'] ?>" class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay ventas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>
