<?php include_once('../../includes/header.php'); ?>
<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

// Función para mostrar alertas y redirigir
function alerta($icon, $title, $text, $url) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      Swal.fire({
        icon: '$icon',
        title: '$title',
        text: '$text',
        confirmButtonColor: '" . ($icon === 'success' ? '#3085d6' : '#d33') . "'
      }).then(() => window.location.href='$url');
    </script>";
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido");
}

// Obtener datos POST de forma segura
$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

if (!$correo || !$password) {
    alerta('error', 'Error', 'Correo o contraseña vacíos', 'login.php');
}

try {
    // Buscar usuario
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE Correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        alerta('error', 'Error', 'El correo no está registrado', 'login.php');
    }

    // Verificar contraseña
    if (!password_verify($password, $usuario['Password'])) {
        alerta('error', 'Error', 'Contraseña incorrecta', 'login.php');
    }

    // Guardar datos de sesión
    $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
    $_SESSION['usuario_nombre'] = $usuario['Usuario'];
    $_SESSION['usuario_correo'] = $usuario['Correo'];
    $_SESSION['usuario_rol'] = $usuario['Rol'];

    // Alerta de éxito
    alerta('success', 'Bienvenido', 'Has iniciado sesión correctamente', '../../index.php');

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
<?php include_once('../../includes/footer.php'); ?>
