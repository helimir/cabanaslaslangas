<?php
// ============================================================
// MODELO INFORMATIVO COMÚN DE CABAÑA - CABAÑAS LAS LENGAS
// ------------------------------------------------------------
// Todas las cabañas comparten este mismo modelo; la información
// variable (nombre, descripción, precio, fotos, galería, etc.)
// se lee desde la base de datos usando el $slug recibido.
//
// Es incluido por los archivos físicos cabana-{slug}.php que
// genera automáticamente el panel de administración.
// ============================================================

require_once __DIR__ . '/../config_public.php';

$slug = isset($slug) ? (string) $slug : '';

// Obtener datos de la cabaña
$cabin = getCabinBySlug($slug);

if (!$cabin) {
    header('Location: index.php');
    exit;
}

// Galería propia de la cabaña (categoría = slug)
$cabin_gallery = getGalleryByCategory($slug);

// Textos según idioma
$cabin_name        = $lang === 'es' ? $cabin['name_es'] : $cabin['name_en'];
$cabin_description = $lang === 'es' ? $cabin['description_es'] : $cabin['description_en'];
$bed_config        = $lang === 'es' ? $cabin['bed_config_es'] : $cabin['bed_config_en'];
$cabin_price       = $lang === 'es'
    ? '$ ' . number_format((float) $cabin['price'], 0, ',', '.')
    : '$ ' . number_format((float) $cabin['dolar'], 0, '.', ',');

// Imagen principal de respaldo para el slider (usa portada si no hay principal)
$image_main_url = publicImageUrl($cabin['image_main'] ?? '');
if (empty($cabin['image_main']) || $image_main_url === SITE_URL . 'assets/img/logolengas.png') {
    $image_main_url = publicImageUrl($cabin['image_path'] ?? '');
}
$cover_url      = publicImageUrl($cabin['image_path'] ?? '');

// Datos de contacto / configuración
$contact_phone    = getSetting('contact_phone');
$contact_email    = getSetting('contact_email');
$contact_address  = getSetting('contact_address');
$facebook_url     = getSetting('facebook_url');
$instagram_url    = getSetting('instagram_url');
$whatsapp_number  = getSetting('whatsapp_number');
$site_title_es    = getSetting('site_title_es');

// Mobile
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isMobile  = (bool) preg_match('/mobile/i', $useragent);

$send = $_GET['send'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($cabin_name) ?> - <?= htmlspecialchars($site_title_es ?: 'Cabañas Las Lengas') ?></title>
    <meta content="" name="description">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/flor2.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
    .floatm1 { position:fixed; width:50px; height:50px; bottom:530px; right:0%; background:#25d366; color:#FFFFFF !important; text-align:center; font-size:30px; z-index:100; }
    .floatm2 { position:fixed; width:50px; height:50px; bottom:480px; right:0%; background:#0F90F3; color:#FFFFFF !important; text-align:center; font-size:30px; z-index:100; }
    .floatm3 { position:fixed; width:50px; height:50px; bottom:430px; right:0%; background:#FF00FF; color:#FFFFFF !important; text-align:center; font-size:30px; z-index:100; }
    .float { position:fixed; width:130px; height:40px; bottom:190px; right:0%; background:#25d366; color:#FFFFFF !important; text-align:left; font-size:15px; padding: 6px 1% 0 1%; border-top-left-radius: 8px; z-index:100; }
    .float2 { position:fixed; width:130px; height:40px; bottom:150px; right:0%; background:#0F90F3; color:#FFFFFF !important; text-align:left; font-size:15px; padding: 6px 1% 0 1%; z-index:100; }
    .float3 { position:fixed; width:130px; height:40px; bottom:110px; right:0%; background:#FF00FF; color:#FFFFFF !important; text-align:left; font-size:15px; padding: 6px 1% 0 1%; border-bottom-left-radius: 8px; z-index:100; }
    .my-float { margin-top:14px; }
    .my-float2 { margin-top:14px; }
    .my-float3 { margin-top:14px; }
    /* Las imágenes del slider se recortan a 16:9 para que todas se vean iguales */
    .cabin-hero-img {
        width: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        object-position: center;
        border-radius: 10px;
        display: block;
    }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-phone d-flex align-items-center"><span><?= htmlspecialchars($contact_phone) ?></span></i>
            </div>
            <div class="languages d-none d-md-flex align-items-center">
                <ul>
                    <li><a href="cabana-<?= htmlspecialchars($slug) ?>.php?lan=es">Español</a></li>
                    <li><a href="cabana-<?= htmlspecialchars($slug) ?>.php?lan=en">English</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-lg-between">
            <a href="index.php" class="logo me-auto me-lg-0">
                <img src="assets/img/logo3.png" alt="" class="img-fluid"/>
            </a>
            <nav id="navbar" class="navbar order-last order-lg-0">
                <ul>
                    <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                    <li><a class="nav-link scrollto" href="index.php#about">Puerto Natales</a></li>
                    <li><a class="nav-link scrollto" href="index.php#why-us"><?= $lang === 'es' ? 'Nosotros' : 'About' ?></a></li>
                    <li><a class="nav-link scrollto" href="index.php#menu"><?= $lang === 'es' ? 'Equipamiento' : 'Equipment' ?></a></li>
                    <li><a class="nav-link scrollto" href="index.php#cabanas"><?= $lang === 'es' ? 'Cabañas' : 'Cabins' ?></a></li>
                    <li><a class="nav-link scrollto" href="index.php#gallery"><?= $lang === 'es' ? 'Galerías' : 'Gallery' ?></a></li>
                    <li><a class="nav-link scrollto" href="#contact"><?= $lang === 'es' ? 'Contacto' : 'Contact' ?></a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <a href="#book-a-table" class="book-a-table-btn scrollto d-none d-lg-flex">
                <?= $lang === 'es' ? 'Reservaciones' : 'Reservations' ?>
            </a>
        </div>
    </header>

    <main id="main">

        <!-- Breadcrumbs -->
        <section id="breadcrumbs" class="breadcrumbs">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><?= htmlspecialchars($cabin_name) ?></h2>
                    <ol>
                        <li><a href="index.php"><?= $lang === 'es' ? 'Inicio' : 'Home' ?></a></li>
                        <?php foreach (getCabins() as $c): ?>
                            <?php if ($c['id'] == $cabin['id']) { continue; } ?>
                            <li><a href="cabana-<?= htmlspecialchars($c['slug']) ?>.php"><?= htmlspecialchars($lang === 'es' ? $c['name_es'] : $c['name_en']) ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Portfolio Details -->
        <section id="portfolio-details" class="portfolio-details">
            <div class="container">
                <div class="row gy-4">

                    <!-- Slider -->
                    <div class="col-lg-8">
                        <div class="events-slider swiper" data-aos="fade-up" data-aos-delay="100">
                            <div class="swiper-wrapper">
                                <?php if (!empty($cabin_gallery)): ?>
                                    <?php foreach ($cabin_gallery as $img): ?>
                                        <div class="swiper-slide">
                                            <div class="row event-item">
                                                <div class="col-lg-12">
                                                    <img src="<?= htmlspecialchars(publicImageUrl($img['image_path'])) ?>" class="cabin-hero-img" alt="<?= htmlspecialchars($cabin_name) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="swiper-slide">
                                        <div class="row event-item">
                                            <div class="col-lg-12">
                                                <img src="<?= htmlspecialchars($image_main_url) ?>" class="cabin-hero-img" alt="<?= htmlspecialchars($cabin_name) ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="col-lg-4">
                        <div style="background: #1D1B16;" class="portfolio-info">
                            <h3><?= $lang === 'es' ? 'Información' : 'Information' ?></h3>
                            <ul>
                                <li><i style="color: #A9AD00;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= $lang === 'es' ? 'Desayuno Incluido' : 'Breakfast included' ?></li>
                                <li><i style="color: #A9AD00;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= $lang === 'es' ? 'Mínimo (2) Noches' : 'Minimum (2) nights' ?></li>
                                <?php if (!empty($bed_config)): ?>
                                    <li><i style="color: #A9AD00;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= htmlspecialchars($bed_config) ?></li>
                                <?php endif; ?>
                                <?php if (!empty($cabin['max_people'])): ?>
                                    <li><i style="color: #A9AD00;" class="bi bi-people"></i>&nbsp;&nbsp;<?= $lang === 'es' ? 'Hasta' : 'Up to' ?> <?= (int) $cabin['max_people'] ?> <?= $lang === 'es' ? 'personas' : 'people' ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div style="background: #1D1B16;margin-top:5%;padding:4%;" class="portfolio-description">
                            <h2>
                                <?= $lang === 'es' ? 'Precio' : 'Price' ?>
                                <strong style="color: #A9AD00;"><?= htmlspecialchars($cabin_price) ?></strong>
                                <span style="font-size:12px">(<?= $lang === 'es' ? 'por noche' : 'per night' ?>)</span>
                            </h2>
                            <p style="text-align: left;">
                                <?= nl2br(htmlspecialchars((string) $cabin_description)) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Equipamiento -->
        <?php $amenities = getCabinAmenities((int) $cabin['id']); ?>
        <?php if (!empty($amenities)): ?>
        <section id="menu" class="menu section-bg">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang === 'es' ? 'Equipamiento' : 'Equipment' ?></h2>
                    <p><?= $lang === 'es' ? 'Incluye esta cabaña' : 'Included in this cabin' ?></p>
                </div>

                <div class="row menu-container">
                    <?php foreach ($amenities as $a): ?>
                        <div class="col-lg-6 menu-item filter-starters">
                            <?php if (!empty($a['image_path']) && is_file(UPLOADS_PATH . basename($a['image_path']))): ?>
                                <img src="<?= htmlspecialchars(UPLOADS_URL . basename($a['image_path'])) ?>" class="menu-img" alt="">
                            <?php elseif (!empty($a['icon'])): ?>
                                <div class="menu-img" style="display:flex;align-items:center;justify-content:center;background:rgba(169,173,0,0.1);border-radius:50%;">
                                    <i class="<?= htmlspecialchars($a['icon']) ?>" style="font-size:2rem;color:#A9AD00;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="menu-content">
                                <h4><?= htmlspecialchars($lang === 'es' ? $a['name_es'] : $a['name_en']) ?></h4>
                            </div>
                            <div class="menu-ingredients">
                                <?= htmlspecialchars((string) ($lang === 'es' ? $a['description_es'] : $a['description_en'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Reservas -->
        <section id="book-a-table" class="book-a-table">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang === 'es' ? 'Reservaciones' : 'Reservations' ?></h2>
                    <p><?= $lang === 'es' ? 'Reserva ' . htmlspecialchars($cabin_name) : 'Reserve ' . htmlspecialchars($cabin_name) ?></p>
                </div>

                <form action="forms/contacto.php" method="post" role="form" class="php-email-form">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 form-group">
                            <label><?= $lang === 'es' ? 'Nombre' : 'Name' ?></label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-lg-4 col-md-6 form-group mt-3 mt-md-0">
                            <label>Email</label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-lg-4 col-md-6 form-group mt-3 mt-md-0">
                            <label><?= $lang === 'es' ? 'Fono' : 'Phone' ?></label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang === 'es' ? 'Fecha Ingreso' : 'Check-in' ?></label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="date" name="date1" class="form-control" id="date1" required>
                        </div>
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang === 'es' ? 'Fecha Salida' : 'Check-out' ?></label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="date" name="date2" class="form-control" id="date2" required>
                        </div>
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang === 'es' ? 'Número de Personas' : 'Number of People' ?></label>
                            <input style="color:#FFFFFF!important;background-color:#333;" type="number" class="form-control" name="people" min="1" max="<?= (int) $cabin['max_people'] ?>" required>
                        </div>
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang === 'es' ? 'Cabaña' : 'Cabin' ?></label>
                            <select style="color:#FFFFFF!important;background-color:#333;" class="form-control" name="cabin" required>
                                <?php foreach (getCabins() as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= $c['id'] == $cabin['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($lang === 'es' ? $c['name_es'] : $c['name_en']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label><?= $lang === 'es' ? 'Mensaje' : 'Message' ?></label>
                        <textarea style="color:#FFFFFF!important;background-color:#333;" class="form-control" name="message" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="loading">Loading</div>
                        <?php if ($send === 'false'): ?>
                            <div style="display:block!important;" class="error-message">
                                <?= $lang === 'es' ? 'Reserva no pudo ser enviada. Favor vuelva a intentar.' : 'Reservation could not be sent. Please try again.' ?>
                            </div>
                        <?php elseif ($send !== ''): ?>
                            <div style="display:block!important;" class="sent-message">
                                <?= $lang === 'es' ? 'Se envió su solicitud. ¡Gracias!' : 'Your request has been sent. Thanks!' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                    <div class="text-center">
                        <button type="submit"><?= $lang === 'es' ? 'Enviar Reserva' : 'Send Reservation' ?></button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Contacto -->
        <section id="contact" class="contact">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang === 'es' ? 'Contacto' : 'Contact' ?></h2>
                    <p><?= $lang === 'es' ? 'Contáctanos' : 'Contact us' ?></p>
                </div>
            </div>
            <div class="container" data-aos="fade-up">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="email">
                                <i class="bi bi-geo-alt"></i>
                                <h4><?= $lang === 'es' ? 'Dirección:' : 'Address:' ?></h4>
                                <p><?= htmlspecialchars($contact_address) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="email">
                                <i class="bi bi-envelope"></i>
                                <h4>Email:</h4>
                                <p><?= htmlspecialchars($contact_email) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="phone">
                                <i class="bi bi-phone"></i>
                                <h4><?= $lang === 'es' ? 'Fono:' : 'Phone:' ?></h4>
                                <p><?= htmlspecialchars($contact_phone) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div data-aos="fade-up">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4945.660049208189!2d-72.51073956970608!3d-51.69955553838408!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xbdafe963400642ab%3A0x9b83512b36c9b3c4!2sCaba%C3%B1as%20Las%20Lengas!5e0!3m2!1ses-419!2scl!4v1639341101770!5m2!1ses-419!2scl" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-info">
                            <h3 class="logo"><a href="index.php"><?= $lang === 'es' ? 'Cabañas Las Lengas' : 'Cabins The Lengas' ?></a></h3>
                            <p>
                                <?= htmlspecialchars($contact_address) ?><br/><br/>
                                <strong><?= $lang === 'es' ? 'Fono:' : 'Phone:' ?></strong> <?= htmlspecialchars($contact_phone) ?><br/>
                                <strong>Email:</strong> <?= htmlspecialchars($contact_email) ?><br>
                            </p>
                            <div class="social-links mt-3">
                                <a href="<?= htmlspecialchars($facebook_url) ?>" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
                                <a href="<?= htmlspecialchars($instagram_url) ?>" class="instagram" target="_blank"><i class="bx bxl-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-links">
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php"><?= $lang === 'es' ? 'Inicio' : 'Home' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#about">Puerto Natales</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#why-us"><?= $lang === 'es' ? 'Nosotros' : 'About' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#menu"><?= $lang === 'es' ? 'Equipamiento' : 'Equipment' ?></a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 footer-links">
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#cabanas"><?= $lang === 'es' ? 'Cabañas' : 'Cabins' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#gallery"><?= $lang === 'es' ? 'Galería' : 'Gallery' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#contact"><?= $lang === 'es' ? 'Contacto' : 'Contact' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#book-a-table"><?= $lang === 'es' ? 'Reservaciones' : 'Reservations' ?></a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6 footer-newsletter text-center">
                        <img src="assets/img/logolengas.png" width="200" height="200" alt="Cabañas Las Lengas" />
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                &reg; <strong style="color:#A9AD00;"><span><?= $lang === 'es' ? 'Cabañas Las Lengas' : 'Cabins The Lengas' ?></span></strong>. <?= date('Y') ?>
            </div>
            <div class="credits">
                <?= $lang === 'es' ? 'Puerto Natales. Patagonia de Chile.' : 'Port Natales. Patagonia of Chile.' ?>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <!-- Botones flotantes -->
    <?php if ($isMobile): ?>
        <a href="https://api.whatsapp.com/send?phone=<?= htmlspecialchars($whatsapp_number) ?>&text=Hola" class="floatm1" target="_blank"><i class="bi bi-whatsapp my-float"></i></a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="floatm2" target="_blank"><i class="bi bi-facebook my-float2"></i></a>
        <a href="<?= htmlspecialchars($instagram_url) ?>" class="floatm3" target="_blank"><i class="bi bi-instagram my-float3"></i></a>
    <?php else: ?>
        <a href="https://web.whatsapp.com/send?phone=<?= htmlspecialchars($whatsapp_number) ?>&text=Hola" class="float" target="_blank"><i class="bi bi-whatsapp my-float"></i> WhatsApp</a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="float2" target="_blank"><i class="bi bi-facebook my-float2"></i> Facebook</a>
        <a href="<?= htmlspecialchars($instagram_url) ?>" class="float3" target="_blank"><i class="bi bi-instagram my-float3"></i> Instagram</a>
    <?php endif; ?>

    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
