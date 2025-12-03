<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once('../../config/db.php');
include_once('../../includes/header.php');

// Consulta para traer solo usuarios con rol 'cliente'
$stmt = $conn->prepare("SELECT ID_Usuario, Usuario, Correo, Fecha_Registro FROM Usuarios WHERE Rol = 'cliente' ORDER BY Fecha_Registro DESC");
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fa-solid fa-users"></i> Lista de Clientes</h3>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['Usuario']) ?></td>
                        <td><?= htmlspecialchars($cliente['Correo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($cliente['Fecha_Registro'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay clientes registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once('../../includes/footer.php'); ?>
