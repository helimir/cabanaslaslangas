<?php
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin/includes/config.php';
require_once 'admin/includes/functions.php';
require_once 'includes/cabin_grid.php';

// Configurar idioma
$lang = $_GET['lan'] ?? $_SESSION['lan'] ?? 'es';
if (!in_array($lang, ['es', 'en'], true)) {
    $lang = 'es';
}
$_SESSION['lan'] = $lang;
$send = $_GET['send'] ?? '';

// Obtener datos dinámicos de la BD
$cabins = getCabins();
$amenities = getAmenities();
$slides = getSlides();
$gallery_images = getGalleryImages('galeria');
$excursiones = getGalleryImagesByCategory('excursion');

// Obtener configuraciones
$site_title = getSetting('site_title_' . $lang);
$about_text = getSetting('about_text_' . $lang);
$pdf_tourism = getSetting('tourism_pdf_' . $lang);
$contact_phone = getSetting('contact_phone');
$contact_email = getSetting('contact_email');
$contact_address = getSetting('contact_address');
$video_url = getSetting('about_video_url');
$facebook_url = getSetting('facebook_url');
$instagram_url = getSetting('instagram_url');
$whatsapp_number = getSetting('whatsapp_number');

// Definir variables para el texto del hero
$hero_title = $lang == 'es' ? 'Cabañas Las Lengas' : 'Cabins Las Lengas';
$hero_subtitle = $lang == 'es' ? 'Estamos ubicados en la Patagonia Austral de Chile.' : 'We are located in the Southern Patagonia of Chile.';
$hero_btn_cabins = $lang == 'es' ? 'Cabañas' : 'Cabins';
$hero_btn_reservations = $lang == 'es' ? 'Reservaciones' : 'Reservations';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($site_title) ?></title>
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
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <meta name="google-site-verification" content="6Lf3zegiAAAAABwY9Qz8SvE35pRSDCf-U0DmYURP" />

    <script src='https://www.google.com/recaptcha/api.js?render=6Lf3zegiAAAAAAZlFCX0a0DxWIT31SXKtMsNXuAl'></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6Lf3zegiAAAAAAZlFCX0a0DxWIT31SXKtMsNXuAl', {action: 'submit'})
            .then(function(token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                if (recaptchaResponse) {
                    recaptchaResponse.value = token;
                }
            });
        });
    </script>

    <script>
        function lan(id) {
            if (id == 1) {
                window.location.href = 'index.php?lan=es';
            } else {
                window.location.href = 'index.php?lan=en';
            }
        }
    </script>

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

        /* Estilos para la sección de turismo */
        .events2 .event-item {
            padding: 30px;
            background: #1a1a2e;
            border-radius: 15px;
            color: #fff;
        }
        .events2 .event-item h3 {
            color: #f8c146;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .events2 .event-item p {
            color: #ccc;
            line-height: 1.8;
        }
        .events2 .event-item .price {
            border-bottom: 2px solid #f8c146;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .events2 .event-item .book-a-table-btn {
            background: #f8c146;
            color: #1a1a2e !important;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .events2 .event-item .book-a-table-btn:hover {
            background: #e6b03a;
            transform: translateY(-2px);
        }
        .events2 .swiper-pagination {
            margin-top: 20px;
            position: relative;
        }
        .events2 .swiper-pagination-bullet {
            background: #f8c146;
        }
        @media (max-width: 768px) {
            .events2 .event-item {
                padding: 20px;
            }
            .events2 .event-item h3 {
                font-size: 20px;
            }
        }

        /* Estilos para el Hero con Slider */
#hero {
    padding: 0;
    position: relative;
    overflow: hidden;
}

#hero .hero-slider-wrapper {
    position: relative;
    width: 100%;
    min-height: 100vh;
}

#hero .carousel-item {
    min-height: 100vh;
    transition: transform 0.6s ease-in-out;
}

#hero .carousel-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

#hero .carousel-item .container {
    position: relative;
    z-index: 2;
}

#hero .hero-title {
    color: #fff;
    font-size: 3.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
    animation: fadeInUp 1s ease;
}

#hero .hero-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.5rem;
    font-weight: 300;
    text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.3);
    animation: fadeInUp 1.2s ease;
}

#hero .btn-menu {
    background: #f8c146;
    color: #1a1a2e;
    padding: 12px 40px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin: 5px;
}

#hero .btn-menu:hover {
    background: #e6b03a;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(248, 193, 70, 0.3);
}

#hero .btn-book {
    background: transparent;
    color: #fff;
    padding: 12px 40px;
    border-radius: 50px;
    font-weight: 600;
    border: 2px solid #fff;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin: 5px;
}

#hero .btn-book:hover {
    background: #fff;
    color: #1a1a2e;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
}

/* Indicadores del carousel */
#hero .carousel-indicators {
    bottom: 30px;
    z-index: 3;
}

#hero .carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    background: transparent;
    margin: 0 6px;
    transition: all 0.3s ease;
}

#hero .carousel-indicators button.active {
    background: #f8c146;
    border-color: #f8c146;
}

/* Controles del carousel */
#hero .carousel-control-prev,
#hero .carousel-control-next {
    z-index: 3;
    width: 60px;
    height: 60px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    opacity: 0;
    transition: all 0.3s ease;
}

#hero:hover .carousel-control-prev,
#hero:hover .carousel-control-next {
    opacity: 1;
}

#hero .carousel-control-prev {
    left: 20px;
}

#hero .carousel-control-next {
    right: 20px;
}

#hero .carousel-control-prev-icon,
#hero .carousel-control-next-icon {
    width: 20px;
    height: 20px;
}

/* Play button */
#hero .play-btn {
    width: 80px;
    height: 80px;
    background: rgba(248, 193, 70, 0.9);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 30px;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
}

#hero .play-btn:hover {
    background: #f8c146;
    transform: scale(1.1);
    box-shadow: 0 0 40px rgba(248, 193, 70, 0.4);
}

#hero .play-btn::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid rgba(248, 193, 70, 0.6);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    #hero .hero-title {
        font-size: 2.5rem;
    }
    
    #hero .hero-subtitle {
        font-size: 1.2rem;
    }
    
    #hero .btn-menu,
    #hero .btn-book {
        padding: 10px 25px;
        font-size: 0.9rem;
        display: block;
        width: 100%;
        text-align: center;
    }
    
    #hero .carousel-control-prev,
    #hero .carousel-control-next {
        width: 40px;
        height: 40px;
        opacity: 1;
    }
    
    #hero .carousel-control-prev {
        left: 10px;
    }
    
    #hero .carousel-control-next {
        right: 10px;
    }
    
    #hero .play-btn {
        width: 60px;
        height: 60px;
        font-size: 20px;
    }
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
</head>

<body>

    <!-- ======= Top Bar ======= -->
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-phone d-flex align-items-center"><span><?= htmlspecialchars($contact_phone) ?></span></i>
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
                        <li><a class="nav-link scrollto active" href="#hero">Inicio</a></li>
                        <li><a class="nav-link scrollto" href="#about">Puerto Natales</a></li>
                        <li><a class="nav-link scrollto" href="#why-us">Nosotros</a></li>
                        <li><a class="nav-link scrollto" href="#menu">Equipamiento</a></li>
                        <li><a class="nav-link scrollto" href="#cabanas">Cabañas</a></li>
                        <li><a class="nav-link scrollto" href="#turismo">Turismo</a></li>
                        <li><a class="nav-link scrollto" href="#gallery">Galerias</a></li>
                        <li><a class="nav-link scrollto" href="#contact">Contacto</a></li>
                    <?php } else { ?>
                        <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                        <li><a class="nav-link scrollto" href="#about">Puerto Natales</a></li>
                        <li><a class="nav-link scrollto" href="#why-us">About</a></li>
                        <li><a class="nav-link scrollto" href="#menu">Equipment</a></li>
                        <li><a class="nav-link scrollto" href="#cabanas">Cabins</a></li>
                        <li><a class="nav-link scrollto" href="#turismo">Tourism</a></li>
                        <li><a class="nav-link scrollto" href="#gallery">Gallery</a></li>
                        <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
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

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container position-relative text-center text-lg-start" data-aos="zoom-in" data-aos-delay="100">
            <div class="row">
                <div class="col-lg-8">
                    <h1><?= $hero_title ?></h1>
                    <h1 style="font-size:30px !important"><?= $hero_subtitle ?></h1>
                    <div class="btns">
                        <a href="#menu" class="btn-menu animated fadeInUp scrollto"><?= $hero_btn_cabins ?></a>
                        <a href="#contacto" class="btn-book animated fadeInUp scrollto"><?= $hero_btn_reservations ?></a>
                    </div>
                </div>
                <div class="col-lg-4 d-flex align-items-center justify-content-center position-relative" data-aos="zoom-in" data-aos-delay="200">
                    <a href="<?= $video_url ?>" class="glightbox play-btn"></a>
                </div>
            </div>
        </div>
    </section>

    <main id="main">

        <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container" data-aos="fade-up">
                <div class="row">
                    <div class="col-lg-6 order-1 order-lg-2" data-aos="zoom-in" data-aos-delay="100">
                        <div class="about-img">
                            <img src="assets/img/puerto-natales-7.png" alt="">
                        </div>
                    </div>
                    <h2><?= $lang == 'es' ? 'Puerto Natales' : 'Port Natales' ?></h2>
                    <div class="col-lg-6 pt-4 pt-lg-0 order-2 order-lg-1 content">
                        <p style="font-size: 20px;"><?= nl2br($about_text) ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======= Why Us Section ======= -->
        <section id="why-us" class="why-us">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Por Qué Nosotros' : 'Why We' ?></h2>
                    <p><?= $lang == 'es' ? 'Por qué elegirnos' : 'Why choose us' ?></p>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="box" data-aos="zoom-in" data-aos-delay="100">
                            <span>01</span>
                            <h4 style="font-size: 30px;text-align: center;"><i class="bi bi-person-lines-fill"></i><br/>
                                <?= $lang == 'es' ? 'Atención' : 'Attention' ?>
                            </h4>
                            <p style="text-align: center;">
                                <?= $lang == 'es' 
                                    ? 'Organizamos tu viaje, con una completa guía turística para que disfrutes tu viaje conociendo lugares imponentes de la patagonia chilena.'
                                    : 'We organize your trip, with a complete tourist guide to enjoy your trip knowing imposing places in Chilean Patagonia.'
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="box" data-aos="zoom-in" data-aos-delay="200">
                            <span>02</span>
                            <h4 style="font-size: 30px;text-align: center;"><i class="bi bi-house-fill"></i><br/>
                                <?= $lang == 'es' ? 'Comodidad' : 'Comfort' ?>
                            </h4>
                            <p style="text-align: center;">
                                <?= $lang == 'es' 
                                    ? 'Contamos con cabañas nuevas, equipadas con todo lo necesario para hacer que tus vacaciones sean inolvidables.'
                                    : 'We have new cabins, equipped with everything you need to make your vacation unforgettable.'
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="box" data-aos="zoom-in" data-aos-delay="300">
                            <span>03</span>
                            <h4 style="font-size: 30px;text-align: center;"><i class="bi bi-pin-map-fill"></i><br/>
                                <?= $lang == 'es' ? 'Ubicación' : 'Location' ?>
                            </h4>
                            <p style="text-align: center;">
                                <?= $lang == 'es' 
                                    ? 'Nuestras cabañas se encuentran ubicadas en un lugar privilegiado a 5 minutos de la ciudad, y fácil acceso al comercio y lugares turísticos.'
                                    : 'Our cabins are located in a privileged place 5 minutes from the city, and easy access to commerce and tourist places.'
                                ?>
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
                </div>
                <div class="col-lg-12 pt-4 pt-lg-0 order-2 order-lg-1 content text-center">
                    <h3 style="font-family: arial;">
                        <?= $lang == 'es' 
                            ? 'Nuestras cabañas cuentan con calefacción central, <br />lo que permite una agradable estadía en invierno y verano, con una vista mágica hacia<br /> el Ventisquero Balmaceda, Canal Señoret y ciudad de Puerto Natales.<br /><span style="font-size:18px;">Cada cabaña detalla su propio equipamiento en su página.</span>'
                            : 'Our cabins have central heating, <br />which allows a pleasant stay in winter and summer, with a magical view towards<br /> the Ventisquero Balmaceda, Canal Señoret and the city of Port Natales.<br /><span style="font-size:18px;">Each cabin lists its own equipment on its page.</span>'
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

        <!-- ======= Cabañas Section ======= -->
        <section id="cabanas" class="chefs">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Cabañas' : 'Cabins' ?></h2>
                    <p><?= $lang == 'es' ? 'Nuestras Cabañas' : 'Our Cabins' ?></p>
                </div>
                <?php
                // Grilla adaptable: 1, 2, 3, 4... cabañas por fila según cantidad.
                $cabin_total = count($cabins);
                $cabin_columns = cabin_grid_columns($cabin_total);
                $cabin_col_class = cabin_grid_class($cabin_columns);
                ?>
                <?php if ($cabin_total === 0): ?>
                    <div class="text-center text-muted">
                        <p><?= $lang == 'es' ? 'Próximamente nuevas cabañas.' : 'New cabins coming soon.' ?></p>
                    </div>
                <?php else: ?>
                <div class="row justify-content-center">
                    <?php foreach ($cabins as $cabin): ?>
                        <div class="<?= $cabin_col_class ?>">
                            <div class="member" data-aos="zoom-in" data-aos-delay="100">
                                <img src="<?= htmlspecialchars(getCabinCover($cabin)) ?>" class="img-fluid cabin-card-img" alt="<?= htmlspecialchars($lang == 'es' ? $cabin['name_es'] : $cabin['name_en']) ?>">
                                <div class="member-info">
                                    <div class="member-info-content">
                                        <h4><?= htmlspecialchars($lang == 'es' ? $cabin['name_es'] : $cabin['name_en']) ?></h4>
                                    </div>
                                    <div class="social">
                                        <a href="cabana-<?= urlencode($cabin['slug']) ?>.php" style="border: 1px #fff solid;border-radius: 6px;padding: 2px 9px;" target="_blank">
                                            <?= $lang == 'es' ? 'Más Información' : 'More Information' ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ======= Events Section (Información de Cabañas) ======= -->
        <section id="events" class="events">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Cabañas' : 'Cabins' ?></h2>
                    <p><?= $lang == 'es' ? 'Información' : 'Information' ?></p>
                </div>
                <div class="events-slider swiper" data-aos="fade-up" data-aos-delay="100">
                    <div class="swiper-wrapper">
                        <?php foreach ($cabins as $cabin): ?>
                            <div class="swiper-slide">
                                <div class="row event-item">
                                    <div class="col-lg-6">
                                        <img src="<?= htmlspecialchars(getCabinImageUrl($cabin['image_main'] ?? null, SITE_URL . 'assets/img/logolengas.png')) ?>" class="img-fluid" alt="<?= htmlspecialchars($lang == 'es' ? $cabin['name_es'] : $cabin['name_en']) ?>">
                                    </div>
                                    <div class="col-lg-6 pt-4 pt-lg-0 content">
                                        <h3><?= $lang == 'es' ? $cabin['name_es'] : $cabin['name_en'] ?></h3>
                                        <div class="price">
                                            <p><span>$ <?= $lang == 'es' ? number_format($cabin['price'], 0, ',', '.') : number_format($cabin['dolar'], 0, '.', ',') ?></span> <small style="font-size:12px">(<?= $lang == 'es' ? 'por noche' : 'per night' ?>)</small></p>
                                        </div>
                                        <p><?= $lang == 'es' ? $cabin['description_es'] : $cabin['description_en'] ?></p>
                                        <ul>
                                            <li><i style="color: #A9AD00 ;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= $lang == 'es' ? 'Desayuno Incluido' : 'Breakfast included' ?></li>
                                            <li><i style="color: #A9AD00 ;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= $lang == 'es' ? 'Mínimo (2) Noches' : 'Minimum (2) nights' ?></li>
                                            <li><i style="color: #A9AD00 ;" class="bi bi-check-circle"></i>&nbsp;&nbsp;<?= $lang == 'es' ? $cabin['bed_config_es'] : $cabin['bed_config_en'] ?></li>
                                        </ul>
                                        <div style="text-align: center;" class="col-lg-4">
                                            <a href="cabana-<?= $cabin['slug'] ?>.php" class="book-a-table-btn scrollto d-none d-lg-flex">
                                                <?= $lang == 'es' ? 'Más Detalles' : 'More Details' ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>

        <!-- ======= Turismo Section ======= -->
        <section id="turismo" class="events2">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Turismo' : 'Tourism' ?></h2>
                    <p><?= $lang == 'es' ? 'Excursiones y Actividades' : 'Excursions and Activities' ?></p>
                </div>
                
                <?php if (!empty($excursiones)): ?>
                    <div class="events-slider swiper" data-aos="fade-up" data-aos-delay="100">
                        <div class="swiper-wrapper">
                            <?php foreach ($excursiones as $excursion): ?>
                                <div class="swiper-slide">
                                    <div class="row event-item">
                                        <div class="col-lg-6">
                                            <img src="<?= UPLOADS_URL . $excursion['image_path'] ?>" class="img-fluid" alt="<?= htmlspecialchars($excursion['titulo']) ?>" style="width: 100%; height: auto; border-radius: 10px;">
                                        </div>
                                        <div class="col-lg-6 pt-4 pt-lg-0 content">
                                            <h3><?= htmlspecialchars($excursion['titulo']) ?></h3>                                           
                                            <p style="border-top:2px solid #f8c146"><?= nl2br(htmlspecialchars($excursion['descripcion'])) ?></p>
                                            <p>
                                                <?= $lang == 'es' 
                                                    ? 'Para más información sobre nuestros planes de turismo disponibles, descargue nuestra guía de Turismo.'
                                                    : 'For more information about our available tourism plans, download our Tourism guide.'
                                                ?>
                                            </p>
                                            <br>
                                            <div style="text-align: center;" class="col-lg-6">     
                                                <?php if ($pdf_tourism && file_exists(__DIR__ . '/admin/uploads/pdfs/' . $pdf_tourism)): ?>
                                                    <a href="<?= SITE_URL ?>admin/uploads/pdfs/<?= htmlspecialchars($pdf_tourism) ?>" 
                                                    target="_blank" 
                                                    class="book-a-table-btn">
                                                        <i class="bi bi-file-pdf me-2"></i>
                                                        <?= $lang == 'es' ? 'Descargar información' : 'Download information' ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        <?= $lang == 'es' ? 'Próximamente disponible' : 'Coming soon' ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p><?= $lang == 'es' ? 'Próximamente más información sobre excursiones.' : 'More information about excursions coming soon.' ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <script>
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

        <!-- ======= Book A Table / Contacto Section ======= -->
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
                            <select class="form-control form-control-dark" name="cabin" required>
                                <option value=""><?= $lang == 'es' ? 'Seleccione una Cabaña' : 'Select a Cabin' ?></option>
                                <?php foreach ($cabins as $cabin): ?>
                                    <option value="<?= $cabin['id'] ?>">
                                        <?= htmlspecialchars($lang == 'es' ? $cabin['name_es'] : $cabin['name_en']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                    
                    <div class="text-center">
                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                        <button type="submit" onclick="return valor1()">
                            <?= $lang == 'es' ? 'Enviar Reserva' : 'Send Reservation' ?>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- ======= Gallery Section ======= -->
        <section id="gallery" class="gallery">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2><?= $lang == 'es' ? 'Galería' : 'Gallery' ?></h2>
                    <p><?= $lang == 'es' ? 'Puerto Natales' : 'Port Natales' ?></p>
                </div>
            </div>
            <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-0">
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="col-lg-3 col-md-4">
                            <div class="gallery-item">
                                <a href="<?= UPLOADS_URL . $image['image_path'] ?>" class="gallery-lightbox" data-gall="gallery-item">
                                    <img src="<?= UPLOADS_URL . $image['image_path'] ?>" alt="<?= htmlspecialchars($lang == 'es' ? $image['caption_es'] : $image['caption_en']) ?>" class="img-fluid">
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                                <h4><?= $lang == 'es' ? 'Fono:' : 'Phone:' ?></h4>
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

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-info">
                            <h3 class="logo me-auto me-lg-0"><a href="index.php"><?= $lang == 'es' ? 'Cabañas Las Lengas' : 'Cabins The Lengas' ?></a></h3>
                            <p>
                                <?= htmlspecialchars($contact_address) ?><br/><br/>
                                <strong><?= $lang == 'es' ? 'Fono:' : 'Phone:' ?></strong> <?= htmlspecialchars($contact_phone) ?><br/>
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
                            <li><i class="bx bx-chevron-right"></i> <a href="#"><?= $lang == 'es' ? 'Inicio' : 'Home' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#about"><?= $lang == 'es' ? 'Puerto Natales' : 'Port Natales' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#why-us"><?= $lang == 'es' ? 'Nosotros' : 'About' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#menu"><?= $lang == 'es' ? 'Equipamiento' : 'Equipment' ?></a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6 footer-links">
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="#cabanas"><?= $lang == 'es' ? 'Cabañas' : 'Cabins' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#gallery"><?= $lang == 'es' ? 'Galería' : 'Gallery' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#contact"><?= $lang == 'es' ? 'Contacto' : 'Contact' ?></a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#contacto"><?= $lang == 'es' ? 'Reservaciones' : 'Reservations' ?></a></li>
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
    <script src="assets/js/main.js"></script>

    <!-- Botones flotantes -->
    <?php $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match("/mobile/i", $useragent)) { ?>
        <a href="https://api.whatsapp.com/send?phone=<?= $whatsapp_number ?>&text=Hola, quisiera más información sobre las cabañas" class="floatm1" target="_blank"><i class="bi bi-whatsapp my-float"></i></a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="floatm2" target="_blank"><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-facebook my-float2"></i></span></a>
        <a href="<?= htmlspecialchars($instagram_url) ?>" class="floatm3" target="_blank"><span style="font-size: 15px;color: #fff !important;"><i class="bi bi-instagram my-float3"></i></span></a>
    <?php } else { ?>
        <a href="https://web.whatsapp.com/send?phone=<?= $whatsapp_number ?>&text=Hola, quisiera más información sobre las cabañas" class="float" target="_blank" title="Consulte cualquier duda via whatsapp"><span><i class="bi bi-whatsapp my-float"></i> WhatsApp</span></a>
        <a href="<?= htmlspecialchars($facebook_url) ?>" class="float2" target="_blank"><span title="Facebook"><i class="bi bi-facebook my-float2"></i> Facebook</span></a>
        <a href="<?= htmlspecialchars($instagram_url) ?>" class="float3" target="_blank"><span title="Instagram"><i class="bi bi-instagram my-float3"></i> Instagram</span></a>
    <?php } ?>

</body>
</html>