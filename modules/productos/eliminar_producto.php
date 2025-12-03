<?php include_once('../../includes/header.php'); ?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../config/db.php');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die("ID inválido");
}

try {
    // 1. Obtener la ruta de la imagen del producto
    $stmt = $conn->prepare("SELECT Imagen FROM Productos WHERE ID_Producto = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto && !empty($producto['Imagen'])) {
        $rutaImagen = '../../' . $producto['Imagen'];
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen); // eliminar archivo físico
        }
    }

    // 2. Borrar el registro de la base de datos
    $stmt = $conn->prepare("DELETE FROM Productos WHERE ID_Producto = ?");
    $stmt->execute([$id]);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Producto eliminado',
            text: 'El producto ha sido eliminado correctamente',
            confirmButtonColor: '#3085d6'
        }).then(() => window.location.href='listar_productos.php');
    </script>";

} catch(PDOException $e) {
    die("Error al eliminar: " . $e->getMessage());
}
?>
<?php include_once('../../includes/footer.php'); ?>
