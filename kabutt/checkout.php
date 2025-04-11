<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Guardar la página actual para redirección después del login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ".SITE_URL."/user/login.php");
    exit();
}


// Verificar que el carrito no esté vacío
if (empty($_SESSION['cart'])) {
    header("Location: /cart.php");
    exit();
}

// Obtener información del usuario
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar el formulario de checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    $billing_address = sanitizeInput($_POST['billing_address']);
    $payment_method = sanitizeInput($_POST['payment_method']);
    $use_same_address = isset($_POST['use_same_address']) ? 1 : 0;

    // Si el usuario marcó "usar misma dirección"
    if ($use_same_address) {
        $billing_address = $shipping_address;
    }

    // Calcular total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Crear orden en la base de datos
    try {
        $db->beginTransaction();

        // Insertar orden
        $stmt = $db->prepare("INSERT INTO orders (user_id, shipping_address, billing_address, payment_method, total, status) 
                             VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $shipping_address, $billing_address, $payment_method, $total]);
        $order_id = $db->lastInsertId();

        // Insertar items de la orden
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) 
                             VALUES (?, ?, ?, ?, ?)");

        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['size']
            ]);
        }

        $db->commit();

        // Vaciar carrito
        unset($_SESSION['cart']);

        // Redirigir a página de confirmación
        header("Location: order-confirmation.php?id=$order_id");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        $error = "Error al procesar tu orden. Por favor intenta nuevamente.";
    }
}

$page_title = 'Finalizar Compra - ' . SITE_NAME;
require_once 'includes/header.php';
?>

    <section class="checkout-section">
        <div class="checkout-container">
            <h1>Finalizar Compra</h1>

            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="checkout-grid">
                <div class="checkout-form">
                    <form method="post" id="checkoutForm">
                        <h2>Información de Envío</h2>
                        <div class="form-group">
                            <label for="shipping_address">Dirección de Envío:</label>
                            <textarea id="shipping_address" name="shipping_address" required><?php echo $user['address'] ?? ''; ?></textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="use_same_address" name="use_same_address" checked>
                            <label for="use_same_address">Usar la misma dirección para facturación</label>
                        </div>

                        <div id="billingAddressSection" style="display: none;">
                            <h2>Información de Facturación</h2>
                            <div class="form-group">
                                <label for="billing_address">Dirección de Facturación:</label>
                                <textarea id="billing_address" name="billing_address"></textarea>
                            </div>
                        </div>

                        <h2>Método de Pago</h2>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="credit_card" name="payment_method" value="credit_card" checked required>
                                <label for="credit_card">Tarjeta de Crédito</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                <label for="paypal">PayPal</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                                <label for="bank_transfer">Transferencia Bancaria</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-checkout">Realizar Pedido</button>
                    </form>
                </div>

                <div class="order-summary">
                    <h2>Resumen del Pedido</h2>
                    <div class="order-items">
                        <?php foreach ($_SESSION['cart'] as $key => $item): ?>
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
                                $subtotal = array_reduce($_SESSION['cart'], function($carry, $item) {
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
                            <span>$<?php echo number_format($subtotal + $shipping, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Mostrar/ocultar dirección de facturación
        document.getElementById('use_same_address').addEventListener('change', function() {
            document.getElementById('billingAddressSection').style.display = this.checked ? 'none' : 'block';
        });
    </script>

<?php require_once 'includes/footer.php'; ?>