<?php
/**
 * ============================================
 * API PÚBLICA DE PARTICIPANTES
 * Retorna JSON con datos parciales de los participantes
 * para la sección de transparencia de la landing
 * ============================================
 */
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

try {
    $pdo = obtenerConexion();
    
    // Obtener todos los participantes ordenados por fecha (más recientes primero)
    $stmt = $pdo->query('SELECT nombre, telefono, codigo, fecha_registro FROM tickets ORDER BY fecha_registro DESC');
    $tickets = $stmt->fetchAll();
    
    $participantes = [];
    
    foreach ($tickets as $ticket) {
        // Nombre parcial: Nombre + inicial del apellido
        // Ej: "Jonathan Fernández" → "Jonathan F."
        $partes = explode(' ', trim($ticket['nombre']));
        if (count($partes) >= 2) {
            $nombreParcial = $partes[0] . ' ' . mb_strtoupper(mb_substr(end($partes), 0, 1, 'UTF-8'), 'UTF-8') . '.';
        } else {
            $nombreParcial = $partes[0];
        }
        
        // Teléfono con últimos 4 dígitos ocultos
        // Ej: "+56912345678" → "+56 9 1234****"
        $tel = $ticket['telefono'];
        if (strlen($tel) >= 4) {
            $telParcial = substr($tel, 0, -4) . '****';
            // Formatear con espacios: +56 9 XXXX****
            if (preg_match('/^\+56(\d)(\d{4})/', $telParcial, $m)) {
                $telParcial = '+56 ' . $m[1] . ' ' . $m[2] . '****';
            }
        } else {
            $telParcial = $tel;
        }
        
        // Formatear fecha DD/MM/YYYY
        $fecha = date('d/m/Y', strtotime($ticket['fecha_registro']));
        
        $participantes[] = [
            'codigo' => $ticket['codigo'],
            'nombre' => $nombreParcial,
            'telefono' => $telParcial,
            'fecha' => $fecha
        ];
    }
    
    echo json_encode([
        'total' => count($participantes),
        'participantes' => $participantes
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener participantes.']);
}
