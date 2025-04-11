<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Verificar si hay un ID de pedido
if (!isset($_GET['id'])) {
    header("Location: /user/account.php?tab=orders");
    exit();
}


// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Guardar la página actual para redirección después del login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ".SITE_URL."/user/login.php");
    exit();
}

// Resto del código...

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
    header("Location: /user/account.php?tab=orders");
    exit();
}

// Obtener items del pedido
$stmt = $db->prepare("SELECT oi.*, p.name, p.image 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Detalles del Pedido #' . $order_id . ' - ' . SITE_NAME;
require_once '../includes/header.php';
?>

    <section class="order-details-section">
        <div class="container">
            <div class="order-header">
                <h1>Detalles del Pedido #<?php echo $order_id; ?></h1>
                <a href="/user/account.php?tab=orders" class="btn-back">← Volver a mis pedidos</a>
            </div>

            <div class="order-details-grid">
                <div class="order-summary">
                    <h2>Resumen del Pedido</h2>

                    <div class="order-info">
                        <div class="info-row">
                            <span>Fecha del Pedido:</span>
                            <span><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="info-row">
                            <span>Estado:</span>
                            <span class="order-status <?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                        </div>
                        <div class="info-row">
                            <span>Método de Pago:</span>
                            <span>
                            <?php
                            $payment_methods = [
                                'credit_card' => 'Tarjeta de Crédito',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Transferencia Bancaria'
                            ];
                            echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </span>
                        </div>
                    </div>

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
                            <span>$<?php
                                $subtotal = array_reduce($order_items, function($carry, $item) {
                                    return $carry + ($item['price'] * $item['quantity']);
                                }, 0);
                                echo number_format($subtotal, 2);
                                ?></span>
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
                        <h3>Dirección de Facturación</h3>
                        <p><?php echo nl2br(htmlspecialchars($order['billing_address'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>