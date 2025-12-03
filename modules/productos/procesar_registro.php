<?php
include_once('../../includes/header.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  alerta('error', 'Acceso denegado', 'Método no permitido', 'listar_productos.php');
}

$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$categoria = $_POST['categoria'] ?? null;

// Validación básica
if (!$producto || $precio <= 0 || $stock < 0) {
  alerta('error', 'Datos inválidos', 'Por favor completa todos los campos obligatorios.', 'registrar_producto.php');
}

// Manejo de imagen
$imagenRuta = null;
if (!empty($_FILES['imagen']['name'])) {
  $carpetaDestino = '../../public/img/';
  if (!is_dir($carpetaDestino)) mkdir($carpetaDestino, 0777, true);
  
  $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
  $rutaArchivo = $carpetaDestino . $nombreArchivo;

  if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
    $imagenRuta = 'public/img/' . $nombreArchivo;
  }
}

try {
  $sql = "INSERT INTO Productos (Producto, Descripcion, Precio, Stock, ID_Categoria, Imagen)
          VALUES (:producto, :descripcion, :precio, :stock, :categoria, :imagen)";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':producto' => $producto,
    ':descripcion' => $descripcion,
    ':precio' => $precio,
    ':stock' => $stock,
    ':categoria' => $categoria ?: null,
    ':imagen' => $imagenRuta
  ]);

  alerta('success', 'Producto agregado', 'El producto se registró correctamente.', 'listar_productos.php');

} catch (PDOException $e) {
  alerta('error', 'Error', 'Hubo un problema al registrar el producto.', 'registrar_producto.php');
}
?>
<?php include_once('../../includes/footer.php'); ?>
