<?php
// admin/manual.php — CENTRO DE AYUDA
require_once 'includes/config.php';
require_once 'includes/auth.php';

$image_guide   = image_size_guide();
$contact_email = getSetting('contact_email') ?: 'soporte@cabanaslaslengas.cl';
$pdf_es        = getSetting('tourism_pdf_es');
$pdf_en        = getSetting('tourism_pdf_en');

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
    .help-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        color: #fff;
        border-radius: 16px;
        padding: 30px;
    }
    .help-hero h2 { color: #fff; }
    .help-hero .lead { color: rgba(255, 255, 255, .75); }
    .help-search-wrap {
        position: sticky;
        top: 8px;
        z-index: 30;
        background: #f0f2f5;
        padding: 8px 0;
    }
    .help-section { scroll-margin-top: 105px; }
    .help-nav .btn { white-space: nowrap; }
    .help-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .help-step {
        display: flex;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .help-step:last-child { border-bottom: 0; }
    .help-step .num {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #f8c146;
        color: #1a1a2e;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .help-size-table code { font-size: .85rem; }
    .accordion-button:not(.collapsed) { background: rgba(248, 193, 70, .15); }
    @media (min-width: 1200px) {
        .help-toc { position: sticky; top: 90px; }
    }
</style>

<div class="container-fluid">

    <!-- ============================================================
         PORTADA
         ============================================================ -->
    <div class="help-hero mb-4">
        <div class="d-flex align-items-start flex-wrap gap-3">
            <div class="help-icon bg-white bg-opacity-10 text-warning">
                <i class="bi bi-life-preserver"></i>
            </div>
            <div class="flex-grow-1">
                <h2 class="mb-1">Centro de Ayuda</h2>
                <p class="lead mb-2">
                    Todo lo que necesitas para administrar Cabañas Las Lengas: cabañas, galerías,
                    equipamiento, reservas, configuración e imágenes.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-warning text-dark"><i class="bi bi-book me-1"></i>Guía completa</span>
                    <span class="badge bg-light text-dark"><i class="bi bi-clock-history me-1"></i>Actualizado: Octubre 2026</span>
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Incluye mejoras recientes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         BUSCADOR
         ============================================================ -->
    <div class="help-search-wrap">
        <div class="input-group input-group-lg shadow-sm mb-1">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="search" id="helpSearch" class="form-control"
                   placeholder="Buscar en el Centro de Ayuda (ej: imágenes, cabaña, galería, reservas, equipamiento...)"
                   aria-label="Buscar en el Centro de Ayuda">
            <button class="btn btn-outline-secondary" type="button" id="helpSearchClear">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="helpSearchSummary" class="small text-muted mb-1"></div>
    </div>

    <div id="helpSearchEmpty" class="alert alert-warning d-none">
        <i class="bi bi-search me-2"></i>
        No encontramos resultados para tu búsqueda. Prueba con otra palabra o revisa el
        <a href="#primeros-pasos" class="alert-link">primeros pasos</a>.
    </div>

    <!-- ============================================================
         ACCESOS RÁPIDOS
         ============================================================ -->
    <div class="row g-2 mb-4 help-nav">
        <?php
        $quick_links = [
            ['primeros-pasos', 'Primeros pasos', 'bi-flag'],
            ['dashboard', 'Dashboard', 'bi-grid-1x2-fill'],
            ['cabins', 'Cabañas', 'bi-building'],
            ['gallery', 'Galería', 'bi-images'],
            ['amenities', 'Equipamiento', 'bi-list-check'],
            ['reservations', 'Reservas', 'bi-calendar-check'],
            ['settings', 'Configuración', 'bi-gear-fill'],
            ['documents', 'Documentos', 'bi-file-pdf'],
            ['users', 'Usuarios', 'bi-people-fill'],
            ['profile', 'Mi Perfil', 'bi-person-circle'],
            ['imagenes', 'Tamaños de imagen', 'bi-aspect-ratio'],
            ['faq', 'Solución de problemas', 'bi-question-circle'],
        ];
        foreach ($quick_links as [$anchor, $label, $icon]):
        ?>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="#<?= $anchor ?>" class="btn btn-light border w-100 text-start py-2">
                    <i class="bi <?= $icon ?> text-primary me-1"></i>
                    <span class="small"><?= $label ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ============================================================
         PRIMEROS PASOS
         ============================================================ -->
    <div id="primeros-pasos" class="help-section mb-4" data-help-title="primeros pasos inicio configuración">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-warning text-dark me-2">0</span> Primeros pasos</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Si es tu primera vez (o de alguien nuevo en el equipo), sigue este orden:</p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="help-step">
                            <div class="num">1</div>
                            <div>
                                <strong>Revisa los datos del sitio</strong><br>
                                <span class="small text-muted">
                                    En <a href="settings.php">Configuración</a> confirma teléfono, email, dirección,
                                    redes sociales y los textos en español e inglés.
                                </span>
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">2</div>
                            <div>
                                <strong>Revisa el equipamiento general</strong><br>
                                <span class="small text-muted">
                                    En <a href="amenities.php">Equipamiento</a> crea/edita el catálogo. Después se
                                    asigna cabaña por cabaña.
                                </span>
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">3</div>
                            <div>
                                <strong>Crea o edita tus cabañas</strong><br>
                                <span class="small text-muted">
                                    En <a href="cabins.php">Cabañas</a>. Al crear una se genera su página pública
                                    y se abre la galería para subir fotos.
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="help-step">
                            <div class="num">4</div>
                            <div>
                                <strong>Sube las fotos de cada cabaña</strong><br>
                                <span class="small text-muted">
                                    Portada (tarjeta del index), Imagen Principal y la galería del slider.
                                    Revisa los <a href="#imagenes">tamaños recomendados</a>.
                                </span>
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">5</div>
                            <div>
                                <strong>Marca el equipamiento de cada cabaña</strong><br>
                                <span class="small text-muted">
                                    Cabañas → Editar → sección "Equipamiento de esta cabaña".
                                </span>
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">6</div>
                            <div>
                                <strong>Revisa el sitio y las reservas</strong><br>
                                <span class="small text-muted">
                                    Usa "Ver Web" y revisa <a href="reservations.php">Reservas</a> y el
                                    <a href="dashboard.php">Dashboard</a> a diario.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mb-0 mt-2">
                    <i class="bi bi-info-circle me-2"></i>
                    ¿Buscas algo puntual? Usa el <strong>buscador</strong> de arriba o el índice de accesos rápidos.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         1. DASHBOARD
         ============================================================ -->
    <div id="dashboard" class="help-section mb-4" data-help-title="dashboard estadísticas resumen">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">1</span> Dashboard</h4>
            </div>
            <div class="card-body">
                <p>Es la pantalla principal al iniciar sesión. Muestra un resumen del sitio en tiempo real.</p>
                <h6><i class="bi bi-bar-chart me-2 text-primary"></i>Qué información muestra</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="mb-3">
                            <li><strong>Total de reservas</strong> y reservas <strong>pendientes</strong> (prioridad).</li>
                            <li><strong>Reservas confirmadas</strong> y canceladas.</li>
                            <li><strong>Cabañas activas</strong> publicadas en el sitio.</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-3">
                            <li><strong>Imágenes de galería</strong> y <strong>equipamiento</strong> registrados.</li>
                            <li><strong>Ingresos estimados</strong> de reservas confirmadas.</li>
                            <li><strong>Tendencia de reservas</strong> de los últimos meses.</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Consejo:</strong> revisa el Dashboard a diario; las reservas pendientes necesitan respuesta rápida.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         2. CABAÑAS
         ============================================================ -->
    <div id="cabins" class="help-section mb-4" data-help-title="cabañas crear editar página galería equipamiento slug eliminar">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">2</span> Cabañas</h4>
            </div>
            <div class="card-body">
                <p>
                    Aquí se administra el corazón del sitio: cada cabaña tiene sus datos, su página pública,
                    su galería y su equipamiento.
                </p>

                <h6><i class="bi bi-plus-circle me-2 text-primary"></i>Crear una cabaña nueva</h6>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="help-step">
                            <div class="num">1</div>
                            <div><strong>Nueva Cabaña:</strong> completa nombre en español e inglés, slug, precios, capacidad y descripciones.</div>
                        </div>
                        <div class="help-step">
                            <div class="num">2</div>
                            <div>
                                <strong>Imagen de Portada:</strong> es la tarjeta del index.
                                <?= htmlspecialchars(image_size_text('cabin_cover')) ?>.
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">3</div>
                            <div>
                                <strong>Imagen Principal:</strong> respaldo del slider de la página de la cabaña.
                                <?= htmlspecialchars(image_size_text('cabin_main')) ?>.
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="help-step">
                            <div class="num">4</div>
                            <div><strong>Equipamiento:</strong> marca los items que tiene esta cabaña (se muestran solo los marcados).</div>
                        </div>
                        <div class="help-step">
                            <div class="num">5</div>
                            <div>
                                <strong>Guardar:</strong> se crea la fila en la base de datos y se genera automáticamente
                                el archivo <code>cabana-{slug}.php</code> en la raíz del sitio.
                            </div>
                        </div>
                        <div class="help-step">
                            <div class="num">6</div>
                            <div>
                                <strong>Galería:</strong> el panel te lleva directo a subir las fotos del slider
                                interno. Puedes subir varias a la vez.
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mt-4"><i class="bi bi-list-ul me-2 text-primary"></i>Campos de una cabaña</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr><th>Campo</th><th>Para qué sirve</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Nombre ES / EN</strong></td><td>Nombre en ambos idiomas.</td></tr>
                            <tr><td><strong>Slug</strong></td><td>Identificador de la URL (ej: <code>coihue</code> → <code>cabana-coihue.php</code>). Solo minúsculas, números y guiones.</td></tr>
                            <tr><td><strong>Precio CLP / Dólar</strong></td><td>Valores por noche en pesos y en dólares.</td></tr>
                            <tr><td><strong>Máx. personas</strong></td><td>Capacidad máxima de la cabaña.</td></tr>
                            <tr><td><strong>Configuración de camas</strong></td><td>Detalle que se muestra en la página de la cabaña.</td></tr>
                            <tr><td><strong>Descripciones</strong></td><td>Texto que aparece en la página pública (ES/EN).</td></tr>
                            <tr><td><strong>Imagen de Portada</strong></td><td>Tarjeta de "Nuestras Cabañas" en el index.</td></tr>
                            <tr><td><strong>Imagen Principal</strong></td><td>Respaldo del slider de la cabaña.</td></tr>
                            <tr><td><strong>Equipamiento</strong></td><td>Items que tiene esta cabaña.</td></tr>
                            <tr><td><strong>Estado</strong></td><td>Activa (visible) o Inactiva (oculta del sitio).</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4"><i class="bi bi-images me-2 text-primary"></i>Galería interna de la cabaña</h6>
                <p class="mb-2">
                    Se administra con el botón <span class="badge bg-success"><i class="bi bi-images"></i></span>
                    de la fila de cada cabaña (o automáticamente al crearla).
                </p>
                <ul>
                    <li>Permite <strong>subir varias fotos</strong> y eliminarlas.</li>
                    <li>Esas fotos son las que se ven en el <strong>slider</strong> de la página pública.</li>
                    <li>Si la cabaña no tiene fotos en su galería, se usa la Imagen Principal.</li>
                    <li>Recomendado: <?= htmlspecialchars(image_size_text('cabin_gallery')) ?>.</li>
                </ul>

                <h6 class="mt-4"><i class="bi bi-list-check me-2 text-primary"></i>Equipamiento por cabaña</h6>
                <p class="mb-2">
                    En <strong>Cabañas → Editar</strong> hay una sección "Equipamiento de esta cabaña" con
                    checkboxes. La página pública muestra <strong>solo lo marcado</strong>.
                    Los items se crean/editan en <a href="amenities.php">Equipamiento</a>.
                </p>

                <h6 class="mt-4"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>La grilla del index se adapta sola</h6>
                <p class="mb-2">La sección "Nuestras Cabañas" acomoda las tarjetas según cuántas cabañas activas haya:</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm w-auto">
                        <thead class="table-light"><tr><th>Cabañas activas</th><th>Tarjetas por fila</th></tr></thead>
                        <tbody>
                            <tr><td>1</td><td>1 (centrada)</td></tr>
                            <tr><td>2</td><td>2</td></tr>
                            <tr><td>3</td><td>3 (situación actual)</td></tr>
                            <tr><td>4</td><td>4</td></tr>
                            <tr><td>5</td><td>3 + 2</td></tr>
                            <tr><td>6</td><td>3 + 3</td></tr>
                            <tr><td>7</td><td>4 + 3</td></tr>
                            <tr><td>8</td><td>4 + 4</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar, cambiar slug y eliminar</h6>
                <ul>
                    <li><strong>Editar:</strong> lápiz de la fila. Puedes cambiar datos, fotos, equipamiento y estado.</li>
                    <li><strong>Cambiar el slug:</strong> el archivo de la página anterior se elimina y se genera el nuevo automáticamente.</li>
                    <li><strong>Ver página:</strong> botón <i class="bi bi-eye"></i>.</li>
                    <li><strong>Eliminar:</strong> borra la cabaña, su página <code>cabana-{slug}.php</code>, sus fotos y su galería. <strong>No hay papelera.</strong></li>
                </ul>

                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Páginas hechas a mano:</strong> si ya existía un archivo <code>cabana-{slug}.php</code>
                    creado manualmente, el panel <strong>no lo sobreescribe</strong> y avisa con una advertencia.
                    Las 3 cabañas originales del sitio mantienen sus páginas propias.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         3. GALERÍA
         ============================================================ -->
    <div id="gallery" class="help-section mb-4" data-help-title="galería imágenes general excursiones turismo">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">3</span> Galería</h4>
            </div>
            <div class="card-body">
                <p>
                    Esta pantalla administra las imágenes del sitio que <strong>no</strong> son de una cabaña:
                    la galería general (Puerto Natales) y las excursiones/turismo.
                </p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Las fotos de cada cabaña no se suben aquí.</strong>
                    Se administran en <a href="cabins.php" class="alert-link">Cabañas</a> →
                    botón <i class="bi bi-images"></i> <strong>Galería</strong> de la cabaña.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr><th>Categoría</th><th>Dónde se usa</th><th>Tamaño recomendado</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-info">galeria</span></td>
                                <td>Sección "Galería" del index</td>
                                <td><?= htmlspecialchars(image_size_text('site_gallery')) ?></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark">excursion</span></td>
                                <td>Sección "Turismo" del index</td>
                                <td><?= htmlspecialchars(image_size_text('excursion')) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h6><i class="bi bi-lightning me-2 text-primary"></i>Acciones</h6>
                <ul class="mb-0">
                    <li><strong>Nueva Imagen:</strong> selecciona categoría, título y archivo.</li>
                    <li><strong>Editar:</strong> cambia título, categoría, orden o reemplaza la imagen.</li>
                    <li><strong>Eliminar:</strong> borra la imagen y su archivo del servidor.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ============================================================
         4. EQUIPAMIENTO
         ============================================================ -->
    <div id="amenities" class="help-section mb-4" data-help-title="equipamiento amenities catálogo asignar items iconos">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">4</span> Equipamiento</h4>
            </div>
            <div class="card-body">
                <p>
                    El equipamiento funciona en dos niveles: un <strong>catálogo general</strong> y la
                    <strong>asignación por cabaña</strong>.
                </p>
                <div class="row">
                    <div class="col-lg-6">
                        <h6><i class="bi bi-collection me-2 text-primary"></i>Catálogo general (esta pantalla)</h6>
                        <ul>
                            <li>Crear, editar y eliminar items.</li>
                            <li>Campos: nombre ES/EN, descripción, ícono, imagen, orden y estado.</li>
                            <li>El botón <strong>"Nuevo Item"</strong> está arriba a la derecha.</li>
                            <li>Un item <strong>inactivo</strong> no se muestra en el sitio.</li>
                            <li>Imagen recomendada: <?= htmlspecialchars(image_size_text('amenity')) ?>.</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h6><i class="bi bi-check2-square me-2 text-primary"></i>Asignación por cabaña</h6>
                        <ul>
                            <li>En <a href="cabins.php">Cabañas → Editar</a> → "Equipamiento de esta cabaña".</li>
                            <li>Puedes marcar un conjunto distinto en cada cabaña.</li>
                            <li>La página pública muestra solo lo asignado.</li>
                            <li>Si creas un item nuevo, recuerda asignarlo a las cabañas que corresponda.</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Íconos:</strong> puedes usar clases de Bootstrap Icons, por ejemplo
                    <code>bi bi-wifi</code>, <code>bi bi-tv</code>, <code>bi bi-water</code>.
                    Catálogo: <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">icons.getbootstrap.com</a>.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         5. RESERVAS
         ============================================================ -->
    <div id="reservations" class="help-section mb-4" data-help-title="reservas estados confirmar cancelar clientes">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">5</span> Reservas</h4>
            </div>
            <div class="card-body">
                <p>Muestra las solicitudes enviadas desde el formulario del sitio.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr><th>Estado</th><th>Significado</th><th>Qué hacer</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><span class="badge bg-warning text-dark">Pendiente</span></td><td>Esperando confirmación</td><td>Revisar y confirmar</td></tr>
                            <tr><td><span class="badge bg-success">Confirmada</span></td><td>Reserva aceptada</td><td>Preparar la llegada</td></tr>
                            <tr><td><span class="badge bg-danger">Cancelada</span></td><td>Reserva anulada</td><td>Archivar</td></tr>
                            <tr><td><span class="badge bg-secondary">Completada</span></td><td>Estadía finalizada</td><td>Archivar</td></tr>
                        </tbody>
                    </table>
                </div>
                <ul class="mb-2">
                    <li><strong>Cambiar estado:</strong> usa el selector de la reserva.</li>
                    <li><strong>Ver detalle:</strong> cliente, fechas, personas y cabaña solicitada.</li>
                    <li><strong>Eliminar:</strong> solo si ya no necesitas el registro.</li>
                </ul>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Consejo:</strong> responde rápido y cambia la reserva a "Confirmada"; el cliente
                    recibe correos automáticos con los datos.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         6. CONFIGURACIÓN
         ============================================================ -->
    <div id="settings" class="help-section mb-4" data-help-title="configuración datos contacto redes textos video smtp env">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">6</span> Configuración</h4>
            </div>
            <div class="card-body">
                <p>Controla los textos y datos generales del sitio. Los cambios se reflejan al instante.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light"><tr><th>Configuración</th><th>Para qué sirve</th></tr></thead>
                        <tbody>
                            <tr><td><strong>Título del sitio (ES/EN)</strong></td><td>Pestaña del navegador y títulos.</td></tr>
                            <tr><td><strong>Descripción (ES/EN)</strong></td><td>Texto de la sección "Sobre Nosotros".</td></tr>
                            <tr><td><strong>Teléfono / Email / Dirección</strong></td><td>Datos de contacto del sitio y correos.</td></tr>
                            <tr><td><strong>Facebook / Instagram</strong></td><td>Enlaces de redes sociales y botones flotantes.</td></tr>
                            <tr><td><strong>WhatsApp</strong></td><td>Número del botón flotante (sin + ni espacios).</td></tr>
                            <tr><td><strong>URL del video</strong></td><td>Video del hero (YouTube).</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Datos técnicos (no editables aquí):</strong> la URL del sitio, la base de datos y el
                    servidor de correo se configuran en el archivo <code>.env</code>. Si algo de eso cambia,
                    contacta al soporte técnico.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         7. DOCUMENTOS
         ============================================================ -->
    <div id="documents" class="help-section mb-4" data-help-title="documentos pdf turismo guía descargar">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">7</span> Documentos</h4>
            </div>
            <div class="card-body">
                <p>Gestiona los PDF de la guía de turismo que se muestran en la sección "Turismo".</p>
                <div class="row">
                    <div class="col-md-7">
                        <ul>
                            <li><strong>PDF Español</strong> y <strong>PDF Inglés</strong> (uno por idioma).</li>
                            <li><strong>Subir PDF:</strong> reemplaza el archivo anterior automáticamente.</li>
                            <li><strong>Ver / Descargar / Eliminar</strong> desde la misma pantalla.</li>
                            <li>Requisitos: PDF válido, <strong>máximo 5 MB</strong>.</li>
                        </ul>
                    </div>
                    <div class="col-md-5">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="mb-2"><i class="bi bi-file-pdf text-danger me-2"></i>Estado actual</h6>
                            <div class="d-flex align-items-center py-1">
                                <span class="badge bg-<?= $pdf_es ? 'success' : 'secondary' ?> me-2">ES</span>
                                <span><?= $pdf_es ? 'PDF subido' : 'Sin subir' ?></span>
                            </div>
                            <div class="d-flex align-items-center py-1">
                                <span class="badge bg-<?= $pdf_en ? 'success' : 'secondary' ?> me-2">EN</span>
                                <span><?= $pdf_en ? 'PDF subido' : 'Sin subir' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         8. USUARIOS
         ============================================================ -->
    <div id="users" class="help-section mb-4" data-help-title="usuarios roles administrador editor contraseña">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">8</span> Usuarios</h4>
            </div>
            <div class="card-body">
                <p>Gestiona las cuentas con acceso al panel de administración.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm w-auto align-middle">
                        <thead class="table-light"><tr><th>Rol</th><th>Permisos</th></tr></thead>
                        <tbody>
                            <tr><td><span class="badge bg-danger">Administrador</span></td><td>Acceso total, incluyendo usuarios.</td></tr>
                            <tr><td><span class="badge bg-secondary">Editor</span></td><td>Gestiona contenido del sitio.</td></tr>
                        </tbody>
                    </table>
                </div>
                <ul class="mb-0">
                    <li><strong>Agregar usuario:</strong> crea cuentas para el equipo.</li>
                    <li><strong>Eliminar usuario:</strong> no puedes eliminar tu propia cuenta.</li>
                    <li><strong>Cambiar contraseña:</strong> cada usuario lo hace desde "Mi Perfil".</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ============================================================
         9. MI PERFIL
         ============================================================ -->
    <div id="profile" class="help-section mb-4" data-help-title="mi perfil contraseña usuario email">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">9</span> Mi Perfil</h4>
            </div>
            <div class="card-body">
                <p>Información de tu cuenta y cambio de contraseña.</p>
                <div class="row">
                    <div class="col-md-7">
                        <h6><i class="bi bi-key me-2 text-primary"></i>Cambiar contraseña</h6>
                        <ol class="mb-0">
                            <li>Ingresa tu contraseña actual.</li>
                            <li>Ingresa la nueva contraseña (mínimo 6 caracteres).</li>
                            <li>Confírmala y guarda.</li>
                        </ol>
                    </div>
                    <div class="col-md-5">
                        <div class="bg-light p-3 rounded-3 small">
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>Usuario:</span>
                                <span class="text-muted"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Estado:</span>
                                <span class="badge bg-success">Activo</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-shield-lock me-2"></i>
                    Usa una contraseña larga y única, y cámbiala periódicamente.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         10. IMÁGENES Y TAMAÑOS RECOMENDADOS
         ============================================================ -->
    <div id="imagenes" class="help-section mb-4" data-help-title="imágenes tamaños medidas recomendadas recorte portada slider galería equipamiento">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">10</span> Imágenes y tamaños recomendados</h4>
            </div>
            <div class="card-body">
                <p>
                    Cada sección del sitio tiene un tamaño sugerido. Si subes una imagen con otra proporción,
                    el sistema la <strong>ajusta y recorta automáticamente</strong> para que el diseño se vea parejo;
                    aun así, usar el tamaño recomendado evita que se corten partes importantes de la foto.
                </p>

                <h6><i class="bi bi-rulers me-2 text-primary"></i>Tabla de tamaños</h6>
                <div class="table-responsive help-size-table">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sección</th>
                                <th>Medida</th>
                                <th>Proporción</th>
                                <th>Dónde se usa</th>
                                <th>Ajuste automático</th>
                                <th>Formato</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($image_guide as $item): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($item['label']) ?></strong></td>
                                <td><code><?= (int) $item['width'] ?> × <?= (int) $item['height'] ?> px</code></td>
                                <td><?= htmlspecialchars($item['ratio']) ?></td>
                                <td class="small"><?= htmlspecialchars($item['where']) ?></td>
                                <td>
                                    <?php if ($item['autocrop']): ?>
                                        <span class="badge bg-success">Sí, recorte centrado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?= htmlspecialchars($item['format']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4"><i class="bi bi-aspect-ratio me-2 text-primary"></i>Cómo se ajustan las imágenes</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info h-100 mb-0">
                            <strong>Tarjeta del index (4:3):</strong>
                            la portada de cada cabaña se muestra siempre con la misma proporción.
                            Una foto vertical o muy panorámica se recorta por los lados.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info h-100 mb-0">
                            <strong>Slider de la cabaña (16:9):</strong>
                            la imagen principal y las fotos de la galería se muestran en formato horizontal.
                            Si la foto es más baja, se recorta arriba/abajo.
                        </div>
                    </div>
                </div>

                <h6 class="mt-4"><i class="bi bi-lightbulb me-2 text-primary"></i>Buenas prácticas</h6>
                <ul>
                    <li>Usa fotos <strong>horizontales</strong> y centra el motivo principal (la cabaña, la vista).</li>
                    <li>Ideal: <strong>menos de 500 KB</strong> por imagen. El panel acepta hasta <strong>5 MB</strong>.</li>
                    <li>Prefiere <strong>JPG</strong> o <strong>WebP</strong> para fotos; PNG solo si necesitas transparencia.</li>
                    <li>Mantén el mismo tamaño entre las fotos de una misma galería para que el slider se vea uniforme.</li>
                    <li>Para recortar: Paint, Canva, Photoshop o cualquier editor de imágenes (elige la proporción indicada).</li>
                </ul>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Regla rápida:</strong> si la foto se ve "corta" o "estirada" en el sitio, revisa la
                    proporción: portada 4:3, slider/galería de cabaña 16:9, equipamiento 1:1.
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         11. SOLUCIÓN DE PROBLEMAS / FAQ
         ============================================================ -->
    <div id="faq" class="help-section mb-4" data-help-title="problemas faq errores imágenes no se ven correo permisos caché backup">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">11</span> Solución de problemas</h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="bi bi-image me-2 text-primary"></i>Subí una imagen nueva y no se ve en el sitio
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                <ol class="mb-2">
                                    <li>Recarga con <kbd>Ctrl + F5</kbd> (el navegador guarda imágenes en caché).</li>
                                    <li>Verifica que la imagen aparezca en el listado del admin.</li>
                                    <li>Revisa que subiste el archivo correcto y que pesa menos de 5 MB.</li>
                                    <li>Si el sitio está en producción, confirma con soporte la configuración de <code>.env</code> (URL y permisos de escritura).</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="bi bi-aspect-ratio me-2 text-primary"></i>La imagen se ve cortada, "corta" o estirada
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                Es normal si la proporción no coincide: el sistema recorta para mantener el diseño.
                                Usa las medidas de la sección <a href="#imagenes">Tamaños recomendados</a>:
                                portada 1200×900 (4:3), imagen principal y galería de cabaña 1600×900 (16:9).
                                Centra el motivo principal en la foto.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="bi bi-exclamation-triangle me-2 text-primary"></i>Creé una cabaña pero su página no abre
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                <ul class="mb-2">
                                    <li>Revisa el aviso en <a href="cabins.php">Cabañas</a>: puede indicar que no se pudo crear el archivo por permisos de escritura en la raíz del proyecto.</li>
                                    <li>Confirma que el slug sea válido (minúsculas, números y guiones).</li>
                                    <li>Verifica que la cabaña esté <strong>Activa</strong>.</li>
                                    <li>Si el problema persiste, pide a soporte revisar los permisos del servidor.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="bi bi-images me-2 text-primary"></i>No encuentro la cabaña en Galería
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                Las fotos de cabañas ya no se administran en <strong>Galería</strong>.
                                Entra a <a href="cabins.php">Cabañas</a> y usa el botón
                                <span class="badge bg-success"><i class="bi bi-images"></i></span> de la cabaña
                                (también se abre automáticamente al crearla). La pantalla Galería es solo para
                                galería general y excursiones.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <i class="bi bi-list-check me-2 text-primary"></i>El equipamiento no aparece en una cabaña
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                Revisa que el item esté <strong>activo</strong> en <a href="amenities.php">Equipamiento</a>
                                y que esté <strong>marcado</strong> en Cabañas → Editar → "Equipamiento de esta cabaña".
                                Si acabas de crear el item, asígnalo a las cabañas correspondientes.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                <i class="bi bi-envelope me-2 text-primary"></i>No llegan los correos de reservas
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                <ul class="mb-0">
                                    <li>Revisa la carpeta de spam del correo destino.</li>
                                    <li>La configuración SMTP está en el archivo <code>.env</code> (host, usuario, clave).</li>
                                    <li>Algunos hostings bloquean el envío de correo; soporte puede configurar SMTP autenticado.</li>
                                    <li>Las reservas igual quedan guardadas en el admin aunque el correo falle.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                <i class="bi bi-database me-2 text-primary"></i>Error de conexión a la base de datos
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                Es un problema de configuración del servidor (credenciales en <code>.env</code> o
                                servicio MySQL detenido). Contacta a soporte técnico; no intentes cambiar archivos
                                de configuración del servidor.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                <i class="bi bi-trash me-2 text-primary"></i>Eliminé una cabaña por error
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                No hay papelera. La cabaña, su página y sus fotos se borran definitivamente.
                                Para recuperarla: restaurar el respaldo de la base de datos y de la carpeta
                                <code>admin/assets/uploads/</code>, o volver a crearla con los mismos datos.
                                <strong>Haz respaldos periódicos.</strong>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                                <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>¿Cómo hago respaldos?
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                Lo ideal es respaldar dos cosas:
                                <ol class="mb-0">
                                    <li>La <strong>base de datos</strong> (exportar un respaldo .sql desde el hosting/phpMyAdmin).</li>
                                    <li>La carpeta <strong>admin/assets/uploads/</strong>, que contiene las imágenes subidas.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                                <i class="bi bi-shield-lock me-2 text-primary"></i>Buenas prácticas de seguridad
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small">
                                <ul class="mb-0">
                                    <li>No compartas tu contraseña del panel.</li>
                                    <li>Cada persona del equipo debe tener su propio usuario.</li>
                                    <li>El archivo <code>.env</code> contiene credenciales: nunca debe subirse a un repositorio público.</li>
                                    <li>Si una clave pudo quedar expuesta, cámbiala y avisa a soporte.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         12. SOBRE EL CENTRO DE AYUDA
         ============================================================ -->
    <div id="centro-ayuda" class="help-section mb-4" data-help-title="centro de ayuda manual buscar atajos soporte">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h4 class="mb-0"><span class="badge bg-primary me-2">12</span> Sobre este Centro de Ayuda</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-7">
                        <p>Esta guía se actualiza junto con el sistema e incluye las mejoras recientes:</p>
                        <ul>
                            <li>Creación automática de la página pública de cada cabaña.</li>
                            <li>Galería interna por cabaña y equipamiento configurable.</li>
                            <li>Grilla del index que se adapta a la cantidad de cabañas.</li>
                            <li>Tamaños de imagen recomendados y ajuste automático de recortes.</li>
                            <li>Sección de solución de problemas paso a paso.</li>
                        </ul>
                    </div>
                    <div class="col-lg-5">
                        <div class="bg-light p-3 rounded-3">
                            <h6><i class="bi bi-lightbulb text-warning me-2"></i>Atajos útiles</h6>
                            <ul class="small mb-0">
                                <li><strong>Buscador:</strong> filtra las secciones al escribir.</li>
                                <li><kbd>Ctrl</kbd> + <kbd>F</kbd>: buscar dentro de la página.</li>
                                <li><a href="#primeros-pasos">Primeros pasos</a> · <a href="#imagenes">Tamaños</a> · <a href="#faq">Problemas</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mb-0 mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    ¿No encuentras la solución? Escríbenos a
                    <a href="mailto:<?= htmlspecialchars($contact_email) ?>"><?= htmlspecialchars($contact_email) ?></a>
                    contando qué intentabas hacer y qué ocurrió.
                </div>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    var input = document.getElementById('helpSearch');
    var clear = document.getElementById('helpSearchClear');
    var summary = document.getElementById('helpSearchSummary');
    var empty = document.getElementById('helpSearchEmpty');
    var sections = Array.prototype.slice.call(document.querySelectorAll('.help-section'));

    if (!input || !sections.length) { return; }

    function normalize(text) {
        return (text || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function run() {
        var term = normalize(input.value.trim());
        var visible = 0;

        sections.forEach(function (section) {
            var haystack = normalize(section.textContent + ' ' + (section.getAttribute('data-help-title') || ''));
            var match = term === '' || haystack.indexOf(term) !== -1;
            section.classList.toggle('d-none', !match);
            if (match) { visible++; }
        });

        if (term === '') {
            summary.textContent = '';
            empty.classList.add('d-none');
        } else {
            summary.textContent = visible + ' de ' + sections.length + ' secciones coinciden con "' + input.value.trim() + '"';
            empty.classList.toggle('d-none', visible !== 0);
        }
    }

    input.addEventListener('input', run);

    if (clear) {
        clear.addEventListener('click', function () {
            input.value = '';
            run();
            input.focus();
        });
    }
})();
</script>

<?php include 'includes/footer.php'; ?>
