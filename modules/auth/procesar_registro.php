<?php include_once('../../includes/header.php'); ?>
<?php
require_once('../../config/db.php');

// Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido");
}

// Obtener y limpiar datos
$usuario = trim($_POST['usuario'] ?? '');
$correo  = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';
$rol = 'cliente';

// Validar campos obligatorios
if (!$usuario || !$correo || !$password) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Todos los campos son obligatorios',
        confirmButtonColor: '#d33'
      }).then(() => window.location.href='registro.php');
    </script>";
    exit;
}

// Hashear contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Función para mostrar alertas
function alerta($icon, $title, $text, $redirect) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      Swal.fire({
        icon: '$icon',
        title: '$title',
        text: '$text',
        confirmButtonColor: '".($icon === 'success' ? '#3085d6' : '#d33')."'
      }).then(() => window.location.href='$redirect');
    </script>";
    exit;
}

try {
    // Verificar si ya existe el correo
    $stmt = $conn->prepare("SELECT 1 FROM Usuarios WHERE Correo = :correo");
    $stmt->execute([':correo' => $correo]);

    if ($stmt->fetchColumn()) {
        alerta('error', 'Error', 'El correo ya está registrado', 'registro.php');
    }

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO Usuarios (Usuario, Correo, Password, Rol)
                            VALUES (:usuario, :correo, :password, :rol)");
    $stmt->execute([
        ':usuario' => $usuario,
        ':correo'  => $correo,
        ':password'=> $passwordHash,
        ':rol'     => $rol
    ]);

    alerta('success', 'Registro exitoso', 'Usuario creado correctamente', 'login.php');

} catch (PDOException $e) {
    alerta('error', 'Error', 'No se pudo registrar el usuario', 'registro.php');
}
?>
<?php include_once('../../includes/footer.php'); ?>
