<?php
if(session_status() === PHP_SESSION_NONE) session_start();
// Calcular total de productos en el carrito
$carrito_total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $carrito_total += $cantidad;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estudio Creativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/AXOLOTE/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #20032eff !important;">
  <div class="container d-flex align-items-center">

    <!-- Mostrar hamburguesa solo si hay sesión -->
    <?php if(isset($_SESSION['usuario_nombre'])): ?>
      <button class="btn btn-outline-light me-3" 
              type="button" 
              data-bs-toggle="offcanvas" 
              data-bs-target="#menuLateral">
          <span class="navbar-toggler-icon"></span>
      </button>
    <?php endif; ?>

    <!-- LOGO + NOMBRE -->
    <a class="navbar-brand d-flex align-items-center" href="/AXOLOTE/modules/catalogo/catalogo.php">
      <img src="/AXOLOTE/public/img/AXOLOTE.jpg" 
           alt="Logo Estudio Creativo" 
           class="rounded-circle me-2" 
           style="width:40px; height:40px; object-fit:cover;">
      AXOLOTE Estudio Creativo
    </a>

    <!-- Contenedor único para botones perfil y carrito -->
    <?php if(isset($_SESSION['usuario_nombre'])): ?>
    <div class="ms-auto d-flex align-items-center gap-2">
      <button class="btn btn-outline-light btn-sm" 
              data-bs-toggle="modal" 
              data-bs-target="#modalUsuario">
        <i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nombre']); ?>
      </button>

      <a href="/AXOLOTE/modules/carrito/carrito.php" class="btn btn-outline-light btn-sm position-relative">
          <i class="fa-solid fa-cart-shopping me-2"></i> Carrito
          <!-- Badge del contador -->
          <?php if ($carrito_total > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  <?= $carrito_total ?>
              </span>
          <?php endif; ?>
      </a>
    </div>
    <?php endif; ?>

  </div>
</nav>


<!-- Menu Lateral -->
<div class="offcanvas offcanvas-start offcanvas-premium" tabindex="-1" id="menuLateral">
  <div class="offcanvas-header border-bottom border-secondary">
    <h5 class="offcanvas-title fw-bold">AXOLOTE</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>

<div class="offcanvas-body">

    <?php if(isset($_SESSION['usuario_rol'])): ?>
        <?php if($_SESSION['usuario_rol'] === 'cliente'): ?>
            <p class="section-title">General</p>
            <a href="/AXOLOTE/index.php" class="menu-item">
              <i class="fa-solid fa-house"></i> <span>Inicio</span>
            </a>

            <p class="section-title mt-3">Sistema</p>
            <a href="/AXOLOTE/modules/catalogo/catalogo.php" class="menu-item">
              <i class="fa-solid fa-shopping-bag"></i> <span>Catálogo</span>
            </a>
        <?php elseif($_SESSION['usuario_rol'] === 'admin'): ?>
            <p class="section-title">General</p>
            <a href="/AXOLOTE/index.php" class="menu-item">
              <i class="fa-solid fa-house"></i> <span>Inicio</span>
            </a>

            <p class="section-title mt-3">Administración</p>
            <a href="/AXOLOTE/modules/categorias/listar_categoria.php" class="menu-item">
              <i class="fa-solid fa-tags"></i> <span>Categorías</span>
            </a>
            <a href="/AXOLOTE/modules/productos/listar_productos.php" class="menu-item">
              <i class="fa-solid fa-box"></i> <span>Productos</span>
            </a>
            <a href="/AXOLOTE/modules/ventas/listar_venta.php" class="menu-item">
              <i class="fa-solid fa-chart-line"></i> <span>Ventas</span>
            </a>
            <a href="/AXOLOTE/modules/usuarios/listar_usuarios.php" class="menu-item">
              <i class="fa-solid fa-users"></i> <span>Clientes</span>
            </a>

            <p class="section-title mt-3">Sistema</p>
            <a href="/AXOLOTE/modules/catalogo/catalogo.php" class="menu-item">
              <i class="fa-solid fa-shopping-bag"></i> <span>Catálogo</span>
            </a>
        <?php endif; ?>

        <!-- Botón de cerrar sesión para todos los roles -->
        <hr>
        <a href="/../AXOLOTE/modules/auth/logout.php" class="btn btn-danger w-100 mt-2">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
        </a>
    <?php endif; ?>

</div>

</div>

<!-- Llamada al modal externo -->
<?php
if (isset($_SESSION['usuario_id'])) {
    include_once __DIR__ . '/../modules/usuarios/modal_usuario.php';
}
?>

