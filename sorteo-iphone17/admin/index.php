<?php
/**
 * ============================================
 * PANEL DE ADMINISTRACIÓN
 * Dashboard, configuración, participantes, sorteo
 * Protegido con sesión PHP
 * ============================================
 */
session_start();
require_once '../config.php';

// Verificar sesión admin
if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$pdo = obtenerConexion();
$mensaje = '';
$tipoMensaje = '';

// Generar token CSRF para el reset (uso único)
if (empty($_SESSION['reset_token'])) {
    $_SESSION['reset_token'] = bin2hex(random_bytes(16));
}
$resetToken = $_SESSION['reset_token'];

// ============================================
// PROCESAR ACCIONES POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Actualizar configuración
    if ($accion === 'guardar_config') {
        $fechaCierre = trim($_POST['fecha_cierre'] ?? '');
        $linkMP = trim($_POST['link_mercadopago'] ?? '');
        
        if ($fechaCierre) {
            actualizarConfig('fecha_cierre', $fechaCierre);
        }
        if ($linkMP) {
            actualizarConfig('link_mercadopago', $linkMP);
        }
        $mensaje = 'Configuración actualizada correctamente.';
        $tipoMensaje = 'success';
    }
    
    // Cerrar ventas manualmente
    if ($accion === 'cerrar_ventas') {
        actualizarConfig('sorteo_activo', '0');
        $mensaje = 'Ventas cerradas manualmente.';
        $tipoMensaje = 'success';
    }
    
    // Reabrir ventas
    if ($accion === 'abrir_ventas') {
        actualizarConfig('sorteo_activo', '1');
        actualizarConfig('codigo_ganador', null);
        $mensaje = 'Ventas reabiertas.';
        $tipoMensaje = 'success';
    }
}

// ============================================
// OBTENER DATOS PARA EL DASHBOARD
// ============================================
$sorteoActivo = obtenerConfig('sorteo_activo');
$fechaCierre = obtenerConfig('fecha_cierre');
$codigoGanador = obtenerConfig('codigo_ganador');
$linkMP = obtenerConfig('link_mercadopago');

// Estadísticas
$stmt = $pdo->query('SELECT COUNT(*) as total, COALESCE(SUM(monto), 0) as recaudacion FROM tickets');
$stats = $stmt->fetch();
$totalTickets = $stats['total'];
$recaudacion = $stats['recaudacion'];

// Estado del sorteo
if ($codigoGanador) {
    $estadoSorteo = 'Finalizado';
    $estadoBadge = 'badge-error';
} elseif ($sorteoActivo === '1' && ($fechaCierre && strtotime($fechaCierre) > time())) {
    $estadoSorteo = 'Abierto';
    $estadoBadge = 'badge-success';
} else {
    $estadoSorteo = 'Cerrado';
    $estadoBadge = 'badge-warning';
}

// Obtener datos del ganador si existe
$ganador = null;
if ($codigoGanador) {
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE codigo = ?');
    $stmt->execute([$codigoGanador]);
    $ganador = $stmt->fetch();
}

// Paginación de participantes
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 20;
$offset = ($pagina - 1) * $porPagina;
$totalPaginas = max(1, ceil($totalTickets / $porPagina));

$stmt = $pdo->prepare('SELECT * FROM tickets ORDER BY id DESC LIMIT ? OFFSET ?');
$stmt->bindValue(1, $porPagina, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$participantes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es-CL" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración — Sorteo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

<!-- Header -->
<header class="admin-header">
    <h1>🎛️ Panel de Administración</h1>
    <div style="display:flex;gap:12px;align-items:center;">
        <a href="../index.php" class="btn-nav" style="font-size:0.75rem;" target="_blank">Ver landing</a>
        <a href="logout.php" style="font-size:0.8125rem;color:var(--error);">Cerrar sesión</a>
    </div>
</header>

<div class="admin-content">
    
    <!-- Mensaje de acción -->
    <?php if ($mensaje): ?>
    <div style="background:<?= $tipoMensaje === 'success' ? 'rgba(48,209,88,0.1)' : 'rgba(255,69,58,0.1)' ?>;border:1px solid <?= $tipoMensaje === 'success' ? 'rgba(48,209,88,0.2)' : 'rgba(255,69,58,0.2)' ?>;border-radius:var(--radius-sm);padding:14px 20px;margin-bottom:24px;font-size:0.875rem;color:<?= $tipoMensaje === 'success' ? 'var(--success)' : 'var(--error)' ?>;">
        <?= esc($mensaje) ?>
    </div>
    <?php endif; ?>

    <!-- ============================================
         DASHBOARD
         ============================================ -->
    <div class="admin-grid">
        <div class="admin-stat">
            <div class="stat-label">Tickets vendidos</div>
            <div class="stat-value accent"><?= number_format($totalTickets) ?></div>
        </div>
        <div class="admin-stat">
            <div class="stat-label">Recaudación total</div>
            <div class="stat-value success">$<?= number_format($recaudacion, 0, ',', '.') ?></div>
        </div>
        <div class="admin-stat">
            <div class="stat-label">Estado del sorteo</div>
            <div class="stat-value"><span class="badge <?= $estadoBadge ?>"><?= $estadoSorteo ?></span></div>
        </div>
    </div>

    <!-- ============================================
         CONFIGURACIÓN
         ============================================ -->
    <div class="admin-section">
        <h2>⚙️ Configuración</h2>
        <form method="POST" action="index.php">
            <input type="hidden" name="accion" value="guardar_config">
            <div class="form-group">
                <label for="fecha_cierre">Fecha y hora de cierre</label>
                <input type="datetime-local" id="fecha_cierre" name="fecha_cierre" 
                       value="<?= $fechaCierre ? date('Y-m-d\TH:i', strtotime($fechaCierre)) : '' ?>"
                       style="width:100%;padding:14px 16px;font-family:inherit;font-size:1rem;color:var(--text-primary);background:var(--input-bg);border:1px solid var(--border);border-radius:var(--radius-sm);outline:none;">
            </div>
            <div class="form-group">
                <label for="link_mercadopago">Enlace de pago Mercado Pago</label>
                <input type="text" id="link_mercadopago" name="link_mercadopago" 
                       value="<?= esc($linkMP) ?>"
                       placeholder="https://link.mercadopago.cl/...">
            </div>
            <div class="admin-btn-group">
                <button type="submit" class="btn btn-primary" style="font-size:0.875rem;padding:12px 24px;">
                    Guardar cambios
                </button>
            </div>
        </form>
        
        <div class="admin-btn-group" style="margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
            <?php if ($sorteoActivo === '1'): ?>
            <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres cerrar las ventas?');">
                <input type="hidden" name="accion" value="cerrar_ventas">
                <button type="submit" class="btn btn-secondary" style="font-size:0.875rem;padding:12px 24px;border-color:var(--error);color:var(--error);">
                    🔒 Cerrar ventas
                </button>
            </form>
            <?php else: ?>
            <form method="POST" action="index.php" style="display:inline;" onsubmit="return confirm('¿Quieres reabrir las ventas? Esto también eliminará el ganador actual si existe.');">
                <input type="hidden" name="accion" value="abrir_ventas">
                <button type="submit" class="btn btn-secondary" style="font-size:0.875rem;padding:12px 24px;border-color:var(--success);color:var(--success);">
                    🔓 Reabrir ventas
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================
         ZONA DE PELIGRO — RESET COMPLETO
         ============================================ -->
    <div class="admin-section" style="border:1px solid rgba(255,69,58,0.35);background:rgba(255,69,58,0.04);">
        <h2 style="color:var(--error);">⚠️ Zona de Peligro</h2>
        <p style="color:var(--text-secondary);font-size:0.9375rem;margin-bottom:20px;">
            Borra <strong>todos los tickets</strong> registrados, elimina el ganador actual y reactiva las ventas.
            Usa esto solo para limpiar datos de prueba antes del sorteo real.
        </p>
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <button id="btnReset" class="btn btn-secondary"
                    style="font-size:0.875rem;padding:12px 24px;border-color:var(--error);color:var(--error);"
                    onclick="ejecutarReset('<?= $resetToken ?>')">
                🗑️ Borrar todos los tickets y reiniciar
            </button>
            <span style="font-size:0.8125rem;color:var(--text-muted);">Tickets actuales: <strong><?= number_format($totalTickets) ?></strong></span>
        </div>
        <div id="resetResultado" style="margin-top:16px;"></div>
    </div>

    <!-- ============================================
         SORTEO
         ============================================ -->
    <div class="admin-section">
        <h2>🎰 Sorteo</h2>
        
        <?php if ($ganador): ?>
            <!-- Ya hay ganador -->
            <div class="winner-result">
                <p style="font-size:0.875rem;color:var(--text-muted);">🎉 ¡GANADOR DEL SORTEO!</p>
                <div class="winner-code-admin"><?= esc($ganador['codigo']) ?></div>
                <div style="margin-top:16px;font-size:0.9375rem;color:var(--text-secondary);line-height:2;">
                    <p><strong>Nombre:</strong> <?= esc($ganador['nombre']) ?></p>
                    <p><strong>Email:</strong> <?= esc($ganador['email']) ?></p>
                    <p><strong>Teléfono:</strong> <?= esc($ganador['telefono']) ?></p>
                    <p><strong>Fecha registro:</strong> <?= date('d/m/Y H:i', strtotime($ganador['fecha_registro'])) ?></p>
                </div>
            </div>
        <?php else: ?>
            <!-- Botón ejecutar sorteo -->
            <p style="color:var(--text-secondary);margin-bottom:20px;font-size:0.9375rem;">
                Total de tickets en juego: <strong><?= number_format($totalTickets) ?></strong>
            </p>
            <?php if ($estadoSorteo === 'Abierto'): ?>
                <p style="color:var(--error);font-size:0.875rem;margin-bottom:16px;">
                    ⚠️ Las ventas deben estar cerradas antes de ejecutar el sorteo.
                </p>
                <button class="btn btn-primary btn-disabled" style="font-size:0.875rem;padding:12px 24px;" disabled>
                    Ejecutar Sorteo
                </button>
            <?php else: ?>
                <button id="btnSorteo" class="btn btn-primary" style="font-size:0.875rem;padding:12px 24px;" onclick="ejecutarSorteo()">
                    🎲 Ejecutar Sorteo
                </button>
            <?php endif; ?>
            <div id="sorteoResultado"></div>
        <?php endif; ?>
    </div>

    <!-- ============================================
         PARTICIPANTES
         ============================================ -->
    <div class="admin-section">
        <h2>👥 Participantes (<?= number_format($totalTickets) ?>)</h2>
        
        <div style="margin-bottom:20px;">
            <a href="exportar.php" class="btn btn-secondary" style="font-size:0.875rem;padding:10px 20px;">
                📥 Descargar CSV
            </a>
        </div>
        
        <?php if (count($participantes) > 0): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Código</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participantes as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= esc($p['nombre']) ?></td>
                        <td><?= esc($p['email']) ?></td>
                        <td><?= esc($p['telefono']) ?></td>
                        <td class="code-cell"><?= esc($p['codigo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_registro'])) ?></td>
                        <td>$<?= number_format($p['monto'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina > 1): ?>
                <a href="index.php?pagina=<?= $pagina - 1 ?>">← Anterior</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i === $pagina): ?>
                    <span class="current"><?= $i ?></span>
                <?php elseif ($i <= 3 || $i > $totalPaginas - 3 || abs($i - $pagina) <= 2): ?>
                    <a href="index.php?pagina=<?= $i ?>"><?= $i ?></a>
                <?php elseif ($i === 4 || $i === $totalPaginas - 3): ?>
                    <span style="color:var(--text-muted);">...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($pagina < $totalPaginas): ?>
                <a href="index.php?pagina=<?= $pagina + 1 ?>">Siguiente →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <p style="color:var(--text-muted);text-align:center;padding:32px;">No hay participantes registrados.</p>
        <?php endif; ?>
    </div>
</div>

<script>
/**
 * Reset completo con triple confirmación
 */
async function ejecutarReset(token) {
    if (!confirm('⚠️ ADVERTENCIA: Esta acción borrará TODOS los tickets registrados.\n\n¿Estás seguro de que quieres continuar?')) return;
    if (!confirm('🔴 SEGUNDA CONFIRMACIÓN: Se eliminarán todos los participantes y se reiniciará el sorteo.\n\n¿Confirmas que quieres borrar todo?')) return;

    const texto = prompt('Escribe la palabra RESET para confirmar:');
    if (!texto || texto.trim().toUpperCase() !== 'RESET') {
        alert('Operación cancelada. No escribiste RESET.');
        return;
    }

    const btn = document.getElementById('btnReset');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Borrando...';

    try {
        const res = await fetch('reset.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'token=' + encodeURIComponent(token)
        });
        const data = await res.json();

        const div = document.getElementById('resetResultado');
        if (data.success) {
            div.innerHTML = `<div style="background:rgba(48,209,88,0.1);border:1px solid rgba(48,209,88,0.2);border-radius:var(--radius-sm);padding:14px 20px;font-size:0.875rem;color:var(--success);">✅ ${data.mensaje}<br><small style="color:var(--text-muted);">Recarga la página para ver los cambios.</small></div>`;
            btn.innerHTML = '✅ Reset completado';
            setTimeout(() => location.reload(), 2500);
        } else {
            div.innerHTML = `<div style="background:rgba(255,69,58,0.1);border:1px solid rgba(255,69,58,0.2);border-radius:var(--radius-sm);padding:14px 20px;font-size:0.875rem;color:var(--error);">❌ ${data.error}</div>`;
            btn.disabled = false;
            btn.innerHTML = '🗑️ Borrar todos los tickets y reiniciar';
        }
    } catch (e) {
        alert('Error de conexión. Intenta de nuevo.');
        btn.disabled = false;
        btn.innerHTML = '🗑️ Borrar todos los tickets y reiniciar';
    }
}

/**
 * Ejecutar sorteo con doble confirmación
 */
async function ejecutarSorteo() {
    // Primera confirmación
    if (!confirm('¿Estás seguro de que quieres ejecutar el sorteo? Esta acción es irreversible.')) {
        return;
    }
    // Segunda confirmación
    if (!confirm('ÚLTIMA CONFIRMACIÓN: ¿Realmente deseas elegir al ganador ahora? No se puede deshacer.')) {
        return;
    }

    const btn = document.getElementById('btnSorteo');
    btn.classList.add('btn-loading');
    btn.innerHTML = '<span class="spinner"></span> Sorteando...';
    btn.disabled = true;

    try {
        const response = await fetch('sorteo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'ejecutar=1'
        });
        const data = await response.json();

        if (data.success) {
            const g = data.ganador;
            document.getElementById('sorteoResultado').innerHTML = `
                <div class="winner-result" style="margin-top:24px;">
                    <p style="font-size:0.875rem;color:var(--text-muted);">🎉 ¡GANADOR DEL SORTEO!</p>
                    <div class="winner-code-admin">${g.codigo}</div>
                    <div style="margin-top:16px;font-size:0.9375rem;color:var(--text-secondary);line-height:2;">
                        <p><strong>Nombre:</strong> ${g.nombre}</p>
                        <p><strong>Email:</strong> ${g.email}</p>
                        <p><strong>Teléfono:</strong> ${g.telefono}</p>
                        <p><strong>Fecha registro:</strong> ${g.fecha}</p>
                    </div>
                    <p style="margin-top:16px;font-size:0.8125rem;color:var(--text-muted);">
                        Total de tickets: ${data.total_tickets}
                    </p>
                </div>
            `;
            btn.style.display = 'none';
        } else {
            alert(data.error || 'Error al ejecutar el sorteo.');
            btn.classList.remove('btn-loading');
            btn.innerHTML = '🎲 Ejecutar Sorteo';
            btn.disabled = false;
        }
    } catch (err) {
        alert('Error de conexión. Intenta de nuevo.');
        btn.classList.remove('btn-loading');
        btn.innerHTML = '🎲 Ejecutar Sorteo';
        btn.disabled = false;
    }
}
</script>

</body>
</html>
