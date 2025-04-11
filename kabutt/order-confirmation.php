<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Verificar si hay un ID de pedido
if (!isset($_GET['id'])) {
    header("Location: /");
    exit();
}

// Iniciar sesión y verificar autenticación
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /user/login.php");
    exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Obtener información del pedido
$stmt = $db->prepare("SELECT o.*, u.name, u.email 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar que el pedido existe y pertenece al usuario
if (!$order) {
    header("Location: /");
    exit();
}

// Obtener items del pedido
$stmt = $db->prepare("SELECT oi.*, p.name, p.image 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular subtotal
$subtotal = array_reduce($order_items, function($carry, $item) {
    return $carry + ($item['price'] * $item['quantity']);
}, 0);

$page_title = 'Confirmación de Pedido #' . $order_id . ' - ' . SITE_NAME;
require_once 'includes/header.php';
?>

    <section class="confirmation-section">
        <div class="confirmation-container">
            <div class="confirmation-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <h1>¡Gracias por tu pedido!</h1>
                <p class="order-number">Número de pedido: #<?php echo $order_id; ?></p>
                <p>Hemos enviado un correo de confirmación a <strong><?php echo $order['email']; ?></strong></p>
            </div>

            <div class="confirmation-details">
                <div class="order-summary">
                    <h2>Resumen del Pedido</h2>

                    <div class="order-items">
                        <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                </div>
                                <div class="item-details">
                                    <h4><?php echo $item['name']; ?></h4>
                                    <p>Talla: <?php echo $item['size']; ?></p>
                                    <p>Cantidad: <?php echo $item['quantity']; ?></p>
                                    <p class="item-price">$<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Envío:</span>
                            <span>$<?php
                                $shipping = $subtotal > 50 ? 0 : 5.99;
                                echo number_format($shipping, 2);
                                ?></span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($order['total'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <div class="shipping-info">
                    <div class="info-box">
                        <h3>Dirección de Envío</h3>
                        <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    </div>

                    <div class="info-box">
                        <h3>Método de Pago</h3>
                        <p><?php
                            $payment_methods = [
                                'credit_card' => 'Tarjeta de Crédito',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Transferencia Bancaria'
                            ];
                            echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                            ?></p>
                    </div>

                    <div class="info-box">
                        <h3>Estado del Pedido</h3>
                        <p class="order-status"><?php echo ucfirst($order['status']); ?></p>
                    </div>
                </div>
            </div>

            <div class="confirmation-actions">
                <a href="products/category.php?cat=colección" class="btn btn-continue">Seguir Comprando</a>
                <a href="user/account.php?tab=orders" class="btn btn-orders">Ver Mis Pedidos</a>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>