<?php include_once('../../includes/header.php'); ?>
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-header text-white text-center" style="background-color: #2b003f;">
          <img src="../../public/img/AXOLOTE.jpg" alt="Logo" 
          class="rounded-circle mb-2 mt-2" 
          style="width: 80px; height: 80px; object-fit: cover;">
          <h4>Iniciar Sesión</h4>
        </div>
        <div class="card-body">
          <form action="procesar_login.php" method="POST">
            <div class="mb-3">
              <label>Correo electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Contraseña</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-center mb-3">
                <button type="submit" class="btn w-50" style="background-color: #5d2983ff; color: #ffffff; border: none;">
                    Ingresar
                </button>
            </div>
          </form>
          <div class="text-center mt-3">
            <small>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('../../includes/footer.php'); ?>
