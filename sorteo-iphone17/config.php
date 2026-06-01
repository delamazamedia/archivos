<?php
/**
 * ============================================
 * CONFIGURACIÓN GENERAL DEL SORTEO
 * ============================================
 *
 * INSTRUCCIONES:
 * 1. Cambia los datos de conexión MySQL según tu hosting.
 * 2. Cambia el usuario y contraseña del panel de administración.
 * 3. Sube todos los archivos al servidor.
 * 4. Ejecuta el archivo database.sql en phpMyAdmin.
 * 5. ¡Listo! Accede a /admin/ para configurar el sorteo.
 */

 // ============================================
 // CONEXIÓN A BASE DE DATOS MYSQL
 // ============================================
 define('DB_HOST', 'localhost');          // Servidor MySQL (generalmente localhost)
 define('DB_NAME', 'u108524332_sorteo_iphone');      // Nombre de la base de datos
 define('DB_USER', 'u108524332_sorteo_iphone');               // Usuario MySQL
 define('DB_PASS', '#kJ1CdG2T9&');                   // Contraseña MySQL
 define('DB_CHARSET', 'utf8mb4');

 // ============================================
 // CREDENCIALES DEL PANEL DE ADMINISTRACIÓN
 // ============================================
 define('ADMIN_USER', 'sorteo_iphone');           // Usuario para acceder al panel
 define('ADMIN_PASS', 'G229&#kJ17d'); // Contraseña del panel (¡CAMBIAR!)

 // ============================================
 // CONFIGURACIÓN DEL SITIO
 // ============================================
 define('SITE_NAME', 'Sorteo iPhone 17 Pro Max');
 define('SITE_URL', 'https://enchillan.com/iphone17');  // URL base sin barra final
 define('MONTO_TICKET', 1000);              // Precio del ticket en CLP

// ============================================
// FUNCIÓN DE CONEXIÓN PDO
// ============================================
function obtenerConexion() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos. Verifica config.php.');
        }
    }
    return $pdo;
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================

/**
 * Obtener un valor de configuración desde la tabla 'config'
 */
function obtenerConfig($clave) {
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('SELECT valor FROM config WHERE clave = ?');
    $stmt->execute([$clave]);
    $row = $stmt->fetch();
    return $row ? $row['valor'] : null;
}

/**
 * Actualizar un valor de configuración en la tabla 'config'
 */
function actualizarConfig($clave, $valor) {
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('UPDATE config SET valor = ? WHERE clave = ?');
    return $stmt->execute([$valor, $clave]);
}

/**
 * Generar código alfanumérico único de 10 caracteres
 * Excluye caracteres ambiguos: O, 0, I, 1
 */
function generarCodigoUnico() {
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $longitud = 10;
    $pdo = obtenerConexion();

    $intentos = 0;
    do {
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        // Verificar unicidad en la base de datos
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM tickets WHERE codigo = ?');
        $stmt->execute([$codigo]);
        $existe = $stmt->fetchColumn() > 0;
        $intentos++;

        if ($intentos > 100) {
            die('Error generando código único. Contacta al administrador.');
        }
    } while ($existe);

    return $codigo;
}

/**
 * Sanitizar string para output HTML
 */
function esc($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Verificar si el sorteo está activo y abierto
 */
function sorteoAbierto() {
    $activo = obtenerConfig('sorteo_activo');
    $fechaCierre = obtenerConfig('fecha_cierre');
    $ganador = obtenerConfig('codigo_ganador');

    if ($ganador !== null && $ganador !== '') {
        return false; // Ya hay ganador
    }
    if ($activo !== '1') {
        return false; // Cerrado manualmente
    }
    if ($fechaCierre && strtotime($fechaCierre) < time()) {
        return false; // Fecha de cierre pasada
    }
    return true;
}
