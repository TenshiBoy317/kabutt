<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Verificar si hay una redirección pendiente
$redirect = isset($_GET['redirect']) ? sanitizeInput($_GET['redirect']) : 'account';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    $user_id = userLogin($db, $email, $password);

    if ($user_id) {
        session_start();
        $_SESSION['user_id'] = $user_id;

        // Redirigir a la página solicitada o por defecto
        $redirect_to = $redirect === 'cart' ? '/cart.php' : '/user/account.php';
        header("Location: ".SITE_URL.$redirect_to);
        exit();
    } else {
        $error = "Email o contraseña incorrectos";
    }
}

$page_title = 'Iniciar Sesión - ' . SITE_NAME;
require_once '../includes/header.php';
?>

    <!-- Resto del formulario de login... -->
    <section class="auth-form">
        <h1>Iniciar Sesión</h1>

        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </section>

<?php require_once '../includes/footer.php'; ?>