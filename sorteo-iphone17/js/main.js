/**
 * ============================================
 * SORTEO IPHONE 17 PRO MAX - JavaScript
 * ============================================
 */

document.addEventListener('DOMContentLoaded', () => {

    // ============================================
    // TEMA OSCURO / CLARO
    // ============================================
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    
    // Cargar tema guardado o usar oscuro por defecto
    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    actualizarIconoTema(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            actualizarIconoTema(next);
        });
    }

    function actualizarIconoTema(theme) {
        if (!themeToggle) return;
        if (theme === 'dark') {
            themeToggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>';
        } else {
            themeToggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
        }
    }

    // ============================================
    // COUNTDOWN REGRESIVO
    // ============================================
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const fechaCierre = countdownEl.dataset.fecha;
        if (fechaCierre) {
            const target = new Date(fechaCierre).getTime();
            
            function actualizarCountdown() {
                const ahora = Date.now();
                const diff = target - ahora;

                if (diff <= 0) {
                    document.getElementById('cd-dias').textContent = '00';
                    document.getElementById('cd-horas').textContent = '00';
                    document.getElementById('cd-min').textContent = '00';
                    document.getElementById('cd-seg').textContent = '00';
                    return;
                }

                const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
                const horas = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const min = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seg = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById('cd-dias').textContent = String(dias).padStart(2, '0');
                document.getElementById('cd-horas').textContent = String(horas).padStart(2, '0');
                document.getElementById('cd-min').textContent = String(min).padStart(2, '0');
                document.getElementById('cd-seg').textContent = String(seg).padStart(2, '0');
            }

            actualizarCountdown();
            setInterval(actualizarCountdown, 1000);
        }
    }

    // ============================================
    // ANIMACIONES SCROLL (Intersection Observer)
    // ============================================
    const fadeElements = document.querySelectorAll('.fade-in');
    if (fadeElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        fadeElements.forEach(el => observer.observe(el));
    }

    // ============================================
    // VALIDACIÓN FORMULARIO DE REGISTRO
    // ============================================
    const formRegistro = document.getElementById('formRegistro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', (e) => {
            let valido = true;

            // Nombre
            const nombre = document.getElementById('nombre');
            const nombreError = document.getElementById('nombreError');
            if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                mostrarError(nombre, nombreError, 'Ingresa tu nombre completo (mínimo 3 caracteres)');
                valido = false;
            } else {
                limpiarError(nombre, nombreError);
            }

            // Email
            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                mostrarError(email, emailError, 'Ingresa un correo electrónico válido');
                valido = false;
            } else {
                limpiarError(email, emailError);
            }

            // Teléfono
            const telefono = document.getElementById('telefono');
            const telefonoError = document.getElementById('telefonoError');
            const telLimpio = telefono.value.replace(/\s/g, '');
            const telRegex = /^\+569\d{8}$/;
            if (!telRegex.test(telLimpio)) {
                mostrarError(telefono, telefonoError, 'Formato válido: +56 9 XXXX XXXX');
                valido = false;
            } else {
                limpiarError(telefono, telefonoError);
            }

            // Términos
            const terminos = document.getElementById('terminos');
            const terminosError = document.getElementById('terminosError');
            if (!terminos.checked) {
                terminosError.textContent = 'Debes aceptar los términos y condiciones';
                terminosError.classList.add('visible');
                valido = false;
            } else {
                terminosError.classList.remove('visible');
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }

    function mostrarError(input, errorEl, mensaje) {
        input.classList.add('error');
        errorEl.textContent = mensaje;
        errorEl.classList.add('visible');
    }

    function limpiarError(input, errorEl) {
        input.classList.remove('error');
        errorEl.classList.remove('visible');
    }

    // ============================================
    // FORMULARIO DE CONFIRMACIÓN POST-PAGO
    // ============================================
    const formConfirmar = document.getElementById('formConfirmar');
    if (formConfirmar) {
        formConfirmar.addEventListener('submit', async (e) => {
            e.preventDefault();
            let valido = true;

            const nombre = document.getElementById('confirmNombre');
            const nombreError = document.getElementById('confirmNombreError');
            if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                mostrarError(nombre, nombreError, 'Ingresa tu nombre completo');
                valido = false;
            } else {
                limpiarError(nombre, nombreError);
            }

            const email = document.getElementById('confirmEmail');
            const emailError = document.getElementById('confirmEmailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                mostrarError(email, emailError, 'Ingresa un correo electrónico válido');
                valido = false;
            } else {
                limpiarError(email, emailError);
            }

            if (!valido) return;

            // Enviar a confirmar.php
            const btn = formConfirmar.querySelector('button[type="submit"]');
            btn.classList.add('btn-loading');
            btn.innerHTML = '<span class="spinner"></span> Procesando...';

            try {
                const formData = new FormData(formConfirmar);
                const response = await fetch('confirmar.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    // Ocultar formulario y mostrar ticket
                    formConfirmar.style.display = 'none';
                    const ticketResult = document.getElementById('ticketResult');
                    ticketResult.style.display = 'block';
                    document.getElementById('ticketCodigo').textContent = data.codigo;
                    document.getElementById('ticketNombre').textContent = data.nombre;
                    document.getElementById('ticketFecha').textContent = data.fecha;
                } else {
                    btn.classList.remove('btn-loading');
                    btn.innerHTML = 'Confirmar y obtener mi código';
                    alert(data.error || 'Error al procesar la confirmación. Intenta de nuevo.');
                }
            } catch (err) {
                btn.classList.remove('btn-loading');
                btn.innerHTML = 'Confirmar y obtener mi código';
                alert('Error de conexión. Intenta de nuevo.');
            }
        });
    }

    // ============================================
    // CARGAR TABLA DE PARTICIPANTES (TRANSPARENCIA)
    // ============================================
    const tablaParticipantes = document.getElementById('tablaParticipantes');
    if (tablaParticipantes) {
        cargarParticipantes();
    }

    async function cargarParticipantes() {
        try {
            const response = await fetch('participantes.php');
            const data = await response.json();
            const tbody = document.getElementById('tbodyParticipantes');
            const emptyMsg = document.getElementById('participantesEmpty');

            if (!data.participantes || data.participantes.length === 0) {
                if (emptyMsg) emptyMsg.style.display = 'block';
                return;
            }

            if (emptyMsg) emptyMsg.style.display = 'none';
            tbody.innerHTML = '';

            data.participantes.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="code-cell">${escHtml(p.codigo)}</td>
                    <td>${escHtml(p.nombre)}</td>
                    <td>${escHtml(p.telefono)}</td>
                    <td>${escHtml(p.fecha)}</td>
                `;
                tbody.appendChild(tr);
            });
        } catch (err) {
            console.error('Error cargando participantes:', err);
        }
    }

    function escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ============================================
    // ACORDEÓN - BASES DEL SORTEO
    // ============================================
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const item = header.closest('.accordion-item');
            const body = item.querySelector('.accordion-body');
            const isActive = item.classList.contains('active');

            // Cerrar todos
            document.querySelectorAll('.accordion-item.active').forEach(ai => {
                ai.classList.remove('active');
                ai.querySelector('.accordion-body').style.maxHeight = '0';
            });

            // Abrir el clickeado si no estaba activo
            if (!isActive) {
                item.classList.add('active');
                body.style.maxHeight = body.scrollHeight + 'px';
            }
        });
    });

    // ============================================
    // SCROLL SUAVE PARA ANCLAS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                const offset = 70; // altura de navbar
                const top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

});
