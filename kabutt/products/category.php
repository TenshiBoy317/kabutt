<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$category = isset($_GET['cat']) ? sanitizeInput($_GET['cat']) : DEFAULT_CATEGORY;
$limit = 12; // Definir el límite de productos a mostrar

// Obtener productos según la categoría
if ($category === 'colección') {
    $stmt = $db->prepare("SELECT * FROM products LIMIT :limit");
} else {
    $stmt = $db->prepare("SELECT * FROM products WHERE category = :category LIMIT :limit");
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener conteo total de productos para la categoría
if ($category === 'colección') {
    $count_stmt = $db->prepare("SELECT COUNT(*) as total FROM products");
} else {
    $count_stmt = $db->prepare("SELECT COUNT(*) as total FROM products WHERE category = :category");
    $count_stmt->bindValue(':category', $category, PDO::PARAM_STR);
}

$count_stmt->execute();
$total_products = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$page_title = ucfirst($category) . ' - ' . SITE_NAME;

require_once '../includes/header.php';
?>

    <div class="products-page">
        <div class="products-container">
            <!-- Barra lateral de filtros -->
            <aside class="filters-sidebar">
                <div class="filter-group">
                    <h3>Filtrar por precio</h3>
                    <div class="price-ranges">
                        <button type="button" class="price-range">€0 - €50</button>
                        <button type="button" class="price-range">€50 - €100</button>
                        <button type="button" class="price-range">€100 - €200</button>
                        <button type="button" class="price-range">€200+</button>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Tallas</h3>
                    <div class="size-grid">
                        <button type="button" class="size-option">35</button>
                        <button type="button" class="size-option">36</button>
                        <button type="button" class="size-option">37</button>
                        <button type="button" class="size-option">38</button>
                        <button type="button" class="size-option">39</button>
                        <button type="button" class="size-option">40</button>
                        <button type="button" class="size-option">41</button>
                        <button type="button" class="size-option">42</button>
                        <button type="button" class="size-option">43</button>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Ofertas especiales</h3>
                    <div class="deals-checkbox">
                        <input type="checkbox" id="deal1">
                        <label for="deal1">Envío gratis</label>
                    </div>
                    <div class="deals-checkbox">
                        <input type="checkbox" id="deal2">
                        <label for="deal2">Descuentos</label>
                    </div>
                </div>
            </aside>

            <!-- Contenido principal -->
            <main class="products-main-content">
                <div class="products-header">
                    <h1><?php echo ucfirst($category); ?> <span>(<?php echo $total_products; ?>)</span></h1>

                    <div class="sort-options">
                        <span>Mostrando <?php echo count($products); ?> productos</span>
                        <select class="sort-select">
                            <option>Ordenar por: Destacados</option>
                            <option>Precio: Menor a mayor</option>
                            <option>Precio: Mayor a menor</option>
                            <option>Novedades</option>
                        </select>
                    </div>
                </div>

                <!-- Grid de productos -->
                <section class="products-grid">
                    <?php if (empty($products)): ?>
                        <div class="no-products">
                            <p>No hay productos disponibles en esta categoría.</p>
                            <a href="<?php echo SITE_URL; ?>" class="btn">Volver al inicio</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <?php if ($product['is_new']): ?>
                                    <div class="product-badge">Nuevo</div>
                                <?php elseif ($product['is_bestseller']): ?>
                                    <div class="product-badge">Más vendido</div>
                                <?php endif; ?>

                                <a href="<?php echo SITE_URL; ?>/products/detail.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                    <div class="product-info">
                                        <span class="product-category"><?php echo $product['category']; ?></span>
                                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                                        <span class="product-colors"><?php echo $product['colors']; ?> colores</span>
                                        <div class="product-price">
                                            <span class="current-price"><?php echo number_format($product['price'], 2); ?> €</span>
                                            <?php if ($product['original_price'] > $product['price']): ?>
                                                <span class="original-price"><?php echo number_format($product['original_price'], 2); ?> €</span>
                                                <?php
                                                $discount = round(($product['original_price'] - $product['price']) / $product['original_price'] * 100);
                                                ?>
                                                <span class="discount">-<?php echo $discount; ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>

<?php require_once '../includes/footer.php'; ?>