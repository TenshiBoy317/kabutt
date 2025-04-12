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

<!-- Estructura principal rediseñada -->
<div class="category-container">
    <!-- Sidebar de filtros estilo Nike -->
    <aside class="category-filters">
        <h2 class="filters-title">Filtrar</h2>

        <form id="filters-form" method="GET">
            <input type="hidden" name="cat" value="<?php echo $category; ?>">

            <!-- Filtro de precios -->
            <div class="filter-section">
                <h3 class="filter-header">Precio</h3>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="radio" name="price" value="0-50">
                        <span>€0 - €50</span>
                    </label>
                    <label class="filter-option">
                        <input type="radio" name="price" value="50-100">
                        <span>€50 - €100</span>
                    </label>
                    <label class="filter-option">
                        <input type="radio" name="price" value="100-200">
                        <span>€100 - €200</span>
                    </label>
                    <label class="filter-option">
                        <input type="radio" name="price" value="200+">
                        <span>€200+</span>
                    </label>
                </div>
            </div>

            <!-- Filtro de tallas -->
            <div class="filter-section">
                <h3 class="filter-header">Tallas</h3>
                <div class="size-options">
                    <?php
                    $sizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43'];
                    foreach ($sizes as $size) {
                        echo '<label class="size-option">
                                <input type="checkbox" name="sizes[]" value="'.$size.'">
                                <span>'.$size.'</span>
                              </label>';
                    }
                    ?>
                </div>
            </div>

            <!-- Filtro de ofertas -->
            <div class="filter-section">
                <h3 class="filter-header">Ofertas</h3>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="free_shipping">
                        <span>Envío gratis</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="discounts">
                        <span>Con descuento</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="apply-filters">Aplicar filtros</button>
            <button type="reset" class="reset-filters">Limpiar</button>
        </form>
    </aside>

    <!-- Contenido principal de productos -->
    <main class="category-content">
        <div class="category-header">
            <h1 class="category-title"><?php echo ucfirst($category); ?> <span class="product-count">(<?php echo $total_products; ?>)</span></h1>

            <div class="sort-container">
                <span class="showing-count">Mostrando <?php echo count($products); ?> de <?php echo $total_products; ?> productos</span>
                <select class="sort-select" id="sort-products">
                    <option value="featured">Destacados</option>
                    <option value="price_asc">Precio: Menor a mayor</option>
                    <option value="price_desc">Precio: Mayor a menor</option>
                    <option value="newest">Novedades</option>
                </select>
            </div>
        </div>

        <!-- Grid de productos -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No hay productos disponibles en esta categoría.</p>
                    <a href="<?php echo SITE_URL; ?>" class="btn">Volver al inicio</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <a href="<?php echo SITE_URL; ?>/products/detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                            <?php if ($product['is_new']): ?>
                                <span class="product-badge new">Nuevo</span>
                            <?php elseif ($product['is_bestseller']): ?>
                                <span class="product-badge bestseller">Más vendido</span>
                            <?php endif; ?>

                            <div class="product-image-container">
                                <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $product['image']; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="product-image">
                            </div>

                            <div class="product-info">
                                <span class="product-category"><?php echo $product['category']; ?></span>
                                <h3 class="product-title"><?php echo $product['name']; ?></h3>
                                <span class="product-colors"><?php echo $product['colors']; ?> colores</span>

                                <div class="product-pricing">
                                    <?php if ($product['original_price'] > $product['price']): ?>
                                        <span class="original-price"><?php echo number_format($product['original_price'], 2); ?> €</span>
                                        <?php
                                        $discount = round(($product['original_price'] - $product['price']) / $product['original_price'] * 100);
                                        ?>
                                        <span class="discount-tag">-<?php echo $discount; ?>%</span>
                                    <?php endif; ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 2); ?> €</span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Paginación (puedes implementarla después) -->
        <div class="pagination-container">
            <!-- Aquí iría tu lógica de paginación -->
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
