<?php

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Inicio - Calzado y ropa deportiva';

// Obtener productos destacados
$featured_products = getFeaturedProducts($db);

require_once 'includes/header.php';
?>

    <section class="hero" style="background-image: url('assets/images/logo.jpg');">
        <div class="hero-content">
            <h1>ENCUENTRA TU PAR PERFECTO</h1>
            <p>Descubre la última colección de zapatillas deportivas</p>
            <a href="<?php echo SITE_URL; ?>/products/category.php?cat=colección" class="btn">Ver colección</a>
        </div>
    </section>


    <section class="featured-products">
        <h2>Productos Destacados</h2>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <a href="products/detail.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="categories">
        <h2>Explora por Categoría</h2>
        <div class="categories-grid">
            <div class="category-card running">
                <a href="<?php echo SITE_URL; ?>/products/category.php?cat=running">
                    <h3>Running</h3>
                </a>
            </div>
            <div class="category-card basketball">
                <a href="<?php echo SITE_URL; ?>/products/category.php?cat=basketball">
                    <h3>Basketball</h3>
                </a>
            </div>
            <div class="category-card training">
                <a href="<?php echo SITE_URL; ?>/products/category.php?cat=training">
                    <h3>Training</h3>
                </a>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>