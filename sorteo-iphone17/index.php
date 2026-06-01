<?php
/**
 * ============================================
 * SORTEO IPHONE 17 PRO MAX - Landing Principal
 * ============================================
 */
session_start();
require_once 'config.php';

// Obtener configuración del sorteo
$sorteoActivo = obtenerConfig('sorteo_activo');
$fechaCierre = obtenerConfig('fecha_cierre');
$codigoGanador = obtenerConfig('codigo_ganador');
$linkMP = obtenerConfig('link_mercadopago');
$abierto = sorteoAbierto();

// Verificar si hay parámetro de pago exitoso
$pagoExitoso = isset($_GET['pago']) && $_GET['pago'] === 'exitoso';

// Procesar formulario de registro (paso 1)
$registroOk = false;
$registroError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $terminos = isset($_POST['terminos']);

    // Validaciones backend
    if (strlen($nombre) < 3) {
        $registroError = 'El nombre debe tener al menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registroError = 'Correo electrónico inválido.';
    } elseif (!preg_match('/^\+569\d{8}$/', str_replace(' ', '', $telefono))) {
        $registroError = 'Teléfono inválido. Formato: +56 9 XXXX XXXX';
    } elseif (!$terminos) {
        $registroError = 'Debes aceptar los términos y condiciones.';
    } elseif (!$abierto) {
        $registroError = 'El sorteo no está activo en este momento.';
    } else {
        // Guardar datos en sesión para la confirmación post-pago
        $_SESSION['registro'] = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => str_replace(' ', '', $telefono)
        ];
        // Redirigir a Mercado Pago
        header('Location: ' . $linkMP);
        exit;
    }
}

// Obtener ganador si existe
$ganador = null;
if ($codigoGanador) {
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare('SELECT nombre, codigo, fecha_registro FROM tickets WHERE codigo = ?');
    $stmt->execute([$codigoGanador]);
    $ganador = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es-CL" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gana un iPhone 17 Pro Max — Sorteo Oficial</title>
    <meta name="description" content="Participa por solo $1.000 CLP y gana un iPhone 17 Pro Max. Compra tu ticket, recibe tu código único y espera el sorteo.">
    <meta name="theme-color" content="#000000">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ============================================
     NAVBAR
     ============================================ -->
<nav class="navbar">
    <div class="navbar-inner">
        <a href="#" class="navbar-brand">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
            <span>Sorteo</span>
        </a>
        <div class="navbar-actions">
            <button class="theme-toggle" id="themeToggle" aria-label="Cambiar tema">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            </button>
            <?php if ($abierto): ?>
            <a href="#participar" class="btn-nav">Participar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ============================================
     SECCIÓN 1: HERO
     ============================================ -->
<section class="hero" id="inicio">

    <!-- <div class="hero-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
        Sorteo oficial
    </div> -->
    <h1>Gana un<br><span class="text-gradient">iPhone 17 Pro Max</span></h1>
    <p class="hero-subtitle">Participa por tan solo</p>
    <p class="hero-price">$1.000 <span>CLP por ticket</span></p>
    <p><a href="#participar" class="btn btn-primary">Participar Ahora</a></p>

    <img src="img/iphone-17-pro-max.webp" alt="iPhone 17 Pro Max" class="hero-image">

    <video muted loop playsinline controls class="hero-video" style="width:300px;">
        <source src="https://enchillan.com/iphone17/img/video-sorteo.mp4" type="video/mp4">
    </video>

    <div class="separador" style="display:block;height:50px;">
    </div>


    <?php if ($ganador): ?>
        <!-- Sorteo finalizado: mostrar ganador -->
        <div class="winner-banner">
            <p class="overline">🎉 ¡Ya tenemos ganador!</p>
            <p class="winner-code text-gradient"><?= esc($ganador['codigo']) ?></p>
            <p style="color:var(--text-secondary);">Felicitaciones a <strong><?= esc($ganador['nombre']) ?></strong></p>
        </div>

    <?php elseif ($abierto): ?>
        <!-- Countdown -->
        <div class="countdown" id="countdown" data-fecha="<?= esc($fechaCierre) ?>">
            <div class="countdown-item">
                <span class="countdown-number" id="cd-dias">--</span>
                <span class="countdown-label">Días</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-horas">--</span>
                <span class="countdown-label">Horas</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-min">--</span>
                <span class="countdown-label">Min</span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="cd-seg">--</span>
                <span class="countdown-label">Seg</span>
            </div>
        </div>
        <a href="#participar" class="btn btn-primary">Participar ahora</a>
    <?php else: ?>
        <div style="margin-top:24px;">
            <span class="badge badge-warning">Sorteo cerrado</span>
            <p style="color:var(--text-secondary);margin-top:12px;">Las ventas de tickets han finalizado. Pronto se realizará el sorteo.</p>
        </div>
    <?php endif; ?>
</section>

<!-- ============================================
     SECCIÓN 2: CÓMO PARTICIPAR
     ============================================ -->
<section class="section section-alt" id="como-participar">
    <div class="container">
        <div class="text-center fade-in">
            <p class="overline">Simple y rápido</p>
            <h2>Cómo participar</h2>
            <p class="subtitle">Solo 3 pasos para tener tu ticket</p>
        </div>
        <div class="steps-grid">
            <div class="step-card fade-in fade-in-delay-1">
                <div class="step-number">1</div>
                <div class="step-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <h3>Regístrate</h3>
                <p>Completa el formulario con tus datos personales: nombre, correo y teléfono.</p>
            </div>
            <div class="step-card fade-in fade-in-delay-2">
                <div class="step-number">2</div>
                <div class="step-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <h3>Paga $1.000</h3>
                <p>Realiza el pago seguro a través de Mercado Pago. Son solo mil pesos.</p>
            </div>
            <div class="step-card fade-in fade-in-delay-3">
                <div class="step-number">3</div>
                <div class="step-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3>Recibe tu código</h3>
                <p>Confirma tu pago y obtén tu código único. ¡Ya estarás participando!</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     SECCIÓN 3: FORMULARIO DE REGISTRO + PAGO
     ============================================ -->
<section class="section" id="participar">
    <div class="container">
        <div class="text-center fade-in">
            <p class="overline">Paso 1</p>
            <h2>Registro y pago</h2>
            <p class="subtitle">Ingresa tus datos y realiza el pago con Mercado Pago</p>
        </div>

        <?php if ($abierto): ?>
        <div class="form-container fade-in" style="margin-top:48px;">
            <?php if ($registroError): ?>
                <div class="login-error"><?= esc($registroError) ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php#participar" id="formRegistro" novalidate>
                <input type="hidden" name="accion" value="registrar">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: María González" required
                           value="<?= esc($_POST['nombre'] ?? '') ?>">
                    <p class="form-error" id="nombreError"></p>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="tu@correo.cl" required
                           value="<?= esc($_POST['email'] ?? '') ?>">
                    <p class="form-error" id="emailError"></p>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono / WhatsApp</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="+56 9 1234 5678" required
                           value="<?= esc($_POST['telefono'] ?? '') ?>">
                    <p class="form-error" id="telefonoError"></p>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">Acepto los <a href="#bases">términos y condiciones</a> del sorteo y la <a href="#bases">política de privacidad</a>.</label>
                </div>
                <p class="form-error" id="terminosError"></p>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Ir a pagar con Mercado Pago
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
            <div class="form-note">
                💡 Después de pagar, vuelve a esta página para recibir tu código único.
            </div>
        </div>
        <?php else: ?>
        <div class="sorteo-cerrado fade-in" style="margin-top:48px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <h3>Sorteo cerrado</h3>
            <p style="color:var(--text-secondary);">Las inscripciones han finalizado. Gracias por tu interés.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================
     SECCIÓN 4: CONFIRMACIÓN POST-PAGO
     ============================================ -->
<?php if ($pagoExitoso && $abierto): ?>
<section class="section section-alt confirmation-section active" id="confirmar">
    <div class="container">
        <div class="text-center fade-in">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:64px;height:64px;color:var(--success)"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <p class="overline">Paso 2 — Confirmación</p>
            <h2>¡Pago realizado!</h2>
            <p class="subtitle">Confirma tus datos para recibir tu código de participación</p>
        </div>

        <div class="form-container fade-in" style="margin-top:48px;" id="formConfirmarWrapper">
            <form id="formConfirmar" novalidate>
                <div class="form-group">
                    <label for="confirmNombre">Nombre completo</label>
                    <input type="text" id="confirmNombre" name="nombre" placeholder="Tu nombre como lo registraste" required>
                    <p class="form-error" id="confirmNombreError"></p>
                </div>
                <div class="form-group">
                    <label for="confirmEmail">Correo electrónico</label>
                    <input type="email" id="confirmEmail" name="email" placeholder="tu@correo.cl" required>
                    <p class="form-error" id="confirmEmailError"></p>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Confirmar y obtener mi código
                </button>
            </form>
        </div>

        <!-- Resultado: Tarjeta de ticket -->
        <div id="ticketResult" style="display:none;" class="confirmation-success">
            <div class="ticket-card">
                <div class="ticket-header">
                    <h3>🎟️ Tu ticket de participación</h3>
                </div>
                <div class="ticket-body">
                    <p style="color:var(--text-muted);font-size:0.75rem;margin-bottom:8px;">TU CÓDIGO ÚNICO</p>
                    <div class="ticket-code" id="ticketCodigo"></div>
                    <div class="ticket-info">
                        <p><strong>Participante:</strong> <span id="ticketNombre"></span></p>
                        <p><strong>Fecha:</strong> <span id="ticketFecha"></span></p>
                        <p><strong>Monto:</strong> $1.000 CLP</p>
                    </div>
                </div>
                <div class="ticket-divider"></div>
                <div class="ticket-footer">
                    Guarda este código. Se usará para el sorteo. ¡Buena suerte! 🍀
                </div>
            </div>
            <div style="margin-top:32px;">
                <a href="index.php" class="btn btn-secondary">Comprar otro ticket</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================
     SECCIÓN 5: SOBRE EL PREMIO
     ============================================ -->
<section class="section" id="premio">
    <div class="container">
        <div class="text-center fade-in">
            <p class="overline">El premio</p>
            <h2>iPhone 17 Pro Max</h2>
            <p class="subtitle">El smartphone más avanzado de Apple. Diseñado para ser extraordinario.</p>
        </div>
        <div class="specs-grid">
            <div class="spec-item fade-in fade-in-delay-1">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18.01"/></svg>
                <h3>Pantalla Super Retina XDR</h3>
                <p>6.9 pulgadas con ProMotion y tecnología siempre activa.</p>
            </div>
            <div class="spec-item fade-in fade-in-delay-2">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                <h3>Chip A19 Pro</h3>
                <p>Rendimiento y eficiencia de siguiente nivel con GPU de 6 núcleos.</p>
            </div>
            <div class="spec-item fade-in fade-in-delay-1">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <h3>Cámara de 48 MP</h3>
                <p>Sistema de cámara pro con teleobjetivo 5x y modo macro avanzado.</p>
            </div>
            <div class="spec-item fade-in fade-in-delay-2">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="6" width="22" height="12" rx="2" ry="2"/><line x1="23" y1="13" x2="23" y2="11"/></svg>
                <h3>Batería todo el día</h3>
                <p>Hasta 33 horas de reproducción de video con carga rápida USB-C.</p>
            </div>
            <div class="spec-item fade-in fade-in-delay-1">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <h3>Titanio de grado aeroespacial</h3>
                <p>Diseño liviano y resistente con Ceramic Shield de última generación.</p>
            </div>
            <div class="spec-item fade-in fade-in-delay-2">
                <svg class="spec-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 3v4M3 5h4M6 17v4M4 19h4M13 3l2 4 4 1-3 3 1 5-4-2-4 2 1-5-3-3 4-1z"/></svg>
                <h3>Apple Intelligence</h3>
                <p>IA integrada para Siri, escritura inteligente, edición de fotos y más.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     SECCIÓN 6: TRANSPARENCIA
     ============================================ -->
<section class="section section-alt" id="participantes">
    <div class="container">
        <div class="text-center fade-in">
            <p class="overline">100% Transparente</p>
            <h2>Participantes registrados</h2>
            <p class="subtitle">Todos los tickets confirmados se muestran aquí de forma pública</p>
        </div>
        <div class="participants-table-wrapper fade-in" id="tablaParticipantes">
            <table class="participants-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody id="tbodyParticipantes">
                </tbody>
            </table>
            <div class="participants-empty" id="participantesEmpty">
                Aún no hay participantes registrados. ¡Sé el primero!
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     SECCIÓN 7: BASES DEL SORTEO
     ============================================ -->
<section class="section" id="bases">
    <div class="container">
        <div class="text-center fade-in">
            <p class="overline">Toda la info</p>
            <h2>Bases del sorteo</h2>
        </div>
        <div class="accordion fade-in">
            <div class="accordion-item">
                <button class="accordion-header">
                    Mecánica del sorteo
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="accordion-body">
                    <div class="accordion-content">
                        <ul>
                            <li>Cada ticket tiene un valor de $1.000 CLP.</li>
                            <li>El pago se realiza a través de Mercado Pago de forma segura.</li>
                            <li>Tras confirmar el pago, el sistema genera automáticamente un código alfanumérico único.</li>
                            <li>En la fecha establecida, se elegirá un código al azar como ganador.</li>
                            <li>Cada persona puede comprar múltiples tickets para aumentar sus posibilidades.</li>
                            <li>El sorteo se realizará de forma aleatoria utilizando un algoritmo computacional imparcial.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">
                    Fechas importantes
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="accordion-body">
                    <div class="accordion-content">
                        <ul>
                            <li>Inicio de ventas: Desde el momento de publicación de esta página.</li>
                            <li>Cierre de ventas: <?= $fechaCierre ? date('d/m/Y H:i', strtotime($fechaCierre)) . ' hrs.' : 'Por confirmar.' ?></li>
                            <li>Fecha del sorteo: Dentro de las 48 horas posteriores al cierre de ventas.</li>
                            <li>El resultado será publicado en esta misma página y se contactará al ganador por correo y teléfono.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">
                    Entrega del premio
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="accordion-body">
                    <div class="accordion-content">
                        <ul>
                            <li>El premio consiste en un (1) iPhone 17 Pro Max nuevo y sellado.</li>
                            <li>La entrega se coordinará directamente con el ganador.</li>
                            <li>El ganador debe presentar su código de ticket y documento de identidad.</li>
                            <li>El envío dentro de Chile continental es gratuito.</li>
                            <li>El ganador será contactado en un plazo máximo de 72 horas tras el sorteo.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">
                    Datos del organizador
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="accordion-body">
                    <div class="accordion-content">
                        <ul>
                            <li>Organizador: [Nombre del organizador]</li>
                            <li>RUT: [XX.XXX.XXX-X]</li>
                            <li>Correo: contacto@tusitio.cl</li>
                            <li>Teléfono: +56 9 XXXX XXXX</li>
                            <li>Dirección: [Ciudad, Chile]</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">
                    Aviso legal
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="accordion-body">
                    <div class="accordion-content">
                        <ul>
                            <li>Este sorteo no está afiliado, patrocinado ni respaldado por Apple Inc.</li>
                            <li>iPhone y Apple son marcas registradas de Apple Inc.</li>
                            <li>El organizador se reserva el derecho de modificar las bases en caso de fuerza mayor.</li>
                            <li>Al participar, el usuario acepta que sus datos parciales (nombre e inicial del apellido, código) sean publicados en la tabla de transparencia.</li>
                            <li>Los datos personales serán tratados conforme a la legislación chilena vigente sobre protección de datos.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     SECCIÓN 8: FOOTER
     ============================================ -->
<footer class="footer" id="contacto">
    <div class="container">
        <div class="footer-contact">
            <a href="mailto:contacto@tusitio.cl">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                contacto@tusitio.cl
            </a>
            <a href="https://wa.me/56912345678" target="_blank" rel="noopener">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
        </div>
        <div class="footer-links">
            <a href="#bases">Términos y Condiciones</a>
            <a href="#bases">Política de Privacidad</a>
            <a href="#como-participar">Cómo participar</a>
        </div>
        <p class="footer-copy">
            © <?= date('Y') ?> [Nombre del organizador]. Todos los derechos reservados.<br>
            Este sorteo no está afiliado ni patrocinado por Apple Inc.
        </p>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
