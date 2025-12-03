<?php
// modal_usuario.php
// Asegurarse de que haya sesión activa
if(session_status() === PHP_SESSION_NONE) session_start();

// Si no hay usuario logeado, no mostrar modal
if(!isset($_SESSION['usuario_id'])) return;
?>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalUsuarioLabel">
          <i class="fa-solid fa-user-circle me-2"></i> Datos del Usuario
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <div class="text-center mb-3">
          <!-- Foto de usuario, si tienes ruta en sesión -->
          <?php if(isset($_SESSION['usuario_foto']) && !empty($_SESSION['usuario_foto'])): ?>
            <img src="<?= htmlspecialchars($_SESSION['usuario_foto']); ?>" class="rounded-circle" style="width:80px; height:80px; object-fit:cover;" alt="Foto Usuario">
          <?php else: ?>
            <i class="fa-solid fa-circle-user fa-5x text-secondary"></i>
          <?php endif; ?>
        </div>

        <ul class="list-group">
          <li class="list-group-item"><strong>Nombre:</strong> <?= htmlspecialchars($_SESSION['usuario_nombre']); ?></li>
          <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['usuario_correo'] ?? 'No registrado'); ?></li>
          <?php if(isset($_SESSION['usuario_rol'])): ?>
          <li class="list-group-item"><strong>Rol:</strong> <?= htmlspecialchars($_SESSION['usuario_rol']); ?></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fa-solid fa-xmark me-1"></i> Cerrar
        </button>
      </div>

    </div>
  </div>
</div>
