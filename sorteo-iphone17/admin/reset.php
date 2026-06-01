<?php
/**
 * ============================================
 * ADMIN - RESET COMPLETO DEL SORTEO
 * Borra TODOS los tickets y reinicia el sistema
 * Solo accesible para administradores autenticados
 * ============================================
 */
session_start();
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar sesión admin
if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado.']);
    exit;
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

// Verificar token CSRF anti-doble-clic
$token = $_POST['token'] ?? '';
if (empty($token) || $token !== ($_SESSION['reset_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Token inválido. Recarga la página e intenta de nuevo.']);
    exit;
}

// Invalidar el token después de usarlo (uso único)
unset($_SESSION['reset_token']);

try {
    $pdo = obtenerConexion();

    // Contar tickets antes de borrar (para el log)
    $stmt = $pdo->query('SELECT COUNT(*) FROM tickets');
    $totalBorrados = $stmt->fetchColumn();

    // Truncar tabla tickets (reset AUTO_INCREMENT también)
    $pdo->exec('TRUNCATE TABLE tickets');

    // Resetear configuración del sorteo
    actualizarConfig('codigo_ganador', null);
    actualizarConfig('sorteo_activo', '1');

    echo json_encode([
        'success'        => true,
        'tickets_borrados' => (int) $totalBorrados,
        'mensaje'        => "Se eliminaron {$totalBorrados} ticket(s). El sorteo fue reiniciado y las ventas están abiertas."
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
