<?php
session_start();
require_once('../../config/db.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Verificar que el carrito tenga productos
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "El carrito está vacío.";
    exit;
}

try {
    $conn->beginTransaction();

    $total = 0;
    $detalleProductos = [];

    // Traer info de los productos y calcular total
    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        $stmt = $conn->prepare("SELECT ID_Producto, Producto, Precio, Stock FROM Productos WHERE ID_Producto = :id");
        $stmt->execute([':id' => $producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            if ($producto['Stock'] < $cantidad) {
                throw new Exception("Stock insuficiente para el producto: " . $producto['Producto']);
            }
            $producto['cantidad'] = $cantidad;
            $detalleProductos[] = $producto;
            $total += $producto['Precio'] * $cantidad;
        }
    }

    // Insertar la venta
    $stmt = $conn->prepare("INSERT INTO Ventas (ID_Usuario, Fecha_Venta, Total) VALUES (:usuario, NOW(), :total)");
    $stmt->execute([
        ':usuario' => $_SESSION['usuario_id'],
        ':total' => $total
    ]);
    $venta_id = $conn->lastInsertId();

    // Preparar inserción de detalle y actualización de stock
    $stmtDetalle = $conn->prepare("
        INSERT INTO Detalle_Venta (ID_Venta, ID_Producto, Cantidad, Precio_Unitario) 
        VALUES (:venta, :producto, :cantidad, :precio)
    ");

    $stmtStock = $conn->prepare("
        UPDATE Productos SET Stock = Stock - :cantidad 
        WHERE ID_Producto = :producto AND Stock >= :cantidad
    ");

    foreach ($detalleProductos as $p) {
        // Insertar detalle de la venta
        $stmtDetalle->execute([
            ':venta' => $venta_id,
            ':producto' => $p['ID_Producto'],
            ':cantidad' => $p['cantidad'],
            ':precio' => $p['Precio']
        ]);

        // Disminuir stock
        $stmtStock->execute([
            ':cantidad' => $p['cantidad'],
            ':producto' => $p['ID_Producto']
        ]);

        // Verificar que la actualización de stock se haya realizado
        if ($stmtStock->rowCount() === 0) {
            throw new Exception("Stock insuficiente para el producto: " . $p['Producto']);
        }
    }

    // Confirmar transacción
    $conn->commit();

    // Limpiar carrito
    unset($_SESSION['carrito']);

    // Redirigir a la página de venta exitosa
    header("Location: ../carrito/venta_exitosa.php?id=" . $venta_id);
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "<div class='alert alert-danger'>Error al procesar la venta: " . htmlspecialchars($e->getMessage()) . "</div>";
}
