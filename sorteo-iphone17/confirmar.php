<?php
/**
 * ============================================
 * CONFIRMACIÓN POST-PAGO
 * Recibe nombre + email vía POST (AJAX)
 * Genera código alfanumérico único y registra el ticket
 * Retorna JSON con los datos del ticket
 * ============================================
 */
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

// Verificar que el sorteo esté activo
if (!sorteoAbierto()) {
    echo json_encode(['success' => false, 'error' => 'El sorteo no está activo en este momento.']);
    exit;
}

// Obtener y sanitizar datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');

// Validaciones backend
if (strlen($nombre) < 3) {
    echo json_encode(['success' => false, 'error' => 'El nombre debe tener al menos 3 caracteres.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Correo electrónico inválido.']);
    exit;
}

// Obtener teléfono desde la sesión o usar uno genérico
// (El teléfono se guardó en la sesión durante el registro previo al pago)
$telefono = '';
if (isset($_SESSION['registro']) && !empty($_SESSION['registro']['telefono'])) {
    $telefono = $_SESSION['registro']['telefono'];
} else {
    // Si no hay sesión, intentar buscar el teléfono por nombre + email en registros previos
    // o solicitar al usuario que repita el proceso
    $telefono = '+56900000000'; // Placeholder si no hay sesión
}

try {
    $pdo = obtenerConexion();
    
    // Generar código único
    $codigo = generarCodigoUnico();
    
    // Insertar ticket en la base de datos
    $stmt = $pdo->prepare('INSERT INTO tickets (nombre, email, telefono, codigo, fecha_registro, monto) VALUES (?, ?, ?, ?, NOW(), ?)');
    $stmt->execute([
        $nombre,
        $email,
        $telefono,
        $codigo,
        MONTO_TICKET
    ]);
    
    // Limpiar datos de sesión del registro
    unset($_SESSION['registro']);
    
    // Formatear fecha para mostrar
    $fecha = date('d/m/Y H:i');
    
    echo json_encode([
        'success' => true,
        'codigo' => $codigo,
        'nombre' => $nombre,
        'fecha' => $fecha
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error al registrar el ticket. Intenta de nuevo.']);
}
