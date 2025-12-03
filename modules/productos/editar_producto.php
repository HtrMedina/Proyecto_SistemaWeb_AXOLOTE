<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

$id = intval($_GET['id'] ?? 0);
if (!$id) exit('ID inválido');

$stmt = $conn->prepare("SELECT * FROM Productos WHERE ID_Producto = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$producto) exit('Producto no encontrado');

// Traer categorías
$catStmt = $conn->query("SELECT * FROM Categoria ORDER BY Categoria ASC");
$categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="procesar_editar.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $producto['ID_Producto'] ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nombre del Producto</label>
            <input type="text" name="producto" class="form-control" value="<?= htmlspecialchars($producto['Producto']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= $producto['Precio'] ?>" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= $producto['Stock'] ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Categoría</label>
            <select name="categoria" class="form-select">
                <option value="">-- Selecciona una categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['ID_Categoria'] ?>" <?= $producto['ID_Categoria'] == $cat['ID_Categoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control"><?= htmlspecialchars($producto['Descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control" accept="image/*">
        <?php if ($producto['Imagen']): ?>
            <img src="../../<?= htmlspecialchars($producto['Imagen']) ?>" width="80" class="mt-2 img-thumbnail">
        <?php endif; ?>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </div>
</form>
