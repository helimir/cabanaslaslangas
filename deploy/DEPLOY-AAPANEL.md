# Despliegue en VPS con **aaPanel** (Nginx + PHP-FPM + MySQL)

Guía específica para servidores administrados con **aaPanel** (Hetzner u otros).
Reemplaza a la guía genérica de Ubuntu cuando el servidor usa el panel.

> aaPanel usa **Nginx**, por lo que `.htaccess` **no se ejecuta**. Las
> protecciones se agregan al vhost del sitio desde el panel
> (ver `deploy/aapanel/nginx-snippets.conf.example`).

---

## 0. Rutas típicas de aaPanel

| Elemento | Ruta habitual |
|---|---|
| Sitios web | `/www/wwwroot/<dominio>` |
| Config del sitio | `/www/server/panel/vhost/nginx/<dominio>.conf` |
| Config global Nginx | `/www/server/nginx/conf/nginx.conf` |
| Binario / test Nginx | `/www/server/nginx/sbin/nginx -t` |
| PHP (versión 83) | `/www/server/php/83/` |
| php.ini | `/www/server/php/83/etc/php.ini` |
| Servicio PHP-FPM | `systemctl restart php-fpm-83` |
| Servicio Nginx | `systemctl reload nginx` |
| Usuario de PHP-FPM | `www` (grupo `www`) |

Ajusta `RUTA`, `DOMINIO` y la versión de PHP (`83`, `82`, etc.) en los comandos.

```bash
RUTA="/www/wwwroot/cabanaslaslengas.cl"
DOMINIO="cabanaslaslengas.cl"
PHPVER="83"
```

---

## 1. Preparar el sitio en aaPanel

1. **App Store / Software Store**: instalar **Nginx**, **MySQL** y **PHP 8.2/8.3**
   con extensiones `pdo_mysql`, `mbstring`, `fileinfo`, `curl`, `openssl`.
2. **Website / Sitios web → Add site**:
   - Domain: `cabanaslaslengas.cl` (y `www.cabanaslaslengas.cl`)
   - Root: `/www/wwwroot/cabanaslaslengas.cl`
   - PHP version: la instalada (8.2 o 8.3)
   - Base de datos: crear también desde aquí (o luego en Databases).
3. **Seguridad del panel**: abrir puertos **80** y **443** en *Security/Firewall*.

Si el proyecto ya está subido al servidor, mueve/copia la carpeta a
`$RUTA` desde el **File Manager** o por SSH/SFTP. No dejes el sitio dentro de
otra subcarpeta: el `root` de aaPanel debe apuntar a la raíz del proyecto.

---

## 2. Configuración de Nginx en aaPanel

**Website → tu sitio → Settings/Configuración → Config file / Configuración de Nginx**

Pegar **dentro** del bloque `server { ... }` las directivas de
[`deploy/aapanel/nginx-snippets.conf.example`](aapanel/nginx-snippets.conf.example):

- `client_max_body_size 60m;`
- Bloqueo de `.env`, `.git`, logs, `.sql`, `/deploy`, `/database`, `/includes`, `/.dev`.
- Bloqueo de ejecución de PHP dentro de `admin/assets/uploads`.
- `try_files` (solo si el sitio no lo tiene ya).

**No borres** las líneas que administra aaPanel (SSL, `include enable-php-83.conf;`,
`include .../rewrite/...`). Solo agrega los bloques.

Guardar y recargar:

```bash
/www/server/nginx/sbin/nginx -t
systemctl reload nginx
```

> Si aaPanel muestra error al guardar, revisa que no haya un `location /`
> duplicado y que las llaves `{ }` estén balanceadas.

---

## 3. Límites de subida de PHP (galería)

La galería de cabañas sube varias imágenes a la vez (hasta 5 MB cada una).

**Opción panel:** App Store → PHP-8.3 → *Settings/Config* → editar `php.ini`.

**Opción terminal:** editar `/www/server/php/83/etc/php.ini` y ajustar:

```ini
upload_max_filesize = 10M
post_max_size = 60M
max_file_uploads = 30
max_execution_time = 120
memory_limit = 256M
```

Guardar y reiniciar PHP:

```bash
systemctl restart php-fpm-83
```

> En aaPanel el nombre del servicio suele ser `php-fpm-83`; si no existe,
> revisa `systemctl list-units | grep php`.

---

## 4. Archivo `.env` en el servidor

El `.env` **no está en GitHub**. Crearlo en `$RUTA/.env`:

- **File Manager**: copiar `.env.example` → pegar como `.env` → editar.
- O por terminal:

```bash
cd $RUTA
cp .env.example .env
nano .env
chown www:www .env
chmod 640 .env
```

Valores de producción:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cabanaslaslengas.cl/
APP_TIMEZONE=America/Santiago

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=laslengas
DB_USER=laslengas
DB_PASS=CLAVE_SEGURA

MAIL_HOST=mail.cabanaslaslengas.cl
MAIL_PORT=587
MAIL_USERNAME=reservaciones@cabanaslaslengas.cl
MAIL_PASSWORD=CLAVE_CORREO
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=reservaciones@cabanaslaslengas.cl
MAIL_FROM_NAME="Cabañas Las Lengas"
MAIL_ADMIN_ADDRESS=reservaciones@cabanaslaslengas.cl

RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

---

## 4.1 Correo (SendGrid recomendado)

El formulario de reservas envía correos con **PHPMailer**. En Hetzner/VPS se
recomienda **SendGrid por API HTTPS (puerto 443)**, que no depende de puertos
SMTP (Hetzner bloquea el 25) ni de un servidor de correo local.
Guía completa: **[CORREO-SENDGRID.md](CORREO-SENDGRID.md)**.

Configuración mínima en `.env`:

```ini
MAIL_DRIVER=sendgrid
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=reservaciones@cabanaslaslengas.cl
MAIL_FROM_NAME="Cabañas Las Lengas"
MAIL_ADMIN_ADDRESS=reservaciones@cabanaslaslengas.cl
```

Probar:

```bash
cd /www/wwwroot/cabanaslaslengas.cl
php tools/test_mail.php correo@destino.cl
```

### Alternativa: SMTP genérico

El formulario de reservas envía correos **por SMTP** (PHPMailer) usando las
variables `MAIL_*` del `.env`. En un VPS:

- No hay servidor de correo local: la función `mail()` de PHP no sirve.
- **Hetzner bloquea el puerto 25** por defecto → usar **587 (tls)** o **465 (ssl)**.
- El remitente (`MAIL_FROM_ADDRESS`) debe pertenecer al dominio autenticado.

Probar desde la raíz del proyecto (Terminal de aaPanel/SSH):

```bash
cd /www/wwwroot/cabanaslaslengas.cl
php tools/test_mail.php correo@destino.cl
```

Muestra la conversación SMTP completa y el error exacto. Si falla:

```bash
# Conectividad al servidor de correo
openssl s_client -connect mail.cabanaslaslengas.cl:587 -starttls smtp -crlf
nc -vz mail.cabanaslaslengas.cl 587
nc -vz mail.cabanaslaslengas.cl 465

# Logs de PHP (errores de correo)
tail -n 100 /www/server/php/83/var/log/php-fpm.log
```

Errores típicos: credenciales incorrectas, puerto/cifrado equivocado
(587=tls, 465=ssl), host mal escrito o salida bloqueada. Revisar también SPAM y
SPF/DKIM del dominio. Alternativa: usar un proveedor transaccional
(Brevo, Mailgun, SMTP2GO, Gmail con contraseña de aplicación) cambiando
`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
`MAIL_ENCRYPTION` y `MAIL_FROM_ADDRESS` en `.env`.

Las reservas se guardan igualmente aunque el correo falle; el error queda en el
log de PHP y se indica en el log con el número de reserva.

---

## 5. Base de datos

**aaPanel → Databases → Add database**:

- Database: `laslengas`
- User: `laslengas`
- Password: una clave segura
- Charset: `utf8mb4`

### 5.1 Migrar datos desde tu PC (recomendado)

En Windows/XAMPP:

```powershell
C:\xampp\mysql\bin\mysqldump.exe --host=127.0.0.1 --user=root --password `
  --default-character-set=utf8mb4 --add-drop-table laslengas `
  --result-file="D:\respaldo-laslengas.sql"
```

Subir el `.sql` al servidor (SFTP/File Manager) y restaurar:

```bash
mysql -u laslengas -p laslengas < /ruta/respaldo-laslengas.sql
```

También puedes importarlo desde **phpMyAdmin** del panel.

### 5.2 Si la base parte vacía

```bash
mysql -u laslengas -p laslengas < $RUTA/database/schema.sql
```

Crear usuario admin:

```bash
php -r "echo password_hash('TU_CLAVE_SEGURA', PASSWORD_DEFAULT), PHP_EOL;"
mysql -u laslengas -p laslengas -e "INSERT INTO admin_users
  (username,password_hash,email,role)
  VALUES ('admin','HASH','correo@dominio.cl','admin');"
```

### 5.3 Migraciones

```bash
mysql -u laslengas -p laslengas < $RUTA/database/migrations/2026-10-24_cabin_amenities.sql
```

Verificar:

```bash
mysql -u laslengas -p laslengas -e "SHOW TABLES; SELECT COUNT(*) FROM cabin_amenities;"
```

---

## 6. Permisos (crítico)

PHP-FPM en aaPanel corre como **`www`**. El panel crea `cabana-{slug}.php` en la
raíz del proyecto y guarda imágenes en `admin/assets/uploads/`:

```bash
chown -R www:www $RUTA
find $RUTA -type d -exec chmod 755 {} \;
find $RUTA -type f -exec chmod 644 {} \;
chmod 640 $RUTA/.env
```

Con el dueño `www` en la raíz, PHP puede crear los archivos y las imágenes.
Si más adelante haces `git pull` como `root`, normaliza de nuevo con
`chown -R www:www $RUTA`.

Probar desde el panel: crear una cabaña de prueba → debe generar
`cabana-{slug}.php` y permitir subir imágenes → luego eliminarla.

---

## 7. Conectar el repositorio de GitHub con el servidor

Usar la **Terminal** de aaPanel (o SSH). Se recomienda una **Deploy Key** de
solo lectura.

### 7.1 Generar llave y registrarla en GitHub

```bash
ssh-keygen -t ed25519 -C "deploy@aapanel" -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub
```

Copiar la llave pública en GitHub → repo `cabanaslaslangas` →
**Settings → Deploy keys → Add deploy key** (sin write access).

```bash
cat > ~/.ssh/config <<'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_deploy
    IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
ssh -T git@github.com
```

### 7.2 Opción A — clonar (sitio nuevo o carpeta vacía)

```bash
mkdir -p /www/wwwroot
cd /www/wwwroot
git clone git@github.com:helimir/cabanaslaslangas.git cabanaslaslengas.cl
chown -R www:www cabanaslaslengas.cl
```

### 7.3 Opción B — el proyecto ya está en el servidor (adoptar carpeta)

> Hacer respaldo antes: aaPanel → Website → Backup, o:
> `tar -czf /root/cabanas-$(date +%F).tar.gz -C /www/wwwroot cabanaslaslengas.cl`

```bash
cd $RUTA
git init
git remote add origin git@github.com:helimir/cabanaslaslangas.git
git fetch origin
git reset --hard origin/main      # sobrescribe archivos versionados
# .env y archivos no versionados se conservan; NO usar git clean
chown -R www:www $RUTA
```

### 7.4 Actualizaciones

Cada vez que hagas `git push` desde tu PC:

```bash
cd $RUTA
git pull --ff-only origin main
# migraciones nuevas, si las hay:
mysql -u laslengas -p laslengas < database/migrations/XXXX.sql
chown -R www:www $RUTA
```

Nginx/PHP no requieren reinicio por cambios de PHP/CSS.

### 7.5 Alternativas de automatización

- Si tu versión de aaPanel incluye **Git deploy / Deploy** en el sitio,
  configúralo con la URL del repo, rama `main` y la llave de deploy.
- O usa el ejemplo de GitHub Actions adaptando ruta y servicio PHP:
  `deploy/github-actions-deploy-aapanel.yml.example`
  (Secrets: `SSH_HOST`, `SSH_USER`, `SSH_KEY`, `SSH_PORT`).

---

## 8. HTTPS en aaPanel

**Website → sitio → SSL → Let's Encrypt**:

1. Seleccionar el dominio y `www`.
2. Emitir certificado (aaPanel configura Nginx automáticamente).
3. Activar **Force HTTPS**.

No hace falta `certbot` por terminal.

---

## 9. CSS y archivos estáticos

El CSS se sirve desde la raíz del proyecto:

```
$RUTA/assets/css/style.css
→ https://cabanaslaslengas.cl/assets/css/style.css
```

- Si el `root` del sitio en aaPanel apunta al proyecto, `git pull` actualiza el CSS.
- Si el CSS está guardado en **otra ruta** del servidor, copiarlo después de cada
  deploy o servir esa ruta con Nginx:

```bash
rsync -av $RUTA/assets/css/style.css /RUTA_DEL_CSS/style.css
chown www:www /RUTA_DEL_CSS/style.css
```

```nginx
location /assets/ {
    alias /RUTA_DEL_CSS/;
}
```

- aaPanel suele cachear estáticos; después del deploy usa `Ctrl + F5`.
- Los últimos cambios CSS (4:3 en tarjetas y 16:9 en slider de cabaña) están en
  `assets/css/style.css` e `includes/cabin_page.php`.

---

## 10. Verificación posterior

```bash
curl -I https://cabanaslaslengas.cl/
curl -I https://cabanaslaslengas.cl/assets/css/style.css
curl -I https://cabanaslaslengas.cl/.env          # 404/403
curl -I https://cabanaslaslengas.cl/.git/config   # 404/403
curl -I https://cabanaslaslengas.cl/error_log     # 404/403
```

Checklist:

- [ ] Home carga sin errores y las imágenes usan el dominio real.
- [ ] Login en `/admin/` funciona.
- [ ] Crear cabaña de prueba genera `cabana-{slug}.php`, sube portada, principal y galería.
- [ ] La grilla del index se adapta y la página pública muestra su equipamiento.
- [ ] El Centro de Ayuda (`/admin/manual.php`) carga con buscador y tabla de tamaños.
- [ ] Eliminar la cabaña de prueba borra página, fotos y galería.
- [ ] `.env`, `.git`, `error_log` y `.sql` responden 404/403.
- [ ] Formulario de reserva guarda y envía correo.
- [ ] HTTPS activo y forzado.

---

## 11. Respaldos en aaPanel

- **Website → Backup**: respaldo de archivos del sitio.
- **Databases → Backup**: respaldo de la base de datos.
- Configurar frecuencia diaria y retención (7–14 días).
- Antes de cada actualización importante: backup manual + `git log` del commit desplegado.
