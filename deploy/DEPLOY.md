# Despliegue en VPS (Hetzner / Ubuntu + Nginx + PHP-FPM)

> **¿Usas aaPanel?** Esta guía es para Ubuntu/Nginx "puro". Para servidores
> administrados con aaPanel (rutas `/www/wwwroot`, usuario `www`, vhost editado
> desde el panel) usa **[DEPLOY-AAPANEL.md](DEPLOY-AAPANEL.md)**.

Guía para publicar **Cabañas Las Lengas** en un VPS con Nginx, conectar el
repositorio de GitHub con el servidor y actualizar el sitio de forma segura.

> **Nota:** el servidor no usa Apache, por lo que el archivo `.htaccess` **no
> tiene efecto**. Las protecciones (`.env`, logs, `.git`, etc.) se configuran en
> Nginx (`deploy/nginx/cabanaslaslengas.conf.example`).

---

## 0. Antes de empezar

- Dominio apuntando al VPS (registro DNS tipo **A** para `@` y `www`).
- Acceso SSH al servidor (`ssh root@IP_DEL_VPS` o usuario con sudo).
- Repositorio: `https://github.com/helimir/cabanaslaslangas` (rama `main`).
- El archivo `.env` **no está en el repositorio**: se crea en el servidor.
- Los datos de la base de datos tampoco están en el repositorio: hay que
  migrarlos desde el entorno local o crearlos en el servidor.

Ajusta estas variables en los comandos de esta guía:

```bash
DOMINIO="cabanaslaslengas.cl"
RUTA="/var/www/cabanaslaslengas"
USUARIO_DEPLOY="deploy"
REPO="git@github.com:helimir/cabanaslaslangas.git"
PHP_VERSION="8.3"     # ver: ls /run/php/ | grep fpm
```

---

## 1. Paquetes del servidor

```bash
sudo apt update
sudo apt install -y nginx mysql-server php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl php${PHP_VERSION}-xml \
    php${PHP_VERSION}-fileinfo php${PHP_VERSION}-openssl unzip git acl certbot python3-certbot-nginx
```

Verificar que Nginx y PHP-FPM estén activos:

```bash
systemctl status nginx --no-pager
systemctl status php${PHP_VERSION}-fpm --no-pager
ls /run/php/
```

---

## 2. Usuario de despliegue y carpeta del proyecto

```bash
# Crear usuario deploy (solo la primera vez)
sudo adduser --disabled-password --gecos "" $USUARIO_DEPLOY
sudo usermod -aG www-data $USUARIO_DEPLOY

# Carpeta del proyecto
sudo mkdir -p $RUTA
sudo chown -R $USUARIO_DEPLOY:www-data $RUTA
```

---

## 3. Conectar el repositorio con el servidor

La forma recomendada es una **Deploy Key** de solo lectura.

### 3.1 Generar la llave en el servidor

```bash
sudo -u $USUARIO_DEPLOY mkdir -p /home/$USUARIO_DEPLOY/.ssh
sudo -u $USUARIO_DEPLOY ssh-keygen -t ed25519 -C "deploy@$(hostname)" \
     -f /home/$USUARIO_DEPLOY/.ssh/github_deploy -N ""

# Mostrar la llave pública:
sudo -u $USUARIO_DEPLOY cat /home/$USUARIO_DEPLOY/.ssh/github_deploy.pub
```

### 3.2 Registrar la llave en GitHub

GitHub → repositorio **cabanaslaslangas** → *Settings* → *Deploy keys* →
*Add deploy key*:

- Title: `VPS Hetzner`
- Key: pegar la llave pública del paso anterior
- **Allow write access: NO** (solo lectura; el servidor solo hace `git pull`)

### 3.3 Configurar SSH para GitHub

```bash
sudo -u $USUARIO_DEPLOY tee /home/$USUARIO_DEPLOY/.ssh/config >/dev/null <<'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_deploy
    IdentitiesOnly yes
EOF

sudo chmod 600 /home/$USUARIO_DEPLOY/.ssh/config
sudo -u $USUARIO_DEPLOY ssh -o StrictHostKeyChecking=accept-new -T git@github.com
```

Debe responder: `Hi helimir/cabanaslaslangas! You've successfully authenticated...`

### 3.4 Opción A — Clonar desde cero (recomendado si el servidor no tiene archivos)

```bash
# La carpeta debe estar vacía
sudo -u $USUARIO_DEPLOY git clone $REPO $RUTA
```

### 3.5 Opción B — El proyecto ya está en el servidor (adoptar carpeta existente)

> Hacer **respaldo** antes. `git reset --hard` sobrescribe archivos versionados,
> pero **no** elimina `.env` ni las imágenes subidas que no estén en Git.

```bash
# 1) Respaldo
sudo tar -czf /root/cabanas-backup-$(date +%F).tar.gz -C $(dirname $RUTA) $(basename $RUTA)

# 2) Inicializar Git en la carpeta existente
cd $RUTA
sudo -u $USUARIO_DEPLOY git init
sudo -u $USUARIO_DEPLOY git remote add origin $REPO
sudo -u $USUARIO_DEPLOY git fetch origin
sudo -u $USUARIO_DEPLOY git reset --hard origin/main

# 3) Verificar que .env siga presente
ls -la $RUTA/.env
```

> No usar `git clean -fd`: podría borrar imágenes subidas y el `.env`.

---

## 4. Archivo `.env` en el servidor

```bash
cd $RUTA
sudo -u $USUARIO_DEPLOY cp .env.example .env
sudo -u $USUARIO_DEPLOY nano .env
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

MAIL_DRIVER=smtp
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

Permisos del `.env`:

```bash
sudo chown $USUARIO_DEPLOY:www-data $RUTA/.env
sudo chmod 640 $RUTA/.env
```

---

## 4.1 Correo SMTP (importante en VPS)

El formulario de reservas envía correos **por SMTP** (PHPMailer) usando las
variables `MAIL_*` del `.env`. En un VPS:

- No hay servidor de correo local: la función `mail()` de PHP no sirve.
- **Hetzner bloquea el puerto 25** por defecto → usar **587 (tls)** o **465 (ssl)**.
- El remitente (`MAIL_FROM_ADDRESS`) debe pertenecer al dominio autenticado.

Probar desde la raíz del proyecto:

```bash
cd /var/www/cabanaslaslengas
php tools/test_mail.php correo@destino.cl
```

Muestra la conversación SMTP completa y el error exacto. Si falla:

```bash
openssl s_client -connect mail.cabanaslaslengas.cl:587 -starttls smtp -crlf
nc -vz mail.cabanaslaslengas.cl 587
nc -vz mail.cabanaslaslengas.cl 465
tail -n 100 /var/log/php/cabanaslaslengas-php-error.log
```

Errores típicos: credenciales incorrectas, puerto/cifrado equivocado
(587=tls, 465=ssl), host mal escrito o salida bloqueada. Revisar también SPAM y
SPF/DKIM del dominio. Alternativa: proveedor transaccional (Brevo, Mailgun,
SMTP2GO, Gmail con contraseña de aplicación) cambiando las variables `MAIL_*`
en `.env`.

Las reservas se guardan igualmente aunque el correo falle; el error queda en el
log de PHP indicando el número de reserva.

---

## 5. Base de datos

### 5.1 Crear base y usuario

```bash
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS laslengas
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'laslengas'@'localhost' IDENTIFIED BY 'CLAVE_SEGURA';
GRANT ALL PRIVILEGES ON laslengas.* TO 'laslengas'@'localhost';
FLUSH PRIVILEGES;
SQL
```

### 5.2 Migrar los datos desde tu entorno local (recomendado)

En tu PC (Windows/XAMPP):

```powershell
C:\xampp\mysql\bin\mysqldump.exe --host=127.0.0.1 --user=root --password `
  --default-character-set=utf8mb4 --add-drop-table laslengas `
  --result-file="D:\respaldo-laslengas.sql"
```

Subir el respaldo al VPS y restaurarlo:

```bash
scp D:\respaldo-laslengas.sql root@IP_DEL_VPS:/root/
# en el servidor:
sudo mysql laslengas < /root/respaldo-laslengas.sql
```

### 5.3 Si la base parte vacía (sin datos)

```bash
sudo mysql laslengas < $RUTA/database/schema.sql
```

Luego crear el usuario administrador (`admin_users`):

```bash
php -r "echo password_hash('TU_CLAVE_SEGURA', PASSWORD_DEFAULT), PHP_EOL;"
# Copiar el hash y ejecutar:
sudo mysql laslengas -e "INSERT INTO admin_users (username, password_hash, email, role)
VALUES ('admin', 'HASH_GENERADO', 'correo@dominio.cl', 'admin');"
```

También hay que cargar los `site_settings` (títulos, contacto, redes) desde
**Admin → Configuración**.

### 5.4 Migraciones pendientes

Después de importar la base, aplicar las migraciones que falten:

```bash
sudo mysql laslengas < $RUTA/database/migrations/2026-10-24_cabin_amenities.sql
```

Verificar:

```bash
sudo mysql laslengas -e "SHOW TABLES; SELECT COUNT(*) FROM cabin_amenities;"
```

---

## 6. Permisos de escritura (¡importante!)

El panel crea archivos `cabana-{slug}.php` en la **raíz del proyecto** y guarda
imágenes en `admin/assets/uploads/`. El usuario de PHP-FPM (`www-data`) necesita
escritura en esas rutas:

```bash
sudo chown -R $USUARIO_DEPLOY:www-data $RUTA
sudo find $RUTA -type d -exec chmod 2755 {} \;
sudo find $RUTA -type f -exec chmod 0644 {} \;

# Escritura para www-data en la raíz (genera cabana-*.php)
sudo setfacl -m u:www-data:rwx $RUTA

# Escritura para www-data en uploads (imágenes del admin)
sudo setfacl -R -m u:www-data:rwX $RUTA/admin/assets/uploads
sudo setfacl -R -d -m u:www-data:rwX $RUTA/admin/assets/uploads
```

Comprobar desde el panel: crear una cabaña de prueba debe generar
`cabana-{slug}.php` y permitir subir imágenes. Luego eliminarla.

---

## 7. Nginx + PHP-FPM

### 7.1 Configuración del sitio

```bash
sudo cp $RUTA/deploy/nginx/cabanaslaslengas.conf.example \
        /etc/nginx/sites-available/cabanaslaslengas.conf
sudo nano /etc/nginx/sites-available/cabanaslaslengas.conf
#  - server_name  -> tu dominio
#  - root         -> /var/www/cabanaslaslengas
#  - fastcgi_pass -> /run/php/php8.3-fpm.sock (según tu versión)

sudo ln -sf /etc/nginx/sites-available/cabanaslaslengas.conf \
            /etc/nginx/sites-enabled/cabanaslaslengas.conf
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

### 7.2 Ajustes de PHP para subidas

```bash
sudo cp $RUTA/deploy/php-fpm/99-cabanas.ini.example \
        /etc/php/${PHP_VERSION}/fpm/conf.d/99-cabanas.ini
sudo mkdir -p /var/log/php
sudo chown www-data:www-data /var/log/php
sudo systemctl restart php${PHP_VERSION}-fpm
```

### 7.3 HTTPS con Certbot

```bash
sudo certbot --nginx -d cabanaslaslengas.cl -d www.cabanaslaslengas.cl
```

Certbot modifica el bloque de Nginx y agrega la redirección de HTTP a HTTPS.

---

## 8. Verificación posterior al despliegue

```bash
curl -I https://cabanaslaslengas.cl/
curl -I https://cabanaslaslengas.cl/assets/css/style.css
curl -I https://cabanaslaslengas.cl/.env          # debe dar 403/404
curl -I https://cabanaslaslengas.cl/.git/config   # debe dar 403/404
curl -I https://cabanaslaslengas.cl/error_log     # debe dar 403/404
```

Checklist manual:

- [ ] Home carga sin errores y las imágenes apuntan al dominio real.
- [ ] Login en `/admin/` funciona.
- [ ] Crear una cabaña de prueba genera `cabana-{slug}.php` y permite subir portada,
      imagen principal y varias fotos de galería.
- [ ] La cabaña de prueba aparece en el index con la grilla correcta.
- [ ] La página pública de la cabaña muestra su equipamiento asignado (no todo).
- [ ] El **Centro de Ayuda** (`/admin/manual.php`) carga con buscador y tabla de tamaños.
- [ ] Eliminar la cabaña de prueba borra página, fotos y galería.
- [ ] Formulario de reserva guarda en BD y envía correo.
- [ ] `.env`, `.git`, `error_log` y `.sql` responden 403/404.

---

## 9. Actualizar el sitio (deploy de cambios)

Cada vez que hagas `git push` desde tu PC:

```bash
ssh deploy@IP_DEL_VPS
cd /var/www/cabanaslaslengas

git pull --ff-only origin main

# Si hay migraciones nuevas:
sudo mysql laslengas < database/migrations/ARCHIVO_NUEVO.sql

# Si cambiaron configuraciones de PHP/Nginx:
# sudo systemctl reload php8.3-fpm
# sudo systemctl reload nginx
```

> `git pull --ff-only` no borra imágenes subidas ni `.env` (no están versionados
> o son archivos nuevos sin seguimiento).

### 9.1 Push desde tu PC

El repositorio local tiene configurado `origin`; en tu PC:

```bash
cd D:\htdocs\cabanaslaslengas.cl
git status
git push
```

Si Git pide credenciales, usa Git Credential Manager (login de GitHub) o un
token personal con permiso de escritura.

### 9.2 Auto-deploy opcional con GitHub Actions

Hay un ejemplo en `deploy/github-actions-deploy.yml.example`. Para usarlo:

1. Crear en GitHub los *Secrets*: `SSH_HOST`, `SSH_USER`, `SSH_KEY` (llave privada
   de deploy), `SSH_PORT` (si no es 22).
2. Copiar el archivo a `.github/workflows/deploy.yml` y hacer commit.
3. Cada push a `main` ejecutará `git pull` en el servidor.

---

## 10. CSS y archivos estáticos (caché y rutas)

El proyecto sirve el CSS desde el repositorio:

```
/var/www/cabanaslaslengas/assets/css/style.css
→ https://cabanaslaslengas.cl/assets/css/style.css
```

**Si en tu servidor el CSS está guardado en otra ruta** (fuera del proyecto),
tienes dos opciones:

1. **Recomendado:** dejar que Nginx sirva los estáticos desde la raíz del
   proyecto (config incluida). Al hacer `git pull` el CSS se actualiza solo.
2. Si debes mantener la ruta externa, copiar el archivo después de cada deploy:

   ```bash
   # Ejemplo: servidor guarda el CSS en /var/www/html/assets/css/
   sudo rsync -av /var/www/cabanaslaslengas/assets/css/style.css \
                /var/www/html/assets/css/style.css
   sudo chown www-data:www-data /var/www/html/assets/css/style.css
   ```

   O servir esa ruta con Nginx:

   ```nginx
   location /assets/ {
       alias /RUTA_DONDE_ESTA_TU_CSS/;
   }
   ```

**Caché del navegador:** la config de Nginx cachea CSS/JS 7 días. Después de un
deploy, recarga con `Ctrl + F5`. Si necesitas invalidar la caché para todos,
cambia el nombre/versión del archivo o ajusta `expires` en la config.

> Cambios recientes de CSS que deben estar presentes: reglas de proporción de
> imágenes (`cabin-card-img` 4:3, `cabin-hero-img` 16:9) en
> `assets/css/style.css` e `includes/cabin_page.php`.

---

## 11. Respaldos

```bash
# Base de datos
sudo mysqldump --default-character-set=utf8mb4 laslengas \
  | gzip > /root/backup-laslengas-$(date +%F).sql.gz

# Imágenes subidas
sudo tar -czf /root/backup-uploads-$(date +%F).tar.gz \
  -C /var/www/cabanaslaslengas admin/assets/uploads

# Proyecto (opcional)
sudo tar -czf /root/backup-proyecto-$(date +%F).tar.gz \
  -C /var/www cabanaslaslengas
```

Programar con `cron` (ejemplo diario 03:00) y conservar al menos 7 copias.

---

## 12. Notas de seguridad

- `.env` solo en el servidor (permisos `640`, dueño deploy:www-data).
- Nginx bloquea `.env`, `.git`, logs y `.sql`; Apache ya no aplica `.htaccess`.
- Usar HTTPS obligatorio (Certbot renueva automáticamente).
- La `deploy key` es de solo lectura: el servidor no puede modificar el repo.
- Si alguna credencial estuvo expuesta, rotarla (BD, SMTP, reCAPTCHA).
