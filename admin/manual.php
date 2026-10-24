<?php
// admin/manual.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <!-- ============================================================
    HEADER DEL MANUAL
    ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div class="me-3 p-3 bg-primary bg-opacity-10 rounded-circle">
                    <i class="bi bi-book text-primary" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h2 class="mb-0">Manual de Usuario</h2>
                    <p class="text-muted mb-0">Guía completa para administrar tu sitio web de Cabañas Las Lengas</p>
                </div>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Esta guía te ayudará a entender cada sección del panel de administración y cómo utilizarla correctamente.
                <strong>Última actualización:</strong> Septiembre 2026
            </div>
        </div>
    </div>

    <!-- ============================================================
    ÍNDICE RÁPIDO
    ============================================================ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Índice Rápido</h5>
                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#dashboard" class="text-decoration-none">
                                <span class="badge bg-primary me-1">1</span> Dashboard
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#cabins" class="text-decoration-none">
                                <span class="badge bg-primary me-1">2</span> Cabañas
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#gallery" class="text-decoration-none">
                                <span class="badge bg-primary me-1">3</span> Galería
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#amenities" class="text-decoration-none">
                                <span class="badge bg-primary me-1">4</span> Equipamiento
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#reservations" class="text-decoration-none">
                                <span class="badge bg-primary me-1">5</span> Reservas
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#settings" class="text-decoration-none">
                                <span class="badge bg-primary me-1">6</span> Configuración
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#documents" class="text-decoration-none">
                                <span class="badge bg-primary me-1">7</span> Documentos
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#users" class="text-decoration-none">
                                <span class="badge bg-primary me-1">8</span> Usuarios
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#profile" class="text-decoration-none">
                                <span class="badge bg-primary me-1">9</span> Mi Perfil
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <a href="#manual" class="text-decoration-none">
                                <span class="badge bg-primary me-1">10</span> Manual
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 1: DASHBOARD
    ============================================================ -->
    <div id="dashboard" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">1</span> Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>¿Qué es?</h5>
                            <p>El Dashboard es la pantalla principal que ves al iniciar sesión. Te muestra un <strong>resumen completo</strong> de tu sitio web con estadísticas en tiempo real.</p>
                            
                            <h6 class="mt-3">📊 ¿Qué información muestra?</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Estadística</th>
                                            <th>¿Qué significa?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Total Reservas</strong></td>
                                            <td>Número total de reservas recibidas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pendientes</strong></td>
                                            <td>Reservas que requieren tu atención (¡prioridad!)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Confirmadas</strong></td>
                                            <td>Reservas que ya has aceptado</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cabañas Activas</strong></td>
                                            <td>Número de cabañas disponibles en el sitio</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Galería</strong></td>
                                            <td>Cantidad de imágenes en tu galería</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Equipamiento</strong></td>
                                            <td>Cantidad de servicios/amenities configurados</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ingresos Estimados</strong></td>
                                            <td>Total estimado de ingresos de reservas confirmadas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tasa de Conversión</strong></td>
                                            <td>Porcentaje de reservas pendientes vs confirmadas</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-3">📈 Gráfico de Reservas por Mes</h6>
                            <p>Visualiza la tendencia de reservas de los últimos 6 meses para identificar temporadas altas y bajas.</p>
                            
                            <h6 class="mt-3">⚡ Acciones Rápidas</h6>
                            <p>Botones de acceso directo a las secciones más importantes:</p>
                            <ul>
                                <li><strong>Ver Reservas</strong> → Gestionar reservas pendientes</li>
                                <li><strong>Gestionar Cabañas</strong> → Editar o agregar cabañas</li>
                                <li><strong>Gestionar Galería</strong> → Subir nuevas imágenes</li>
                                <li><strong>Configuración</strong> → Cambiar textos y datos del sitio</li>
                                <li><strong>Ver Sitio Web</strong> → Abrir tu sitio en nueva pestaña</li>
                            </ul>
                            
                            <h6 class="mt-3">📋 Últimas Reservas</h6>
                            <p>Lista de las 5 reservas más recientes con su estado actual.</p>
                            
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Consejo:</strong> Revisa el dashboard diariamente para estar al tanto de las nuevas reservas pendientes. ¡Es tu centro de control!
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <i class="bi bi-grid-1x2-fill" style="font-size: 3rem; color: #f8c146;"></i>
                                <h6 class="mt-2">Dashboard</h6>
                                <div class="d-flex justify-content-around mt-3 flex-wrap">
                                    <div class="text-center m-1">
                                        <span class="badge bg-primary">Total</span>
                                        <h5 class="mt-1" id="total-reservas">--</h5>
                                    </div>
                                    <div class="text-center m-1">
                                        <span class="badge bg-warning">Pendientes</span>
                                        <h5 class="mt-1" id="pendientes">--</h5>
                                    </div>
                                    <div class="text-center m-1">
                                        <span class="badge bg-success">Confirmadas</span>
                                        <h5 class="mt-1" id="confirmadas">--</h5>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2">
                                    <i class="bi bi-arrow-right"></i> Vista rápida de tu negocio
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 2: CABAÑAS
    ============================================================ -->
    <div id="cabins" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">2</span> Cabañas</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-building me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Esta sección te permite gestionar toda la información de las cabañas que ofreces.</p>
                            
                            <h6 class="mt-3">📋 Campos de una cabaña:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Campo</th>
                                            <th>¿Para qué sirve?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Nombre (ES/EN)</strong></td>
                                            <td>El nombre de la cabaña en ambos idiomas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Slug</strong></td>
                                            <td>Identificador para la URL (ej: "coihue" → cabana-coihue.php)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Descripción (ES/EN)</strong></td>
                                            <td>Descripción detallada que aparece en la página de la cabaña</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Precio</strong></td>
                                            <td>Precio por noche en pesos chilenos (CLP)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Máx. Personas</strong></td>
                                            <td>Capacidad máxima de la cabaña</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Configuración Camas</strong></td>
                                            <td>Detalle de las camas (ej: "1 Cama King Size")</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Imagen de Portada</strong></td>
                                            <td>Imagen que se muestra en la tarjeta del index (si no se sube, se usa la Imagen Principal)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Imagen Principal</strong></td>
                                            <td>Imagen que aparece en el slider de la página pública de la cabaña</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado</strong></td>
                                            <td>Activa (visible) o Inactiva (oculta)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Página pública</strong></td>
                                            <td>Se genera automáticamente el archivo <code>cabana-{slug}.php</code> con el mismo modelo informativo que las demás cabañas. El contenido (nombre, precio, fotos, galería) se toma de la base de datos.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Agregar nueva cabaña:</strong> Haz clic en "Nueva Cabaña". Al guardar se crea la página pública y se abre la galería para subir las fotos del slider.</li>
                                <li><strong>Editar cabaña:</strong> Haz clic en <i class="bi bi-pencil text-primary"></i>. Ahí también se marca el equipamiento de esa cabaña.</li>
                                <li><strong>Galería de la cabaña:</strong> Haz clic en <i class="bi bi-images text-success"></i> para subir/eliminar las fotos del slider interno.</li>
                                <li><strong>Ver página pública:</strong> Haz clic en <i class="bi bi-eye text-secondary"></i></li>
                                <li><strong>Eliminar cabaña:</strong> Haz clic en <i class="bi bi-trash text-danger"></i> (¡cuidado!)</li>
                            </ul>

                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Grilla del index:</strong> las tarjetas de "Nuestras Cabañas" se reacomodan solas según cuántas cabañas activas haya
                                (2 por fila con 2 cabañas, 3 por fila con 3 o 6, 4 por fila con 4 u 8, etc.).
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Al eliminar una cabaña, se perderá toda su información. Las reservas asociadas se mantienen.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-building" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Ejemplo de cabaña</h6>
                                </div>
                                <div class="border rounded p-2 mt-2 bg-white">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary" style="width: 60px; height: 45px; border-radius: 5px;"></div>
                                        <div class="ms-2">
                                            <strong>Cabaña Coihue</strong>
                                            <div class="small text-muted">$160.000 por noche</div>
                                        </div>
                                        <span class="badge bg-success ms-auto">Activa</span>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2 text-center">
                                    <i class="bi bi-arrow-right"></i> La imagen, nombre y precio se muestran en la web
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 3: GALERÍA
    ============================================================ -->
    <div id="gallery" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">3</span> Galería</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-images me-2 text-primary"></i>¿Qué es?</h5>
                            <p>La galería almacena todas las imágenes del sitio web, organizadas por categorías.</p>
                            
                            <h6 class="mt-3">📂 Categorías disponibles:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Categoría</th>
                                            <th>¿Dónde se usan?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-info">galeria</span></td>
                                            <td>Imágenes de la sección "Galería" en la página principal</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning">excursion</span></td>
                                            <td>Imágenes de la sección "Turismo"</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">(cabañas)</span></td>
                                            <td>
                                                Las galerías de las cabañas se administran desde
                                                <strong>Cabañas → botón <i class="bi bi-images"></i></strong>,
                                                no desde esta pantalla.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Agregar imagen:</strong> "Nueva Imagen" y selecciona categoría</li>
                                <li><strong>Editar imagen:</strong> Modifica título y categoría</li>
                                <li><strong>Eliminar imagen:</strong> Elimina imágenes que ya no necesites</li>
                            </ul>

                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Galería de una cabaña:</strong> para las fotos del slider interno de una cabaña,
                                entra a <strong>Cabañas</strong> y pulsa el botón <i class="bi bi-images"></i> de esa cabaña.
                                También se abre automáticamente al crear una cabaña nueva.
                            </div>
                            
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Consejo:</strong> Usa imágenes de buena calidad (1920x1080px) organizadas por categorías.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-images" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Organización de imágenes</h6>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between small">
                                        <span class="badge bg-info">general</span>
                                        <span>Imágenes de portada</span>
                                    </div>
                                    <div class="d-flex justify-content-between small mt-1">
                                        <span class="badge bg-success">coihue</span>
                                        <span>Fotos de Coihue</span>
                                    </div>
                                    <div class="d-flex justify-content-between small mt-1">
                                        <span class="badge bg-warning">excursion</span>
                                        <span>Fotos de excursiones</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 4: EQUIPAMIENTO
    ============================================================ -->
    <div id="amenities" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">4</span> Equipamiento</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-list-check me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Aquí gestionas el catálogo general de equipamiento y servicios. Luego, en cada cabaña, eliges cuáles de estos items tiene.</p>
                            
                            <h6 class="mt-3">📋 Campos de cada servicio:</h6>
                            <ul>
                                <li><strong>Nombre:</strong> En español e inglés</li>
                                <li><strong>Descripción:</strong> Breve explicación del servicio</li>
                                <li><strong>Icono:</strong> Ícono de Bootstrap Icons (ej: <code>bi bi-wifi</code>)</li>
                                <li><strong>Imagen:</strong> Opcional, una imagen representativa</li>
                                <li><strong>Orden:</strong> Posición en la lista</li>
                                <li><strong>Estado:</strong> Activo o inactivo</li>
                            </ul>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Agregar servicio:</strong> "Nuevo Item"</li>
                                <li><strong>Editar servicio:</strong> Modifica cualquier dato</li>
                                <li><strong>Eliminar servicio:</strong> Elimina servicios que ya no ofrezcas</li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Importante:</strong> este catálogo es general. Para asignar qué equipamiento
                                tiene cada cabaña, entra a <strong>Cabañas → Editar</strong> y marca los items
                                correspondientes en la sección "Equipamiento de esta cabaña". La página pública de
                                cada cabaña muestra solo lo que tenga asignado.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-list-check" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Catálogo general</h6>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center border-bottom py-1">
                                        <i class="bi bi-wifi me-2"></i>
                                        <span>Wifi</span>
                                    </div>
                                    <div class="d-flex align-items-center border-bottom py-1">
                                        <i class="bi bi-tv me-2"></i>
                                        <span>Smart TV</span>
                                    </div>
                                    <div class="d-flex align-items-center border-bottom py-1">
                                        <i class="bi bi-water me-2"></i>
                                        <span>Hot Tub</span>
                                    </div>
                                    <div class="d-flex align-items-center py-1">
                                        <i class="bi bi-cup-hot me-2"></i>
                                        <span>Desayuno Incluido</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 5: RESERVAS
    ============================================================ -->
    <div id="reservations" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">5</span> Reservas</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-calendar-check me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Muestra todas las reservas realizadas por los clientes desde el sitio web.</p>
                            
                            <h6 class="mt-3">🎯 Estados de reserva:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Estado</th>
                                            <th>Color</th>
                                            <th>Significado</th>
                                            <th>¿Qué hacer?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-warning">Pendiente</span></td>
                                            <td>🟡 Amarillo</td>
                                            <td>Esperando tu confirmación</td>
                                            <td><strong>¡Revisar y confirmar!</strong></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">Confirmada</span></td>
                                            <td>🟢 Verde</td>
                                            <td>Reserva aceptada</td>
                                            <td>Preparar para la llegada</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">Cancelada</span></td>
                                            <td>🔴 Rojo</td>
                                            <td>Reserva cancelada</td>
                                            <td>Archivar</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Completada</span></td>
                                            <td>⚪ Gris</td>
                                            <td>Estadía finalizada</td>
                                            <td>Archivar</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Cambiar estado:</strong> Usa el botón <i class="bi bi-gear"></i> y selecciona el nuevo estado</li>
                                <li><strong>Ver detalles:</strong> Haz clic en la reserva para ver más información</li>
                                <li><strong>Eliminar:</strong> Elimina reservas que ya no sean necesarias</li>
                            </ul>
                            
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Consejo:</strong> Cuando recibas una nueva reserva, cámbiala a "Confirmada" para que el cliente sepa que fue aceptada.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-calendar-check" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Flujo de reserva</h6>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-warning me-2">1</span>
                                        <span>Cliente envía reserva</span>
                                    </div>
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-warning me-2">2</span>
                                        <span>Tú la revisas en el admin</span>
                                    </div>
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-success me-2">3</span>
                                        <span>Confirmas la reserva</span>
                                    </div>
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-secondary me-2">4</span>
                                        <span>Completas la estadía</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 6: CONFIGURACIÓN
    ============================================================ -->
    <div id="settings" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">6</span> Configuración</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-gear-fill me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Controla los textos y datos generales del sitio web. Todo lo que cambies aquí se actualiza automáticamente.</p>
                            
                            <h6 class="mt-3">📋 Configuraciones disponibles:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Configuración</th>
                                            <th>¿Para qué sirve?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Título del sitio (ES/EN)</strong></td>
                                            <td>El título que aparece en la pestaña del navegador</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Descripción (ES/EN)</strong></td>
                                            <td>Texto de la sección "Sobre Nosotros"</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Teléfono de contacto</strong></td>
                                            <td>Número que aparece en el sitio y en los emails</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email de contacto</strong></td>
                                            <td>Correo para recibir reservas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dirección</strong></td>
                                            <td>Dirección física de las cabañas</td>
                                        </tr>
                                        <tr>
                                            <td><strong>URL de Facebook/Instagram</strong></td>
                                            <td>Enlaces a tus perfiles sociales</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Número WhatsApp</strong></td>
                                            <td>Para el botón flotante de WhatsApp</td>
                                        </tr>
                                        <tr>
                                            <td><strong>URL del Video</strong></td>
                                            <td>Video que se muestra en el Hero (YouTube)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Los cambios se reflejan inmediatamente en el sitio web.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-gear-fill" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Datos del sitio</h6>
                                </div>
                                <div class="mt-2 small">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Teléfono:</span>
                                        <span class="text-muted">(+56) 9-93499820</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Email:</span>
                                        <span class="text-muted">reservaciones@...</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Redes:</span>
                                        <span class="text-muted">Facebook, Instagram</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 7: DOCUMENTOS
    ============================================================ -->
    <div id="documents" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">7</span> Documentos</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-file-pdf me-2 text-danger"></i>¿Qué es?</h5>
                            <p>Gestiona los documentos PDF que se muestran en el sitio web, como la guía de turismo.</p>
                            
                            <h6 class="mt-3">📄 Documentos disponibles:</h6>
                            <ul>
                                <li><strong>PDF en Español:</strong> Guía de turismo para usuarios en español</li>
                                <li><strong>PDF en Inglés:</strong> Guía de turismo para usuarios en inglés</li>
                            </ul>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Subir PDF:</strong> Selecciona un archivo PDF y haz clic en "Subir"</li>
                                <li><strong>Ver PDF:</strong> Haz clic en "Ver PDF" para abrirlo en el navegador</li>
                                <li><strong>Descargar PDF:</strong> Haz clic en "Descargar" para guardarlo en tu computadora</li>
                                <li><strong>Eliminar PDF:</strong> Haz clic en el botón <i class="bi bi-trash text-danger"></i> para eliminarlo</li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Requisitos:</strong> Archivos PDF, máximo 5MB. Los PDFs se muestran en la sección "Turismo".
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-file-pdf" style="font-size: 3rem; color: #dc3545;"></i>
                                    <h6 class="mt-2">Estado de PDFs</h6>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-success me-2">✅</span>
                                        <span>PDF Español: <?= getSetting('tourism_pdf_es') ? 'Subido' : 'No subido' ?></span>
                                    </div>
                                    <div class="d-flex align-items-center py-1">
                                        <span class="badge bg-success me-2">✅</span>
                                        <span>PDF Inglés: <?= getSetting('tourism_pdf_en') ? 'Subido' : 'No subido' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 8: USUARIOS
    ============================================================ -->
    <div id="users" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">8</span> Usuarios</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-people-fill me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Gestiona los usuarios que tienen acceso al panel de administración.</p>
                            
                            <h6 class="mt-3">👤 Roles disponibles:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rol</th>
                                            <th>Color</th>
                                            <th>Permisos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-danger">Administrador</span></td>
                                            <td>🔴 Rojo</td>
                                            <td>Acceso total a todas las secciones</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary">Editor</span></td>
                                            <td>⚪ Gris</td>
                                            <td>Gestiona contenido pero no usuarios</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-3">⚡ Acciones disponibles:</h6>
                            <ul>
                                <li><strong>Agregar usuario:</strong> Crea nuevas cuentas para el equipo</li>
                                <li><strong>Eliminar usuario:</strong> Elimina cuentas que ya no se necesiten</li>
                                <li><strong>Ver usuarios:</strong> Lista de todas las cuentas activas</li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Nota:</strong> No puedes eliminar tu propio usuario. Solo el admin puede gestionar otros usuarios.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-people-fill" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Roles y permisos</h6>
                                </div>
                                <div class="mt-2">
                                    <div class="border-bottom py-1">
                                        <span class="badge bg-danger">Admin</span>
                                        <span class="small text-muted ms-2">Acceso total</span>
                                    </div>
                                    <div class="py-1">
                                        <span class="badge bg-secondary">Editor</span>
                                        <span class="small text-muted ms-2">Acceso limitado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 9: MI PERFIL
    ============================================================ -->
    <div id="profile" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">9</span> Mi Perfil</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><i class="bi bi-person-circle me-2 text-primary"></i>¿Qué es?</h5>
                            <p>Gestiona tu información personal y contraseña de acceso al panel de administración.</p>
                            
                            <h6 class="mt-3">📋 Información que puedes ver:</h6>
                            <ul>
                                <li><strong>Usuario:</strong> Tu nombre de usuario</li>
                                <li><strong>Email:</strong> Tu correo electrónico</li>
                                <li><strong>Rol:</strong> Administrador o Editor</li>
                            </ul>
                            
                            <h6 class="mt-3">🔒 Cambiar contraseña:</h6>
                            <ul>
                                <li>Ingresa tu <strong>contraseña actual</strong></li>
                                <li>Ingresa tu <strong>nueva contraseña</strong> (mínimo 6 caracteres)</li>
                                <li>Confirma tu <strong>nueva contraseña</strong></li>
                                <li>Haz clic en "Actualizar Contraseña"</li>
                            </ul>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Mantén tu contraseña segura y cámbiala periódicamente.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded-3">
                                <div class="text-center">
                                    <i class="bi bi-person-circle" style="font-size: 3rem; color: #f8c146;"></i>
                                    <h6 class="mt-2">Tu perfil</h6>
                                </div>
                                <div class="mt-2 small">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Usuario:</span>
                                        <span class="text-muted"><?= $_SESSION['admin_username'] ?? 'admin' ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Rol:</span>
                                        <span class="badge bg-danger">Administrador</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Estado:</span>
                                        <span class="badge bg-success">Activo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SECCIÓN 10: MANUAL
    ============================================================ -->
    <div id="manual" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h4 class="mb-0"><span class="badge bg-primary me-2">10</span> Manual</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="bi bi-book me-2 text-primary"></i>¿Qué es esta sección?</h5>
                            <p>¡Estás aquí! Esta es la guía de usuario que te está ayudando a entender el sistema.</p>
                            
                            <h6 class="mt-3">📖 ¿Para qué sirve?</h6>
                            <ul>
                                <li><strong>Capacitación:</strong> Aprende a usar todas las herramientas del admin</li>
                                <li><strong>Referencia rápida:</strong> Consulta dudas sobre cualquier sección</li>
                                <li><strong>Actualizaciones:</strong> Conoce nuevas funcionalidades del sistema</li>
                            </ul>
                            
                            <h6 class="mt-3">💡 Consejos para usar el manual:</h6>
                            <ul>
                                <li>Usa el <strong>índice rápido</strong> para ir directamente a la sección que necesitas</li>
                                <li>Busca palabras clave con <kbd>Ctrl + F</kbd> (Windows) o <kbd>Cmd + F</kbd> (Mac)</li>
                                <li>Revisa las <strong>alertas</strong> con consejos y advertencias importantes</li>
                            </ul>
                            
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-star-fill me-2 text-warning"></i>
                                <strong>¡Pro Tip!</strong> Marca esta página como favorita para tener siempre la guía a mano.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    CONSEJOS GENERALES
    ============================================================ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body">
                    <h4><i class="bi bi-star-fill text-warning me-2"></i>Consejos para administrar tu sitio</h4>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-calendar-check" style="font-size: 2rem; color: #f8c146;"></i>
                                <h6>Revisa el dashboard diariamente</h6>
                                <p class="small text-muted">Las reservas pendientes requieren tu atención rápida para no perder clientes.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-images" style="font-size: 2rem; color: #f8c146;"></i>
                                <h6>Mantén las imágenes actualizadas</h6>
                                <p class="small text-muted">Sube fotos nuevas regularmente para mostrar las cabañas en su mejor estado.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-currency-dollar" style="font-size: 2rem; color: #f8c146;"></i>
                                <h6>Actualiza precios estacionalmente</h6>
                                <p class="small text-muted">Revisa y ajusta los precios según temporada alta/baja.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    BOTÓN DE AYUDA
    ============================================================ -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-question-circle" style="font-size: 2rem; color: #f8c146;"></i>
                    <h5 class="mt-2">¿Necesitas ayuda adicional?</h5>
                    <p class="text-muted">Si tienes dudas que no están cubiertas en este manual, contacta al soporte técnico.</p>
                    <a href="mailto:helimirlopez@gmail.com" class="btn btn-primary">
                        <i class="bi bi-envelope me-2"></i> Contactar Soporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>