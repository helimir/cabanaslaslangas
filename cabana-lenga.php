<?php 
session_start();
require_once 'admin/includes/config.php';
require_once 'admin/includes/functions.php';

// Configurar idioma
if ($_GET['lan']=='es' or $_GET['lan']=='') {
    $_SESSION['lan']='es';    
} else {
    $_SESSION['lan']='en';
}
$lang = $_SESSION['lan'];
$send = $_GET['send'] ?? '';

// Obtener el slug de la URL (coihue, cipres, lenga)
$slug = 'lenga'; // Cambia según la página

// Obtener datos de la cabaña desde la BD
$cabin = getCabinBySlug($slug);
if (!$cabin) {
    header('Location: index.php');
    exit;
}

// Variables dinámicas
$title = $lang == 'es' ? $cabin['name_es'] : $cabin['name_en'];
$description = $lang == 'es' ? $cabin['description_es'] : $cabin['description_en'];
$price = $cabin['price'];
$max_people = $cabin['max_people'];
$bed_config = $lang == 'es' ? $cabin['bed_config_es'] : $cabin['bed_config_en'];
$image_main = $cabin['image_main'] ? 'assets/img/' . $cabin['image_main'] : 'assets/img/default-cabin.jpg';

// Obtener imágenes de la galería para esta cabaña
$gallery_images = getGalleryImagesByCategory($slug);
$amenities = getAmenities();

// Configurar el breadcrumb
$breadcrumb_items = [
    ['url' => 'index.php', 'label' => $lang == 'es' ? 'Inicio' : 'Home'],
    ['url' => '#', 'label' => $title]
];

// Obtener información adicional de la cabaña (checklist)
$cabin_features = [
    'breakfast' => ['label_es' => 'Desayuno Incluido', 'label_en' => 'Breakfast included'],
    'min_nights' => ['label_es' => 'Mínimo (2) Noches', 'label_en' => 'Minimum (2) nights'],
    'bed_config' => ['label_es' => $bed_config, 'label_en' => $bed_config]
];

// Si es la cabaña Lenga, agregar la cama adicional
if ($slug == 'lenga') {
    $cabin_features['extra_bed'] = [
        'label_es' => '(02) Cama Twin Size (1 ½ Plaza)',
        'label_en' => '(02) Twin Size bed (1 ½ square)'
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($title) ?> - Cabañas Las Lengas</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/flor2.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        .floatm1 {   
            position:fixed;
            width:50px;
            height:50px;
            bottom:530px;
            right:0%;
            background:#25d366;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:center;
            font-size:30px;
            z-index:100;
        }
        .my-floatm1 { margin-top:12px; }
        .floatm2 {   
            position:fixed;
            width:50px;
            height:50px;
            bottom:480px;
            right:0%;
            background:#0F90F3;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:center;
            font-size:30px;
            z-index:100;
        }
        .my-floatm2 { margin-top:12px; }
        .floatm3 {   
            position:fixed;
            width:50px;
            height:50px;
            bottom:430px;
            right:0%;
            background:#FF00FF;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:center;
            font-size:30px;
            z-index:100;
        }
        .my-floatm3 { margin-top:14px; }

        .float {   
            position:fixed;
            width:130px;
            height:40px;
            bottom:190px;
            right:0%;
            background:#25d366;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:left;
            font-size:15px;
            padding-right: 1%;
            padding-top: 0.5%;
            padding-left: 1%;
            border-top-left-radius: 8px;
            z-index:100;
        }
        .float:hover {
            width:150px;
            border-bottom-left-radius: 8px;
            font-size:18px;
        }
        .float2 {   
            position:fixed;
            width:130px;
            height:40px;
            bottom:150px;
            right:0%;
            background:#0F90F3;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:left;
            font-size:15px;
            padding-top: 0.5%;
            padding-right: 1%;
            padding-left: 1%;
            font-style: normal !important;
            z-index:100;
        }
        .float2:hover {
            width:150px;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            font-size:18px;
        }
        .float3 {   
            position:fixed;
            width:130px;
            height:40px;
            bottom:110px;
            right:0%;
            background:#FF00FF;
            color:#FFFFFF !important;
            border-radius:0px;
            text-align:left;
            font-size:15px;
            padding-right: 1%;
            padding-top: 0.5%;
            padding-left: 1%;
            border-bottom-left-radius: 8px;
            z-index:100;
        }
        .float3:hover {
            width:150px;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            font-size:18px;
        }
        .my-float { margin-top:14px; }
        .my-float2 { margin-top:14px; }
        .my-float3 { margin-top:14px; }

        .boton_personalizado {
            text-decoration: none;
            padding: 5px;
            font-weight: 300;
            font-size: 20px;
            color: #282828 !important;
            border-radius: 6px;
            border: 1px solid #282828 !important;
            background: transparent;
        }
        .boton_personalizado:hover {
            color: #1883ba;
            background-color: #ffffff;
        }

        /* Estilos para el checklist de características */
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            padding: 6px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list li i {
            color: #A9AD00;
            font-size: 1.2rem;
            width: 24px;
        }
        .feature-list li .feature-label {
            color: #fff;
            font-size: 0.95rem;
        }

        /* Estilos para formularios */
.form-control-dark {
    color: #FFFFFF !important;
    background-color: #333 !important;
    border: 1px solid #555 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    width: 100% !important;
}

.form-control-dark:focus {
    border-color: #f8c146 !important;
    box-shadow: 0 0 0 3px rgba(248, 193, 70, 0.2) !important;
    outline: none !important;
}

.form-control-dark::placeholder {
    color: #888 !important;
}

/* Estilo específico para selects */
.form-control-dark select {
    appearance: auto !important;
    -webkit-appearance: auto !important;
    -moz-appearance: auto !important;
}

.form-control-dark option {
    color: #FFFFFF !important;
    background-color: #333 !important;
    padding: 8px !important;
}

/* Estilo para labels */
.form-group label {
    color: #ccc !important;
    font-weight: 500 !important;
    margin-bottom: 6px !important;
    display: block !important;
}


    </style>

    <script>
        function lan(id) {
            if (id==1) {
                window.location.href='cabana-<?= $slug ?>.php?lan=es';
            } else {
                window.location.href='cabana-<?= $slug ?>.php?lan=en';
            }
        }

        function valor1() {
            var f1 = document.getElementById('date1').value;
            var f2 = document.getElementById('date2').value;
            if (f1 == '') {
                alert('<?= $lang == 'es' ? 'Fecha ingreso no puede estar vacía' : 'Check-in date cannot be empty' ?>');
                return false;
            }
            if (f2 == '') {
                alert('<?= $lang == 'es' ? 'Fecha salida no puede estar vacía' : 'Check-out date cannot be empty' ?>');
                return false;
            }
            if (f1 > f2) {
                alert('<?= $lang == 'es' ? 'Fecha salida no puede ser menor a fecha de ingreso' : 'Check-out cannot be before check-in' ?>');
                return false;
            }
            if (f1 == f2) {
                alert('<?= $lang == 'es' ? 'Las fechas no pueden ser iguales' : 'Dates cannot be the same' ?>');
                return false;
            }
            return true;
        }
    </script>
</head>

<body>

    <!-- ======= Top Bar ======= -->
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-phone d-flex align-items-center"><span>(+56) 9-93499820</span></i>
            </div>
            <div class="languages d-none d-md-flex align-items-center">
                <ul>
                    <li><a href="#" onclick="lan(1)">Español</a></li>
                    <li><a href="#" onclick="lan(2)">English</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-lg-between">
            <a href="index.php" class="logo me-auto me-lg-0"><img src="assets/img/logo3.png" alt="" class="img-fluid"/></a>
            <nav id="navbar" class="navbar order-last order-lg-0">
                <ul>
                    <?php if ($lang == 'es') { ?>
                        <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                        <li><a class="nav-link scrollto" href="index.php#about">Puerto Natales</a></li>
                        <li><a class="nav-link scrollto" href="index.php#why-us">Nosotros</a></li>
                        <li><a class="nav-link scrollto" href="index.php#menu">Equipamiento</a></li>
                        <li><a class="nav-link scrollto" href="index.php#cabanas">Cabañas</a></li>
                        <li><a class="nav-link scrollto" href="index.php#gallery">Galerias</a></li>
                        <li><a class="nav-link scrollto" href="index.php#contacto">Contacto</a></li>
                    <?php } else { ?>
                        <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                        <li><a class="nav-link scrollto" href="index.php#about">Puerto Natales</a></li>
                        <li><a class="nav-link scrollto" href="index.php#why-us">About</a></li>
                        <li><a class="nav-link scrollto" href="index.php#menu">Equipment</a></li>
                        <li><a class="nav-link scrollto" href="index.php#cabanas">Cabins</a></li>
                        <li><a class="nav-link scrollto" href="index.php#gallery">Gallery</a></li>
                        <li><a class="nav-link scrollto" href="index.php#contacto">Contact</a></li>
                    <?php } ?>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <?php if ($lang == 'es') { ?>
                <a href="#contacto" class="book-a-table-btn scrollto d-none d-lg-flex">Reservaciones</a>
            <?php } else { ?>
                <a href="#contacto" class="book-a-table-btn scrollto d-none d-lg-flex">Reservations</a>
            <?php } ?>
        </div>
    </header>

    <main id="main">

      <!-- ======= Breadcrumbs ======= -->
      <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
        <?php if ($_SESSION['lan']=='es') { ?>   
          <div class="d-flex justify-content-between align-items-center">
            <h2>Caba&ntilde;a Lenga</h2>
            <ol>
              <li><a href="index.php">Inicio</a></li>
              <li><a href="cabana-coihue.php">Caba&ntilde;ana Coihue</a></li>
              <li><a href="cabana-cipres.php">Caba&ntilde;ana Cipres</a></li>
            </ol>
          </div>
        <?php } else { ?>
          <div class="d-flex justify-content-between align-items-center">
            <h2>Cabin Lenga</h2>
            <ol>
              <li><a href="index.php">Inicio</a></li>
              <li><a href="cabana-coihue.php">Cabin Coihue</a></li>
              <li><a href="cabana-cipres.php">Cabin Cipres</a></li>
            </ol>
          </div>
        <?php } ?>  

        </div>
      </section><!-- End Breadcrumbs -->

        <!-- ======= Portfolio Details Section ======= -->
        <section id="portfolio-details" class="portfolio-details">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-8">
                        <div class="events-slider swiper" data-aos="fade-up" data-aos-delay="100">
                            <div class="swiper-wrapper">                                
                                <!-- Imágenes de la galería de esta cabaña -->
                                <?php foreach ($gallery_images as $image): ?>
                                    <div class="swiper-slide">
                                        <div class="row event-item">
                                            <div class="col-lg-12">
                                                <img src="<?= UPLOADS_URL . $image['image_path'] ?>" class="img-fluid" alt="<?= htmlspecialchars($lang == 'es' ? $image['caption_es'] : $image['caption_en']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <!-- Si no hay imágenes de galería, mostrar imágenes por defecto -->
                                <?php if (empty($gallery_images)): ?>
                                    <div class="swiper-slide">
                                        <div class="row event-item">
                                            <div class="col-lg-12">
                                                <img src="assets/img/<?= $slug ?>-1.png" class="img-fluid" alt="<?= htmlspecialchars($title) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="row event-item">
                                            <div class="col-lg-12">
                                                <img src="assets/img/<?= $slug ?>-2.png" class="img-fluid" alt="<?= htmlspecialchars($title) ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>

                    <!-- Información de la cabaña -->
                    <div class="col-lg-4">
                        <div style="background: #1D1B16;" class="portfolio-info">
                            <h3><?= $lang == 'es' ? 'Información' : 'Information' ?></h3>
                            <ul class="feature-list">
                                <?php foreach ($cabin_features as $feature): ?>
                                    <li>
                                        <i class="bi bi-check-circle"></i>
                                        <span class="feature-label"><?= $lang == 'es' ? $feature['label_es'] : $feature['label_en'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div style="background: #1D1B16;margin-top: 5%;padding: 4%;" class="portfolio-description">
                            <h2>
                                <?= $lang == 'es' ? 'Precio' : 'Price' ?>
                                <strong style="color: #A9AD00;">$ <?= number_format($price, 0, ',', '.') ?></strong>
                                <span style="font-size:12px">(<?= $lang == 'es' ? 'por noche' : 'per night' ?>)</span>
                            </h2>
                            <p style="text-align: left;">
                                <?= nl2br(htmlspecialchars($description)) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======= Equipamiento Section ======= -->
        <section id="menu" class="menu section-bg">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Equipamiento' : 'Equipment' ?></h2>
                    <?php if ($lang == 'es'): ?>
                        <p>Todas Incluyen</p>
                    <?php else: ?>
                        <p>All Include</p>
                    <?php endif; ?>
                </div>

                <div class="col-lg-12 pt-4 pt-lg-0 order-2 order-lg-1 content text-center">
                    <h3 style="font-family: arial;">
                        <?= $lang == 'es' 
                            ? 'Todas cuentan en su interior con 1 Hot Tub y calefacción central, <br />lo que permite una agradable estadía en invierno y verano, con una vista mágica hacia<br /> el Ventisquero Balmaceda, Canal Señoret y ciudad de Puerto Natales.'
                            : 'All have 1 Hot Hub inside and central heating, <br />which allows a pleasant stay in winter and summer, with a magical view towards<br /> the Ventisquero Balmaceda, Canal Señoret and the city of Port Natales.'
                        ?>
                    </h3>
                </div>

                <div class="row menu-container" data-aos="fade-up" data-aos-delay="200">
                    <?php foreach ($amenities as $amenity): ?>
                        <div class="col-lg-6 menu-item filter-starters">
                            <?php if ($amenity['image_path']): ?>
                                <img src="<?= UPLOADS_URL . $amenity['image_path'] ?>" class="menu-img" alt="<?= htmlspecialchars($lang == 'es' ? $amenity['name_es'] : $amenity['name_en']) ?>">
                            <?php elseif ($amenity['icon']): ?>
                                <div class="menu-img" style="display: flex; align-items: center; justify-content: center; width: 70px; height: 70px; background: rgba(169, 173, 0, 0.1); border-radius: 50%;">
                                    <i class="<?= $amenity['icon'] ?>" style="font-size: 2rem; color: #A9AD00;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="menu-content">
                                <h4 style="font-family: arial;"><?= $lang == 'es' ? $amenity['name_es'] : $amenity['name_en'] ?></h4>
                            </div>
                            <div style="font-family: arial;" class="menu-ingredients">
                                <?= $lang == 'es' ? $amenity['description_es'] : $amenity['description_en'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ======= Book A Table Section ======= -->
        <section id="contacto" class="book-a-table">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Reservaciones' : 'Reservations' ?></h2>
                    <p><?= $lang == 'es' ? 'Reserva una Cabaña' : 'Reserve a Cabin' ?></p>
                </div>

                <form action="forms/contacto.php" method="post" role="form" class="php-email-form" data-aos="fade-up" data-aos-delay="100">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-lg-4 col-md-6 form-group">
                            <label><?= $lang == 'es' ? 'Nombre' : 'Name' ?></label>
                            <input type="text" name="name" class="form-control form-control-dark" required>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-lg-4 col-md-6 form-group mt-3 mt-md-0">
                            <label>Email</label>
                            <input type="email" class="form-control form-control-dark" name="email" required>
                        </div>
                        
                        <!-- Teléfono -->
                        <div class="col-lg-4 col-md-6 form-group mt-3 mt-md-0">
                            <label><?= $lang == 'es' ? 'Fono' : 'Phone' ?></label>
                            <input type="text" class="form-control form-control-dark" name="phone" required>
                        </div>
                        
                        <!-- Fecha Ingreso -->
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang == 'es' ? 'Fecha Ingreso' : 'Check-in' ?></label>
                            <input type="date" name="date1" class="form-control form-control-dark" id="date1" required>
                        </div>
                        
                        <!-- Fecha Salida -->
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang == 'es' ? 'Fecha Salida' : 'Check-out' ?></label>
                            <input type="date" name="date2" class="form-control form-control-dark" id="date2" required>
                        </div>
                        
                        <!-- Número de Personas -->
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang == 'es' ? 'Número de Personas' : 'Number of People' ?></label>
                            <input type="number" class="form-control form-control-dark" name="people" id="people" min="1" required>
                        </div>
                        
                        <!-- Select de Cabañas -->
                        <div class="col-lg-3 col-md-6 form-group mt-3">
                            <label><?= $lang == 'es' ? 'Cabaña' : 'Cabin' ?></label>
                            <input type="text" class="form-control form-control-dark" name="cabin_nombre" value="<?= $lang == 'es' ? 'Lenga' : 'Lenga' ?>" readonly>
                        </div>
                    </div>
                    
                    <!-- Mensaje -->
                    <div class="form-group mt-3">
                        <label><?= $lang == 'es' ? 'Mensaje' : 'Message' ?></label>
                        <textarea class="form-control form-control-dark" name="message" rows="5"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="loading">Loading</div>
                        <?php if ($send == 'false'): ?>
                            <div style="display: block !important;" class="error-message">
                                <?= $lang == 'es' ? 'Reserva no pudo ser enviada. Favor vuelva a intentar.' : 'Reservation could not be sent. Please try again.' ?>
                            </div>
                        <?php elseif ($send != ''): ?>
                            <div style="display: block !important;" class="sent-message">
                                <?= $lang == 'es' ? 'Se envió su solicitud de reserva. Le devolveremos la llamada o le enviaremos un correo electrónico para confirmar su reserva. ¡Gracias!' : 'Your reservation request has been sent. We will call you back or email you to confirm your reservation. Thanks!' ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="lang" value="<?= $lang ?>">
                    <input type="hidden" name="cabin" value="3">
                    
                    <div class="text-center">
                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        <button type="submit" onclick="return valor1()">
                            <?= $lang == 'es' ? 'Enviar Reserva' : 'Send Reservation' ?>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- ======= Contact Section ======= -->
        <section id="contact" class="contact">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Contacto' : 'Contact' ?></h2>
                    <p><?= $lang == 'es' ? 'Contáctanos' : 'Contact us' ?></p>
                </div>
            </div>

            <div class="container" data-aos="fade-up">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="email">
                                <i class="bi bi-geo-alt"></i>
                                <h4><?= $lang == 'es' ? 'Dirección:' : 'Address:' ?></h4>
                                <p>Gabriel Silva (Camino 1) 132. Puerto Natales. Región del Magallanes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="email">
                                <i class="bi bi-envelope"></i>
                                <h4>Email:</h4>
                                <p>reservaciones@cabanaslaslengas.cl</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="info">
                            <div class="phone">
                                <i class="bi bi-phone"></i>
                                <h4><?= $lang == 'es' ? 'Fono:' : 'Phone:' ?></h4>
                                <p>(+56) 9-93499820</p>
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

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-info">
                            <h3 class="logo me-auto me-lg-0"><a href="index.php"><?= $lang == 'es' ? 'Cabañas Las Lengas' : 'Cabins The Lengas' ?></a></h3>
                            <p>
                                Gabriel Silva (Camino 1) 132. <?= $lang == 'es' ? 'Puerto Natales. Región del Magallanes' : 'Port Natales. Magallanes Region' ?><br/><br/>
                                <strong><?= $lang == 'es' ? 'Fono:' : 'Phone:' ?></strong> (+56) 9-93499820<br/>
                                <strong>Email:</strong> reservaciones@cabanaslaslengas.cl<br>
                            </p>
                            <div class="social-links mt-3">
                                <a href="https://www.facebook.com/Caba%C3%B1aslaslengas-102356472311320" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
                                <a href="https://www.instagram.com/cabanaslaslengas/" class="instagram" target="_blank"><i class="bx bxl-instagram"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 footer-links">
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php"><?= $lang == 'es' ? 'Inicio' : 'Home' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#about"><?= $lang == 'es' ? 'Puerto Natales' : 'Port Natales' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#why-us"><?= $lang == 'es' ? 'Nosotros' : 'About' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#menu"><?= $lang == 'es' ? 'Equipamiento' : 'Equipment' ?></a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#cabanas"><?= $lang == 'es' ? 'Cabañas' : 'Cabins' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#gallery"><?= $lang == 'es' ? 'Galería' : 'Gallery' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.php#contact"><?= $lang == 'es' ? 'Contacto' : 'Contact' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#book-a-table"><?= $lang == 'es' ? 'Reservaciones' : 'Reservations' ?></a></li>
                        </ul>
                    </div>

                    <div style="text-align: center;" class="col-lg-4 col-md-6 footer-newsletter">
                        <img src="assets/img/logolengas.png" width="200" height="200" />
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="copyright">
                &reg; <strong style="color:#A9AD00;"><span><?= $lang == 'es' ? 'Cabañas Las Lengas' : 'Cabins The Lengas' ?></span></strong>. 2026
            </div>
            <div class="credits">
                <?= $lang == 'es' ? 'Puertos Natales. Patagonia de Chile.' : 'Port Natales. Patagonia of Chile.' ?>
            </div>
        </div>
    </footer>

    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/cabanas/main.js"></script>
    <script src="assets/js/main.js"></script>

    <!-- Botones flotantes -->
    <?php $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match("/mobile/i", $useragent)) { ?>
        <a href="https://api.whatsapp.com/send?phone=56993499820&text=Hola, quisiera más información sobre las cabañas" class="floatm1" target="_blank"><i class="bi bi-whatsapp my-float"></i></a>
        <a href="https://www.facebook.com/Caba%C3%B1aslaslengas-102356472311320" class="floatm2" target="_blank"><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-facebook my-float2"></i></span></a>
        <a href="https://www.instagram.com/cabanaslaslengas/" class="floatm3" target="_blank"><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-instagram my-float3"></i></span></a>
    <?php } else { ?>
        <a href="https://web.whatsapp.com/send?phone=56993499820&text=Hola, quisiera más información sobre las cabañas" class="float" target="_blank" title="Consulte cualquier duda via whatsapp"><span><i class="bi bi-whatsapp my-float"></i> WhatsApp</span></a>
        <a href="https://www.facebook.com/Caba%C3%B1aslaslengas-102356472311320" class="float2" target="_blank"><span title="Facebook"><i class="bi bi-facebook my-float2"></i> Facebook</span></a>
        <a href="https://www.instagram.com/cabanaslaslengas/" class="float3" target="_blank"><span title="Instagram"><i class="bi bi-instagram my-float3"></i> Instagram</span></a>
    <?php } ?>

</body>
</html>