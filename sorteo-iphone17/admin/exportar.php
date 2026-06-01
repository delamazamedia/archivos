<?php
/**
 * ============================================
 * ADMIN - EXPORTAR CSV
 * Descarga todos los tickets en formato CSV
 * UTF-8 con BOM para compatibilidad con Excel y Google Sheets
 * ============================================
 */
session_start();
require_once '../config.php';

// Verificar sesión admin
if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = obtenerConexion();
    $stmt = $pdo->query('SELECT id, nombre, email, telefono, codigo, fecha_registro, monto FROM tickets ORDER BY id ASC');
    $tickets = $stmt->fetchAll();
    
    // Configurar cabeceras HTTP para descarga CSV
    $filename = 'sorteo_participantes_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    
    // Abrir output y escribir BOM UTF-8
    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF"); // BOM UTF-8
    
    // Cabeceras en español
    fputcsv($output, ['ID', 'Nombre', 'Email', 'Telefono', 'Codigo', 'Fecha Registro', 'Monto']);
    
    // Datos
    foreach ($tickets as $ticket) {
        fputcsv($output, [
            $ticket['id'],
            $ticket['nombre'],
            $ticket['email'],
            $ticket['telefono'],
            $ticket['codigo'],
            date('d/m/Y H:i:s', strtotime($ticket['fecha_registro'])),
            $ticket['monto']
        ]);
    }
    
    fclose($output);
    
} catch (PDOException $e) {
    die('Error al exportar datos.');
}
