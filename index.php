<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Si no hay sesión, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: modules/auth/login.php");
    exit;
}

include_once('includes/header.php');
?>

<div class="container mt-5">
  <div class="card shadow-sm border-0">
    <div class="card-body text-center p-5">
      <h2 class="mb-3">Bienvenido al Sistema del Estudio Creativo <i class="fa-solid fa-palette"></i></h2>
      <p class="lead">
        Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>.<br>
      </p>

      <hr>

      <div class="mt-4">
          <?php if($_SESSION['usuario_rol'] === 'admin'): ?>
              <a href="modules/categorias/listar_categoria.php" class="btn btn-primary btn-lg me-3">
                  <i class="fa-solid fa-tags me-2"></i> Categorías
              </a>

              <a href="modules/productos/listar_productos.php" class="btn btn-warning btn-lg me-3 text-dark">
                  <i class="fa-solid fa-box me-2"></i> Productos
              </a>

              <a href="modules/ventas/listar_venta.php" class="btn btn-success me-3 btn-lg">
                  <i class="fa-solid fa-chart-line me-2"></i> Ventas
              </a>

              <a href="modules/usuarios/listar_usuarios.php" class="btn btn-info me-3 btn-lg text-dark">
                  <i class="fa-solid fa-users me-2"></i> Usuarios
              </a>
          <?php endif; ?>

          <?php if($_SESSION['usuario_rol'] === 'cliente' || $_SESSION['usuario_rol'] === 'admin'): ?>
              <a href="modules/catalogo/catalogo.php" class="btn btn-dark btn-lg">
                  <i class="fa-solid fa-shopping-bag me-2"></i> Catalogo
              </a>
          <?php endif; ?>
      </div>


    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>
