<?php
/**
 * ============================================
 * ADMIN - EJECUTAR SORTEO
 * Selecciona un código aleatorio como ganador
 * Solo funciona si ventas están cerradas y no hay ganador previo
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

try {
    $pdo = obtenerConexion();
    
    // Verificar que no haya ganador previo
    $ganadorExistente = obtenerConfig('codigo_ganador');
    if ($ganadorExistente !== null && $ganadorExistente !== '') {
        echo json_encode(['success' => false, 'error' => 'El sorteo ya fue realizado. El ganador es: ' . $ganadorExistente]);
        exit;
    }
    
    // Verificar que las ventas estén cerradas
    $sorteoActivo = obtenerConfig('sorteo_activo');
    if ($sorteoActivo === '1') {
        // Verificar si la fecha de cierre ya pasó
        $fechaCierre = obtenerConfig('fecha_cierre');
        if ($fechaCierre && strtotime($fechaCierre) > time()) {
            echo json_encode(['success' => false, 'error' => 'Las ventas aún están abiertas. Cierra las ventas primero.']);
            exit;
        }
    }
    
    // Verificar que haya al menos un ticket
    $stmt = $pdo->query('SELECT COUNT(*) FROM tickets');
    $total = $stmt->fetchColumn();
    
    if ($total == 0) {
        echo json_encode(['success' => false, 'error' => 'No hay tickets registrados para sortear.']);
        exit;
    }
    
    // Seleccionar ganador aleatorio
    $stmt = $pdo->query('SELECT id, nombre, email, telefono, codigo, fecha_registro FROM tickets ORDER BY RAND() LIMIT 1');
    $ganador = $stmt->fetch();
    
    if (!$ganador) {
        echo json_encode(['success' => false, 'error' => 'Error al seleccionar ganador.']);
        exit;
    }
    
    // Guardar ganador en config
    actualizarConfig('codigo_ganador', $ganador['codigo']);
    // Cerrar ventas si no lo estaban
    actualizarConfig('sorteo_activo', '0');
    
    echo json_encode([
        'success' => true,
        'ganador' => [
            'nombre' => $ganador['nombre'],
            'email' => $ganador['email'],
            'telefono' => $ganador['telefono'],
            'codigo' => $ganador['codigo'],
            'fecha' => date('d/m/Y H:i', strtotime($ganador['fecha_registro']))
        ],
        'total_tickets' => $total
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos.']);
}
