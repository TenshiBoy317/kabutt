<?php
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

$page_title = 'Registro';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        if (userRegister($db, $name, $email, $password)) {
            header('Location: /login.php?registered=1');
            exit;
        } else {
            $error = "Error al registrar el usuario. Inténtalo de nuevo.";
        }
    }
}

require_once '../includes/header.php';
?>

    <section class="auth-form">
        <h1>Registro</h1>

        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <button type="submit" class="btn">Registrarse</button>
        </form>

        <p>¿Ya tienes cuenta? <a href="/user/login.php">Inicia sesión aquí</a></p>
    </section>

<?php require_once '../includes/footer.php'; ?>