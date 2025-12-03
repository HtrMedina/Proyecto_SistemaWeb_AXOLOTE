<?php
include_once('../../includes/header.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_productos.php');
    exit;
}

// Obtener datos
$id = intval($_POST['id'] ?? 0);
$producto = trim($_POST['producto'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$categoria = $_POST['categoria'] ?? null;
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$id || !$producto) {
    die("Datos incompletos");
}

// Traer la imagen anterior
$stmt = $conn->prepare("SELECT Imagen FROM Productos WHERE ID_Producto = ?");
$stmt->execute([$id]);
$productoAnterior = $stmt->fetch(PDO::FETCH_ASSOC);
$imagenAnterior = $productoAnterior['Imagen'] ?? null;

// Manejar imagen nueva
$imagenSubida = $imagenAnterior; // por defecto la misma
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $carpeta = '../../uploads/productos/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    $nombreArchivo = time() . '_' . basename($_FILES['imagen']['name']);
    $rutaDestino = $carpeta . $nombreArchivo;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        $imagenSubida = 'uploads/productos/' . $nombreArchivo;

        // Eliminar imagen anterior si existe
        if ($imagenAnterior && file_exists('../../' . $imagenAnterior)) {
            unlink('../../' . $imagenAnterior);
        }
    }
}

// Actualizar en la base de datos
try {
    $stmt = $conn->prepare("UPDATE Productos 
                            SET Producto = ?, Precio = ?, Stock = ?, ID_Categoria = ?, Descripcion = ?, Imagen = ? 
                            WHERE ID_Producto = ?");
    $stmt->execute([$producto, $precio, $stock, $categoria ?: null, $descripcion, $imagenSubida, $id]);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Producto actualizado',
            text: 'Los cambios se han guardado correctamente',
            confirmButtonColor: '#3085d6'
        }).then(() => window.location.href='listar_productos.php');
    </script>";

} catch (PDOException $e) {
    die("Error al actualizar el producto: " . $e->getMessage());
}
?>
<?php include_once('../../includes/footer.php'); ?>
