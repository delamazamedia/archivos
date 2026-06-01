# Sorteo iPhone 17 Pro Max — Landing Page

Landing page completa para un sorteo online cuyo premio es un iPhone 17 Pro Max. Cada ticket cuesta $1.000 CLP.

---

## Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior (o MariaDB 10.3+)
- Hosting compartido con soporte PHP + MySQL (cualquier cPanel estándar sirve)

---

## Instalación paso a paso

### 1. Crear la base de datos

1. Accede a **phpMyAdmin** en tu hosting.
2. Crea una nueva base de datos llamada `sorteo_iphone` (o el nombre que prefieras).
3. Selecciona la base de datos y ve a la pestaña **Importar**.
4. Sube el archivo `database.sql` y ejecútalo.
5. Verifica que se crearon las tablas `tickets` y `config`.

### 2. Configurar credenciales

Abre el archivo `config.php` y modifica:

```php
// Conexión a base de datos
define('DB_HOST', 'localhost');          // Generalmente no cambiar
define('DB_NAME', 'sorteo_iphone');      // Nombre de tu BD
define('DB_USER', 'tu_usuario_mysql');    // Usuario MySQL
define('DB_PASS', 'tu_contraseña');      // Contraseña MySQL

// Credenciales del panel admin
define('ADMIN_USER', 'admin');           // Cambia por tu usuario
define('ADMIN_PASS', 'TuClaveSegura');   // Cambia por tu contraseña

// URL de tu sitio
define('SITE_URL', 'https://tusitio.cl');
```

### 3. Subir archivos

Sube todos los archivos al directorio raíz de tu hosting (o a un subdirectorio).

```
/public_html/
├── index.php
├── confirmar.php
├── participantes.php
├── config.php
├── /admin/
├── /css/
├── /js/
└── /img/
```

### 4. Configurar Mercado Pago

1. Inicia sesión en [Mercado Pago](https://www.mercadopago.cl).
2. Ve a **Tu negocio > Links de pago**.
3. Crea un nuevo link de pago por $1.000 CLP.
4. En la configuración del link, **agrega la URL de retorno**: `https://tusitio.cl/index.php?pago=exitoso`
5. Copia el enlace generado.
6. Accede al panel admin (`/admin/`) e ingresa el enlace en el campo **Enlace de pago Mercado Pago**.

### 5. ¡Listo!

- Landing: `https://tusitio.cl/`
- Panel admin: `https://tusitio.cl/admin/`

---

## Flujo del usuario

1. El usuario llega a la landing y hace clic en **Participar ahora**.
2. Llena el formulario con nombre, correo y teléfono.
3. Es redirigido a Mercado Pago para pagar $1.000.
4. Mercado Pago redirige de vuelta a la landing con `?pago=exitoso`.
5. El usuario confirma sus datos y recibe su código único.
6. El código se muestra como una tarjeta de ticket.

---

## Panel de administración

Acceso: `/admin/` con las credenciales configuradas en `config.php`.

- **Dashboard**: Contador de tickets, recaudación total, estado del sorteo.
- **Configuración**: Editar fecha de cierre, enlace de Mercado Pago, cerrar/abrir ventas.
- **Sorteo**: Botón para ejecutar el sorteo (doble confirmación). Solo funciona cuando las ventas están cerradas.
- **Participantes**: Tabla paginada con todos los registros. Botón para descargar CSV.

---

## Estructura de archivos

```
├── index.php           → Landing principal con todas las secciones
├── confirmar.php       → Procesa confirmación post-pago y genera código
├── participantes.php   → API JSON pública para tabla de transparencia
├── config.php          → Credenciales y funciones auxiliares
├── database.sql        → Script SQL para crear las tablas
├── README.md           → Este archivo
├── admin/
│   ├── index.php       → Panel de administración
│   ├── login.php       → Inicio de sesión
│   ├── logout.php      → Cerrar sesión
│   ├── sorteo.php      → Ejecutar el sorteo (AJAX)
│   └── exportar.php    → Descargar CSV
├── css/
│   └── style.css       → Estilos (modo oscuro/claro, Apple-inspired)
├── js/
│   └── main.js         → Countdown, validaciones, animaciones
└── img/
    └── iphone17.png    → Imagen del premio
```

---

## Notas importantes

- Este sorteo **no está afiliado ni patrocinado por Apple Inc.**
- El sistema NO verifica pagos contra la API de Mercado Pago. El respaldo del pago queda en el panel de Mercado Pago.
- Los datos del organizador en la sección de bases legales están como placeholder — **debes editarlos** en `index.php`.
- Recuerda cambiar los datos de contacto en el footer de `index.php`.
