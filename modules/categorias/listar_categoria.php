<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../auth/login.php");
  exit;
}
require_once('../../config/db.php');
include_once('../../includes/header.php');

$stmt = $conn->query("SELECT * FROM Categoria ORDER BY ID_Categoria ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fa-solid fa-tags me-2"></i> Lista de Categorías</h3>
    <!-- Botón que abre el modal -->
    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalCategoria">
      <i class="fa-solid fa-plus me-1"></i> Nueva Categoría
    </button>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Categoría</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias as $cat): ?>
          <tr>
            <td><?= htmlspecialchars($cat['Categoria']) ?></td>
            <td><?= htmlspecialchars($cat['Descripcion'] ?? '—') ?></td>
            <td>
              <button class="btn btn-sm btn-warning btnEditar"
                      data-id="<?= $cat['ID_Categoria'] ?>"
                      data-categoria="<?= htmlspecialchars($cat['Categoria']) ?>"
                      data-descripcion="<?= htmlspecialchars($cat['Descripcion'] ?? '') ?>">
                <i class="fa-solid fa-pen-to-square me-1"></i> Editar
              </button>
              <button class="btn btn-sm btn-danger btnEliminar"
                      data-id="<?= $cat['ID_Categoria'] ?>">
                <i class="fa-solid fa-trash me-1"></i> Eliminar
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<!-- Modal para registrar categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalCategoriaLabel">Registrar Nueva Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCategoria">
          <div class="mb-3">
            <label class="form-label">Nombre de la Categoría</label>
            <input type="text" name="categoria" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
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

<!-- Modal para editar categoría -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="modalEditarCategoriaLabel">Editar Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarCategoria">
          <input type="hidden" name="id_categoria" id="edit_id">
          <div class="mb-3">
            <label class="form-label">Nombre de la Categoría</label>
            <input type="text" name="categoria" id="edit_categoria" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" id="edit_descripcion" class="form-control"></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<?php include_once('../../includes/footer.php'); ?>

<!-- SweetAlert + AJAX -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formCategoria').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  try {
    const res = await fetch('procesar_categoria.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: '¡Categoría agregada!',
        text: data.message
      }).then(() => location.reload());
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.message });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error inesperado', text: err.message });
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

  // 🟡 Abrir modal de edición y llenar datos
  document.querySelectorAll('.btnEditar').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit_id').value = btn.dataset.id;
      document.getElementById('edit_categoria').value = btn.dataset.categoria;
      document.getElementById('edit_descripcion').value = btn.dataset.descripcion;
      new bootstrap.Modal(document.getElementById('modalEditarCategoria')).show();
    });
  });

  // 🟢 Guardar cambios de edición (AJAX)
  document.getElementById('formEditarCategoria').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);

    const res = await fetch('procesar_categoria.php?action=editar', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.status === 'success') {
      Swal.fire('¡Actualizado!', data.message, 'success').then(() => location.reload());
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  });

  // 🔴 Eliminar categoría
  document.querySelectorAll('.btnEliminar').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;

      const confirm = await Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (confirm.isConfirmed) {
        const res = await fetch('procesar_categoria.php?action=eliminar', {
          method: 'POST',
          body: new URLSearchParams({ id })
        });
        const data = await res.json();
        if (data.status === 'success') {
          Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      }
    });
  });

});
</script>
