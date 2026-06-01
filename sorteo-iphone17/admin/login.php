<?php
/**
 * ============================================
 * ADMIN - LOGIN
 * Autenticación con usuario y contraseña de config.php
 * ============================================
 */
session_start();
require_once '../config.php';

// Si ya hay sesión, redirigir al panel
if (isset($_SESSION['admin_logueado']) && $_SESSION['admin_logueado'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');
    
    if ($usuario === ADMIN_USER && $clave === ADMIN_PASS) {
        $_SESSION['admin_logueado'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es-CL" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Iniciar sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="login-wrapper">
    <div class="form-container login-card">
        <h1>🔐 Panel Admin</h1>
        <?php if ($error): ?>
            <div class="login-error"><?= esc($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label for="clave">Contraseña</label>
                <input type="password" id="clave" name="clave" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px;">
                Iniciar sesión
            </button>
        </form>
        <p style="text-align:center;margin-top:24px;font-size:0.8125rem;color:var(--text-muted);">
            <a href="../index.php">← Volver a la landing</a>
        </p>
    </div>
</div>
</body>
</html>
