# Cabañas Las Lengas — Sitio web y panel de administración

Sitio web de cabañas en Puerto Natales (Patagonia chilena) construido en PHP + MySQL,
con un panel de administración propio para gestionar cabañas, galería, equipamiento,
reservas y configuración del sitio.

---

## Requisitos

- PHP 8.1 o superior (probado con PHP 8.2)
- MySQL 5.7 / MariaDB 10.4 o superior
- Apache con `mod_rewrite` (producción) o el servidor embebido de PHP (desarrollo)
- Extensiones PHP: `pdo_mysql`, `mbstring`, `fileinfo`, `curl`, `openssl` (para correo)

En local se puede usar XAMPP: PHP en `C:\xampp\php\php.exe` y MySQL en `C:\xampp\mysql`.

---

## Instalación

### 1. Clonar el proyecto

```bash
git clone <URL_DEL_REPOSITORIO> cabanaslaslengas.cl
cd cabanaslaslengas.cl
```

### 2. Crear el archivo de configuración `.env`

El archivo `.env` **no se versiona** (contiene credenciales). Se crea a partir del ejemplo:

```powershell
# Windows
Copy-Item .env.example .env
```

```bash
# Linux / macOS
cp .env.example .env
```

Editar `.env` y completar:

| Variable | Descripción |
|---|---|
| `APP_ENV` | `local` o `production` |
| `APP_DEBUG` | `true` muestra errores en pantalla (solo desarrollo) |
| `APP_URL` | URL pública CON barra final, ej. `https://cabanaslaslengas.cl/` |
| `APP_TIMEZONE` | Zona horaria, por defecto `America/Santiago` |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Conexión a MySQL |
| `MAIL_*` | SMTP para el envío de correos (PHPMailer) |
| `MAIL_ADMIN_ADDRESS` | Correo que recibe las notificaciones de reservas |
| `RECAPTCHA_SECRET_KEY` | Clave secreta de Google reCAPTCHA |

> **Importante:** en producción usar `APP_ENV=production` y `APP_DEBUG=false`.

### 3. Crear la base de datos

```bash
mysql -u USUARIO -p -e "CREATE DATABASE laslengas CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u USUARIO -p laslengas < database/schema.sql
```

`database/schema.sql` contiene **solo la estructura** (sin datos). Los datos reales
del sitio (cabañas, reservas, configuración) no se versionan por privacidad.

### 4. Crear el usuario administrador

La tabla `admin_users` guarda contraseñas con `password_hash()`. Para crear/actualizar
un usuario admin, ejecutar una vez:

```bash
php -r "echo password_hash('TU_CLAVE_SEGURA', PASSWORD_DEFAULT), PHP_EOL;"
```

E insertar el resultado:

```sql
INSERT INTO admin_users (username, password_hash, email, role)
VALUES ('admin', '<HASH_GENERADO>', 'correo@dominio.cl', 'admin');
```

### 5. Probar en local

Con XAMPP encendido (Apache + MySQL), abrir:

- Sitio: `http://localhost/cabanaslaslengas.cl/`
- Panel: `http://localhost/cabanaslaslengas.cl/admin/`

O con el servidor embebido de PHP:

```bash
php -S 127.0.0.1:8080 -t .
```

---

## Creación de cabañas (flujo automático)

Desde **Admin → Cabañas → Nueva Cabaña**:

1. Se guarda la cabaña en la tabla `cabins`.
2. Se sube la **imagen de portada** (tarjeta del index) y la **imagen principal**
   (slider de la página de la cabaña). Si no hay portada se usa la principal.
3. Se genera **automáticamente** el archivo físico `cabana-{slug}.php` en la raíz.
   Ese archivo carga el modelo informativo común `includes/cabin_page.php`.
4. Todas las cabañas comparten el mismo modelo; el contenido (nombre, descripción,
   precio, fotos, galería) se lee de la base de datos en tiempo real.

Notas:

- Si ya existe un archivo `cabana-{slug}.php` **hecho a mano** (las 3 cabañas
  originales del sitio), no se sobreescribe; el panel avisa con una advertencia.
- Al **cambiar el slug** de una cabaña se elimina el archivo anterior y se genera el nuevo.
- Al **eliminar** una cabaña se borran también su archivo físico y sus imágenes.
- La galería de cada cabaña usa como categoría su `slug` (Admin → Galería).

### Grilla de "Nuestras Cabañas" en el index

La cantidad de tarjetas por fila se adapta automáticamente (`includes/cabin_grid.php`):

| Cabañas activas | Tarjetas por fila |
|---|---|
| 1 | 1 (centrada) |
| 2 | 2 |
| 3 | 3 (situación actual) |
| 4 | 4 |
| 5 | 3 + 2 |
| 6 | 3 + 3 |
| 7 | 4 + 3 |
| 8 | 4 + 4 |
| 9 | 3 + 3 + 3 |
| 12 | 4 + 4 + 4 |

Las filas incompletas se centran para que no quede una tarjeta aislada a la izquierda.

---

## Estructura del proyecto

```
├── index.php                  Página principal (secciones dinámicas desde la BD)
├── cabana-{slug}.php          Páginas públicas de cabañas (generadas por el panel)
├── config_public.php          Configuración para páginas públicas
├── includes/
│   ├── env.php                Cargador de variables .env
│   ├── cabin_page.php         Modelo informativo común de cabaña
│   └── cabin_grid.php         Cálculo de la grilla de cabañas
├── admin/                     Panel de administración
│   ├── includes/config.php    Configuración central + helpers de cabañas
│   ├── process/               Controladores (guardar cabaña, galería, etc.)
│   └── assets/uploads/        Imágenes subidas
├── assets/                    CSS, JS, imágenes y librerías del sitio
├── database/schema.sql        Estructura de la base de datos (sin datos)
├── forms/                     Procesamiento de formularios de contacto/reserva
├── PHPMailer/                 Librería de correo
└── .env.example               Ejemplo de configuración (el .env real no se versiona)
```

---

## Seguridad

- `.env`, `error_log` y archivos `.sql`, `.log` están bloqueados por `.htaccess`
  y excluidos en `.gitignore`.
- Nunca subir `.env` al repositorio. En el servidor se crea manualmente.
- Cambiar las claves de correo/SMTP si se compartieron en algún momento.
- El acceso a `/admin/` requiere usuario y contraseña.

---

## Git

El repositorio ya está inicializado. Para publicarlo en GitHub/GitLab:

```bash
git remote add origin <URL_DEL_REPOSITORIO>
git push -u origin main
```

---

## Pendientes / notas

- `en/` contiene una versión antigua estática en inglés; el idioma del sitio principal
  se controla con `?lan=es` / `?lan=en`.
- El sitio original tenía 3 cabañas con páginas hechas a mano; las cabañas nuevas usan
  el modelo común generado automáticamente.
