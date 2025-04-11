<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo $page_title ?? 'Calzado y ropa deportiva'; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>">
                <span>SportStyle</span>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/products/category.php?cat=running">Running</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products/category.php?cat=basketball">Basketball</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products/category.php?cat=training">Training</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products/category.php?cat=football">Football</a></li>
            </ul>
        </nav>
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo SITE_URL; ?>/user/account.php"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/user/login.php"><i class="fas fa-user"></i></a>
            <?php endif; ?>

            <a href="<?php echo isset($_SESSION['user_id']) ? SITE_URL.'/cart.php' : SITE_URL.'/user/login.php?redirect=cart'; ?>">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>
</header>
<main>