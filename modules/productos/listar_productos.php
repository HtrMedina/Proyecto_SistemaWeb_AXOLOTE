<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');
include_once('../../includes/header.php');

// Traer productos
$stmt = $conn->query("SELECT p.*, c.Categoria AS NombreCategoria 
                      FROM Productos p 
                      LEFT JOIN Categoria c ON p.ID_Categoria = c.ID_Categoria
                      ORDER BY p.Fecha_Creacion ASC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traer categorías
$catStmt = $conn->query("SELECT * FROM Categoria ORDER BY Categoria ASC");
$categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fa-solid fa-box me-2"></i> Lista de Productos</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Producto
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['Producto']) ?></td>
                            <td><?= htmlspecialchars($p['NombreCategoria'] ?? 'Sin categoría') ?></td>
                            <td>$<?= number_format($p['Precio'], 2) ?></td>
                            <td><?= $p['Stock'] ?></td>
                            <td>
                                <?php if ($p['Imagen']): ?>
                                    <img src="../../<?= htmlspecialchars($p['Imagen']) ?>" width="60" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $p['ID_Producto'] ?>">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $p['ID_Producto'] ?>">
                                    <i class="fa-solid fa-trash me-1"></i> Eliminar
                                </button>
                                <button class="btn btn-sm btn-success btn-stock" data-id="<?= $p['ID_Producto'] ?>">
                                    <i class="fa-solid fa-plus me-1"></i> Agregar Stock
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo Producto -->
<div class="modal fade" id="modalNuevoProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProducto" action="procesar_registro.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="producto" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="">-- Selecciona una categoría --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['ID_Categoria'] ?>"><?= htmlspecialchars($cat['Categoria']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoEditar">
                <!-- Se cargará el formulario vía AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Stock -->
<div class="modal fade" id="modalAgregarStock" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoStock">
                <!-- Se cargará el formulario vía AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
//Editar
document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        fetch('editar_producto.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenidoEditar').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
            });
    });
});

//Eliminar
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        Swal.fire({
            title: '¿Seguro que quieres eliminar este producto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = 'eliminar_producto.php?id=' + id;
            }
        });
    });
});

// Agregar Stock
document.querySelectorAll('.btn-stock').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        fetch('agregar_stock.php?id=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenidoStock').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalAgregarStock')).show();
            });
    });
});
</script>

<?php include_once('../../includes/footer.php'); ?>
