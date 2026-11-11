# Correo con SendGrid

El sitio envía los correos de reservas (cliente y administrador) mediante
**PHPMailer**. En un VPS con **Hetzner**, la mejor opción es **SendGrid por su
API HTTPS (puerto 443)**: no depende de puertos SMTP y evita el bloqueo del
puerto 25.

> El envío está implementado en `includes/mailer.php` y se prueba con
> `php tools/test_mail.php correo@destino.cl`.

---

## 1. Cuenta y remitente verificado en SendGrid

1. Crear cuenta en <https://signup.sendgrid.com/>.
2. Verificar el **remitente** en **Settings → Sender Authentication**:
   - **Single Sender Verification** (rápido): agregar el correo remitente
     (ej. `reservaciones@cabanaslaslengas.cl`), completar datos y confirmar el
     email que envía SendGrid.
   - **Domain Authentication** (recomendado para producción): autenticar el
     dominio `cabanaslaslengas.cl` agregando los registros DNS (CNAME) que
     entrega SendGrid. Esto configura SPF/DKIM y mejora la entregabilidad.
3. El correo configurado en `MAIL_FROM_ADDRESS` **debe coincidir** con el
   remitente verificado o con el dominio autenticado.

---

## 2. Crear la API Key

SendGrid → **Settings → API Keys → Create API Key**:

- Nombre: `Cabanas Las Lengas - VPS`
- Permisos: **Restricted Access → Mail Send: Full Access** (solo eso)
- Copiar la clave (`SG.xxxxx...`); no se vuelve a mostrar.

---

## 3. Configurar el `.env` del servidor (modo API)

En `/www/wwwroot/cabanaslaslengas.cl/.env` (aaPanel) o
`/var/www/cabanaslaslengas/.env`:

```ini
MAIL_DRIVER=sendgrid
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=reservaciones@cabanaslaslengas.cl
MAIL_FROM_NAME="Cabañas Las Lengas"
MAIL_ADMIN_ADDRESS=reservaciones@cabanaslaslengas.cl
```

No hace falta `MAIL_HOST` ni `MAIL_PORT` en este modo. El sitio usa:

- Endpoint: `https://api.sendgrid.com/v3/mail/send`
- Autenticación: `Authorization: Bearer SENDGRID_API_KEY`
- Puerto: **443 (HTTPS)** → no lo bloquea Hetzner.

> El `.env` no está en Git: se edita solo en el servidor. Después de guardarlo
> no hay que reiniciar PHP (se lee en cada petición).

---

## 4. Probar el envío

```bash
cd /www/wwwroot/cabanaslaslengas.cl
php tools/test_mail.php correo@destino.cl
```

- ✅ `CORREO ENVIADO` → listo; envía una reserva de prueba desde el sitio.
- ❌ Error → el script muestra el código HTTP y el mensaje de SendGrid.

Después de una reserva real, si el correo falla, el número de reserva y el error
quedan en el log de PHP. La reserva **igual se guarda** en la base de datos.

---

## 5. Alternativa: SMTP de SendGrid

Si prefieres SMTP (también funciona en Hetzner; solo el puerto 25 está
bloqueado):

```ini
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=reservaciones@cabanaslaslengas.cl
MAIL_FROM_NAME="Cabañas Las Lengas"
MAIL_ADMIN_ADDRESS=reservaciones@cabanaslaslengas.cl
```

- Host: `smtp.sendgrid.net`
- Usuario: literalmente `apikey`
- Contraseña: la API key de SendGrid
- Puertos: `587` (tls), `465` (ssl) o `2525` (tls)

---

## 6. Errores frecuentes

| Error / síntoma | Causa | Solución |
|---|---|---|
| `HTTP 401 Unauthorized` | API key inválida o revocada | Crear una nueva API key y actualizar `.env` |
| `HTTP 403 The from address does not match a verified Sender Identity` | Remitente no verificado | Verificar `MAIL_FROM_ADDRESS` en Sender Authentication |
| `HTTP 400` con errores de `personalizations` | Formato/destinatario inválido | Revisar el correo destino |
| No llega pero la API responde 202 | Spam / reputación / DNS | Revisar SPAM, completar Domain Authentication (SPF/DKIM) |
| `cURL PHP NO disponible` | Falta extensión curl | Instalar/habilitar `php-curl` en aaPanel y reiniciar PHP |
| `Falta SENDGRID_API_KEY en .env` | Variable vacía | Agregar la API key en `.env` |

Revisar también el **Activity Feed** de SendGrid (Activity → Email Activity) para
ver si el correo fue entregado, rebotado o marcado como spam.

---

## 7. Buenas prácticas

- Completar **Domain Authentication** (SPF/DKIM) en SendGrid para buena
  entregabilidad.
- Usar un remitente real del dominio (`reservaciones@...`), no un correo
  personal.
- Plan gratuito de SendGrid: límite de correos por día; para volumen alto,
  revisar el plan.
- Si un correo rebota, SendGrid lo reporta en Activity; no hay reintentos
  automáticos desde el sitio.
- Nunca subir la API key a Git (`.env` está en `.gitignore`).
