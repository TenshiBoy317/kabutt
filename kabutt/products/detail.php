<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: /index.php');
    exit;
}

$product_id = (int)$_GET['id'];
$product = getProductById($db, $product_id);

if (!$product) {
    header('Location: /index.php');
    exit;
}

$page_title = $product['name'] . ' - ' . SITE_NAME;

require_once '../includes/header.php';
?>

    <section class="product-detail">
        <div class="product-images">
            <div class="main-image">
                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
        </div>

        <div class="product-info">
            <h1><?php echo $product['name']; ?></h1>
            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>

            <div class="product-description">
                <p><?php echo $product['description']; ?></p>
            </div>

            <form action="../cart.php " method="post" class="add-to-cart">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                <div class="size-selector">
                    <label for="size">Talla:</label>
                    <select name="size" id="size" required>
                        <option value="">Selecciona talla</option>
                        <option value="36">36</option>
                        <option value="37">37</option>
                        <option value="38">38</option>
                        <option value="39">39</option>
                        <option value="40">40</option>
                        <option value="41">41</option>
                        <option value="42">42</option>
                        <option value="43">43</option>
                        <option value="44">44</option>
                    </select>
                </div>

                <div class="quantity-selector">
                    <label for="quantity">Cantidad:</label>
                    <input type="number" name="quantity" id="quantity" min="1" value="1" required>
                </div>

                <button type="submit" class="btn">Añadir al carrito</button>
            </form>
        </div>
    </section>

    <section class="related-products">
        <h2>Productos Relacionados</h2>
        <div class="products-grid">
            <?php
            $related = getProductsByCategory($db, $product['category'], 4);
            foreach ($related as $rel_product):
                if ($rel_product['id'] != $product['id']):
                    ?>
                    <div class="product-card">
                        <a href="/products/detail.php?id=<?php echo $rel_product['id']; ?>">
                            <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $rel_product['image']; ?>" alt="<?php echo $rel_product['name']; ?>">
                            <h3><?php echo $rel_product['name']; ?></h3>
                            <p class="price">$<?php echo number_format($rel_product['price'], 2); ?></p>
                        </a>
                    </div>
                <?php
                endif;
            endforeach;
            ?>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>