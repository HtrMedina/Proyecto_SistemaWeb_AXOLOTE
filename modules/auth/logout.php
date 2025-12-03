<?php
if(session_status() === PHP_SESSION_NONE) session_start();
session_unset();
session_destroy();
?>

<?php include_once('../../includes/header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  Swal.fire({
    icon: 'info',
    title: 'Sesión cerrada',
    text: 'Has salido del sistema correctamente',
    confirmButtonColor: '#3085d6'
  }).then(() => window.location = 'login.php');
</script>

<?php include_once('../../includes/footer.php'); ?>
