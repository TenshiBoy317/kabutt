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


// Resto del código del carrito...
$page_title = 'Carrito de Compras - ' . SITE_NAME;

// Procesar añadir al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $size = sanitizeInput($_POST['size']);

    // Obtener información del producto
    $product = getProductById($db, $product_id);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $item_key = $product_id . '-' . $size;

        if (isset($_SESSION['cart'][$item_key])) {
            $_SESSION['cart'][$item_key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$item_key] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'size' => $size,
                'quantity' => $quantity
            ];
        }
    }
}

// Procesar eliminar del carrito
if (isset($_GET['remove'])) {
    $item_key = sanitizeInput($_GET['remove']);
    if (isset($_SESSION['cart'][$item_key])) {
        unset($_SESSION['cart'][$item_key]);
    }
}

require_once 'includes/header.php';
?>

    <section class="cart-section">
        <h1>Carrito de Compras</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Tu carrito está vacío</p>
                <a href="<?php echo SITE_URL; ?>/products/category.php?cat=colección" class="btn">Ver productos</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <table>
                    <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Talla</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $key => $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td class="product-info">
                                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <span><?php echo $item['name']; ?></span>
                            </td>
                            <td><?php echo $item['size']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <a href="?remove=<?php echo $key; ?>" class="remove-item">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4" class="total-label">Total:</td>
                        <td colspan="2" class="total-amount">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="cart-actions">
                <a href="<?php echo SITE_URL; ?>/products/category.php?cat=colección" class="btn continue-shopping">Seguir Comprando</a>
                <a href="checkout.php" class="btn checkout">Proceder al Pago</a>
            </div>
        <?php endif; ?>
    </section>

<?php require_once 'includes/footer.php'; ?>