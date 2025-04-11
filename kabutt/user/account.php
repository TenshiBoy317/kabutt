<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Guardar la página actual para redirección después del login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ".SITE_URL."/user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_tab = isset($_GET['tab']) ? sanitizeInput($_GET['tab']) : 'profile';

// Obtener información del usuario
$stmt = $db->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener pedidos del usuario (si está en la pestaña de pedidos)
$orders = [];
if ($current_tab === 'orders') {
    $stmt = $db->prepare("SELECT id, created_at, total, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = 'Mi Cuenta - ' . SITE_NAME;
require_once '../includes/header.php';
?>

    <section class="account-section">
        <div class="account-tabs">
            <a href="account.php?tab=profile" class="<?php echo $current_tab === 'profile' ? 'active' : ''; ?>">Perfil</a>
            <a href="account.php?tab=orders" class="<?php echo $current_tab === 'orders' ? 'active' : ''; ?>">Mis Pedidos</a>
        </div>

        <div class="account-content">
            <?php if ($current_tab === 'profile'): ?>
                <div class="account-info">
                    <h2>Información Personal</h2>
                    <p><strong>Nombre:</strong> <?php echo $user['name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                    <a href="edit-profile.php" class="btn">Editar Perfil</a>
                </div>

            <?php elseif ($current_tab === 'orders'): ?>
                <div class="orders-list">
                    <h2>Mis Pedidos</h2>

                    <?php if (empty($orders)): ?>
                        <div class="no-orders">
                            <p>No has realizado ningún pedido aún.</p>
                            <a href="/products/category.php?cat=all" class="btn">Ver Productos</a>
                        </div>
                    <?php else: ?>
                        <table class="orders-table">
                            <thead>
                            <tr>
                                <th>N° Pedido</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    </td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">Ver Detalles</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>