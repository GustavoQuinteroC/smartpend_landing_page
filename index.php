<?php
$NOINDEX = false;

require_once __DIR__ . '/_smartpend.php';
require_once __DIR__ . '/_seo.php';

$AHORRO  = a_ahorro_anual();
$URL_APP = a_url_panel();
$PROD    = 'smartpend';

$DESDE_TXT = '$' . a_num(sp_tarifa_min());

$TITULO  = 'SmartPend: gestión de pendientes y proyectos | Alkes';
$DESC    = 'Sistema en la nube para dar seguimiento a pendientes y proyectos con folio, historial, formularios públicos e IA. Desde ' . $DESDE_TXT . ' MXN por usuario al mes. Pruébalo.';
$DESC_OG = 'Sistema en la nube para dar seguimiento a pendientes y proyectos con folio, historial, formularios públicos e IA. Desde ' . $DESDE_TXT . ' MXN por usuario al mes.';

/* Capacidades en pestañas: icono, título y descripción de cada punto. */
$CAPACIDADES = array(
    array('id' => 'operacion', 'tab' => 'Operación diaria', 'eyebrow' => 'Operación diaria',
        'h3' => 'Todo el contexto de un pendiente en una sola pantalla',
        'p' => 'Quien atiende no tiene que reconstruir la historia: ve la solicitud, los archivos, las notas previas y cada cambio de estado con su responsable.',
        'items' => array(
            array('user-check', 'Asignación con responsable y estado', 'Cambios de estado controlados y visibles para todo el equipo.'),
            array('paperclip', 'Archivos y notas por pendiente', 'Adjunta documentos o pega imágenes directo desde el portapapeles.'),
            array('history', 'Historial de cada movimiento', 'Quién hizo qué y cuándo, sin depender de la memoria de nadie.'),
        ),
        'img' => 'assets/img/dashboard.jpg', 'alt' => 'Panel principal de SmartPend con indicadores y gráficas de pendientes', 'w' => 1568, 'h' => 765,
        'cap' => 'Panel principal con el estado de la operación'),
    array('id' => 'ia', 'tab' => 'Inteligencia artificial', 'eyebrow' => 'Inteligencia artificial',
        'h3' => 'Registrar deja de ser el cuello de botella',
        'p' => 'La IA está integrada donde realmente ahorra tiempo: al capturar, al clasificar y al planear un proyecto. No es un chat aparte, es parte del flujo.',
        'items' => array(
            array('mic', 'Dictado por voz', 'Cuéntale a la IA que solicita el cliente/usuario y el problema que presenta, y la IA reformateará el mensaje para registrar el pendiente de forma clara para el técnico.'),
            array('search', 'Búsqueda inteligente de servicio', 'La IA lee la descripción y sugiere el servicio que mejor aplica antes de guardar.'),
            array('sparkle', 'Pendientes de un proyecto con IA', 'Describe el proyecto y recibe la lista de pendientes propuesta, revisable y editable antes de crearla.'),
        ),
        'img' => 'assets/img/dictado.jpg', 'alt' => 'Dictado por voz de SmartPend transcribiendo un pendiente', 'w' => 1568, 'h' => 765,
        'cap' => 'Dictado por voz durante el registro'),
    array('id' => 'organizacion', 'tab' => 'Organización', 'eyebrow' => 'Organización',
        'h3' => 'Calendario, recurrencias y proyectos',
        'p' => 'Más allá del listado, SmartPend ubica cada pendiente en el tiempo: cuándo vence, si se repite y a qué proyecto pertenece.',
        'items' => array(
            array('calendar', 'Calendario por color de estado', 'Vista de mes, semana, día o lista con cada pendiente coloreado según su estado.'),
            array('repeat', 'Pendientes programados', 'Define una recurrencia en días, semanas, meses o años y el sistema genera el pendiente solo.'),
            array('layers', 'Expediente del proyecto', 'Notas, archivos, historial e involucrados del proyecto, con progreso calculado según los pendientes resueltos.'),
        ),
        'img' => 'assets/img/calendario.jpg', 'alt' => 'Calendario de SmartPend con pendientes coloreados por estado', 'w' => 1568, 'h' => 765,
        'cap' => 'Calendario con el estado de cada pendiente'),
    array('id' => 'seguimiento', 'tab' => 'Seguimiento', 'eyebrow' => 'Seguimiento',
        'h3' => 'Etiquetas, seguidores y avisos dentro de la app',
        'p' => 'Clasifica lo que importa y entérate cuando se mueve, sin depender de que alguien te copie en un correo.',
        'items' => array(
            array('tag', 'Etiquetas de color propias', 'Clasifica pendientes, proyectos y tareas programadas con etiquetas de color propias de tu organización.'),
            array('bell', 'Seguidores por pendiente o proyecto', 'Suma personas a un registro y les llega el aviso dentro de la app cuando algo cambia.'),
            array('sparkle', 'Etiquetado automático por IA', 'La IA lee la descripción y propone las etiquetas que aplican, también en el dictado por voz.'),
        ),
        'img' => 'assets/img/etiquetas.jpg', 'alt' => 'Pendiente de SmartPend con etiquetas de color y seguidores asignados', 'w' => 1568, 'h' => 765,
        'cap' => 'Etiquetas y seguidores en el detalle de un pendiente'),
    array('id' => 'reportes', 'tab' => 'Reportes', 'eyebrow' => 'Reportes',
        'h3' => 'Diez reportes, con periodo comparado',
        'p' => 'Cada reporte se compara automáticamente contra el periodo anterior equivalente, y se exporta cuando lo necesitas para juntas o auditorías.',
        'items' => array(
            array('chart', 'Diez reportes por familia', 'Resumen, cumplimiento de SLA, vencidos y en riesgo, antigüedad, desempeño por usuario, actividad por cliente, demanda por servicio, avance de proyectos, salud de programados y bitácora de auditoría.'),
            array('history', 'Comparación contra el periodo anterior', 'Cada cifra se lee junto a su equivalente del periodo previo, no aislada.'),
            array('report', 'Exportación a Excel, CSV o PDF', 'El mismo reporte, en el formato que tu equipo o tu cliente necesite.'),
        ),
        'img' => 'assets/img/reportes.jpg', 'alt' => 'Reporte de desempeño por usuario de SmartPend con exportación a Excel, CSV y PDF', 'w' => 1568, 'h' => 765,
        'cap' => 'Reportes con comparación de periodo y exportación'),
    array('id' => 'tudia', 'tab' => 'Tu día', 'eyebrow' => 'Tu día',
        'h3' => 'Tablero, búsqueda y avisos a tu medida',
        'p' => 'Ve tus pendientes como columnas de trabajo, encuentra cualquier folio desde cualquier pantalla y elige de qué quieres enterarte.',
        'items' => array(
            array('layers', 'Tablero de pendientes', 'Una columna por estado; arrastra una tarjeta para iniciarlo, resolverlo o cancelarlo, con las mismas reglas del formulario.'),
            array('search', 'Búsqueda en todo el sistema', 'Folios, clientes, proyectos y usuarios desde la barra de arriba, solo con lo que tú puedes abrir.'),
            array('bell', 'Tú eliges qué avisos recibir', 'Un interruptor por tipo de aviso: estados, asignaciones, notas, archivos, etiquetas, cambios de datos y lo que haces tú mismo.'),
        ),
        'img' => 'assets/img/tablero.jpg', 'alt' => 'Tablero de pendientes de SmartPend con una columna por estado', 'w' => 1568, 'h' => 765,
        'cap' => 'Tablero: una columna por estado'),
);

$SOSTIENE = array(
    array('lock', 'Permisos por grupos', 'Organiza usuarios en grupos y define qué áreas ven y gestionan información de otras.'),
    array('history', 'Historial de cada pendiente', 'Quién hizo qué y cuándo, registrado automáticamente en cada movimiento.'),
    array('filter', 'Filtros rápidos', 'Alterna estados con pestañas de un clic y filtra cada columna: contiene, empieza con, vacío y más.'),
    array('building', 'Clientes y servicios', 'Asocia cada pendiente a su cliente y al servicio correspondiente.'),
    array('route', 'Accesos cruzados', 'Salta entre proyectos, pendientes relacionados y usuarios desde cualquier registro.'),
    array('paperclip', 'Archivos y notas', 'Adjunta documentos o pega imágenes directo desde el portapapeles, en pendientes y proyectos.'),
);

$AVANZADAS = array(
    array('layers', 'Gestión de proyectos', 'Organiza tareas por proyectos con seguimiento de progreso.'),
    array('users', 'Multiusuario', 'Todo el equipo sobre la misma información; la campana se actualiza sola.'),
    array('lock', 'Seguridad Argon2id', 'Las contraseñas se protegen con Argon2id.'),
    array('building', 'Gestión integral', 'Empresas, servicios, proyectos y usuarios.'),
    array('clock', 'Acceso desde el navegador', 'Entra desde cualquier equipo, sin instalar nada.'),
    array('mic', 'Dictado inteligente por voz', 'Registra tareas con voz y deja que la IA las organice.'),
    array('shield', 'Roles y permisos a tu medida', 'Define por rol qué se puede ver, crear, editar y dar de baja en cada módulo, con ajustes para una persona concreta.'),
    array('route', 'Visibilidad entre áreas', 'Reglas por grupo para colaboración controlada entre departamentos.'),
    array('book', 'Manual dentro del sistema', 'La ayuda se abre en la pantalla donde estás, con buscador.'),
    array('report', 'Imprime como documento', 'Un pendiente o un proyecto sale en papel con el logo de tu organización y sin los botones de la pantalla.'),
    array('box', 'Duplicar como plantilla', 'Abre un alta nueva con los datos, etiquetas y seguidores de otro registro ya cargados.'),
    array('zap', 'Alta rápida de catálogos', 'Crea el cliente, servicio, categoría o etiqueta que falte sin salir del formulario que estás llenando.'),
);

function sp_items($lista)
{
    $h = '';
    foreach ($lista as $i) {
        $h .= '<div class="item">' . a_icon($i[0]) . '<div><h4>' . a_e($i[1]) . '</h4><p>' . a_e($i[2]) . '</p></div></div>';
    }
    return $h;
}
?>
<?php
// SEO y GEO: datos para seo_head() y seo_enriquecer() (ver _seo.php).
$SEO_PAG = seo_de_pagina(SP_SITIO, isset($CAPACIDADES) ? $CAPACIDADES : array(), array($SOSTIENE, $AVANZADAS));
$SEO = array(
    'sitio' => SP_SITIO, 'nombre' => 'SmartPend', 'titulo' => $TITULO, 'noindex' => $NOINDEX,
    'subcategoria' => 'Gestión de pendientes, tickets y proyectos', 'dispositivos' => 'Computadora, teléfono y tableta', 'requisitos' => 'Navegador web moderno con conexión a internet',
    'alt' => 'Vista de SmartPend, software en la nube de Alkes',
    'palabras' => array('software de gestión de pendientes', 'control de pendientes', 'seguimiento de tareas', 'sistema de tickets', 'mesa de ayuda', 'gestión de solicitudes con folio', 'gestión de proyectos', 'dictado por voz con IA', 'inteligencia artificial para tareas', 'formularios públicos', 'software para equipos de trabajo', 'sistema en la nube', 'SaaS México', 'software Guadalajara', 'Alkes'),
    'version' => seo_version(__DIR__), 'funciones' => $SEO_PAG['funciones'], 'capturas' => $SEO_PAG['capturas'],
);
?>
<!doctype html>
<html lang="es" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= a_e($TITULO) ?></title>

<meta name="description" content="<?= a_e($DESC) ?>">
<meta name="author" content="Alkes">
<meta name="theme-color" content="#061c6b">
<meta name="format-detection" content="telephone=no">
<?php if ($NOINDEX): ?><meta name="robots" content="noindex,nofollow">
<?php endif; ?>
<link rel="canonical" href="<?= SP_SITIO ?>/">

<meta property="og:type" content="website">
<meta property="og:site_name" content="SmartPend">
<meta property="og:url" content="<?= SP_SITIO ?>/">
<meta property="og:title" content="<?= a_e($TITULO) ?>">
<meta property="og:description" content="<?= a_e($DESC_OG) ?>">
<meta property="og:image" content="<?= SP_SITIO ?>/assets/img/og-image.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="SmartPend, sistema de gestión de pendientes y proyectos">
<meta property="og:locale" content="es_MX">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= a_e($TITULO) ?>">
<meta name="twitter:description" content="<?= a_e($DESC_OG) ?>">
<meta name="twitter:image" content="<?= SP_SITIO ?>/assets/img/og-image.png">
<?= seo_head($SEO) ?>
<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">

<link rel="preload" href="assets/fonts/lexend-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/source-sans-3-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="assets/css/smartpend.css">

<script type="application/ld+json"><?= sp_jsonld($SEO) ?></script>

<script>
(function () {
    var r = document.documentElement;
    r.className = r.className.replace('no-js', 'js');
    try {
        var t = localStorage.getItem('tema');
        if (t === 'dark' || t === 'light') { r.setAttribute('data-theme', t); }
    } catch (e) {}
})();
</script>
</head>
<body>

<a class="skip" href="#main">Saltar al contenido</a>

<header class="nav" id="nav">
    <div class="container nav__in">
        <a class="brand" href="#inicio"><?= sp_logo(34) ?><span>SmartPend</span></a>
        <a class="brand__by" href="<?= ALKES_SITIO ?>">por <strong>Alkes</strong></a>
        <nav class="nav__links" aria-label="Secciones">
            <a class="nav__link" href="#producto">Producto</a>
            <a class="nav__link" href="#capacidades">Capacidades</a>
            <a class="nav__link" href="#precios">Precios</a>
            <a class="nav__link" href="#preguntas">Preguntas</a>
            <a class="nav__link" href="#novedades">Novedades</a>
            <a class="nav__link" href="#contacto">Contacto</a>
        </nav>
        <div class="nav__actions">
            <?= a_tema() ?>
            <a class="btn btn--line btn--sm" href="<?= a_e($URL_APP) ?>">Iniciar sesión</a>
            <a class="btn btn--primary btn--sm" href="#precios">Ver precios</a>
        </div>
        <button class="nav__burger" type="button" id="navToggle" aria-expanded="false" aria-controls="navPanel" aria-label="Abrir menú">
            <?= a_icon('menu', 'i-menu') ?><?= a_icon('close', 'i-close') ?>
        </button>
    </div>
    <div class="nav__panel container" id="navPanel">
        <a class="nav__link" href="#producto">Producto</a>
        <a class="nav__link" href="#capacidades">Capacidades</a>
        <a class="nav__link" href="#precios">Precios</a>
        <a class="nav__link" href="#preguntas">Preguntas</a>
        <a class="nav__link" href="#novedades">Novedades</a>
        <a class="nav__link" href="#contacto">Contacto</a>
        <a class="nav__link" href="#ecosistema">Ecosistema Alkes</a>
        <div class="nav__panel-actions">
            <?= a_tema() ?>
            <a class="btn btn--line" href="<?= a_e($URL_APP) ?>">Iniciar sesión</a>
            <a class="btn btn--primary" href="#precios">Ver precios</a>
        </div>
    </div>
</header>

<main id="main">

<!-- Hero ------------------------------------------------------------------ -->
<section class="hero" id="inicio">
    <div class="container">
        <div class="hero__grid">
            <div class="hero__body">
                <p class="eyebrow" data-reveal>Gestión de pendientes y proyectos para soporte, TI y operaciones</p>
                <h1 data-reveal>Nada se pierde entre <em>correos</em> y juntas</h1>
                <p class="lede" data-reveal>Registra cada solicitud, asígnala y sigue su avance hasta cerrarla. Quien reporta consulta su folio sin llamar. Desde <?= a_e($DESDE_TXT) ?> MXN por usuario al mes.</p>
                <div class="row" data-reveal>
                    <a class="btn btn--primary btn--lg" href="<?= a_e($URL_APP) ?>" data-open-demo><?= a_icon('play') ?>Probar el demo</a>
                    <a class="btn btn--line btn--lg" href="#precios">Ver precios</a>
                </div>
                <div class="row" data-reveal>
                    <a class="link" href="<?= a_e(sp_url_agendar()) ?>">Agendar demostración guiada<?= a_icon('arrow') ?></a>
                    <a class="link" href="#novedades">Ver novedades<?= a_icon('arrow') ?></a>
                </div>
                <ul class="hero__facts" data-reveal>
                    <li><?= a_icon('check') ?>Sin instalar nada</li>
                    <li><?= a_icon('check') ?>Actualizaciones incluidas</li>
                    <li><?= a_icon('check') ?>Soporte del equipo que lo desarrolla</li>
                </ul>
            </div>
            <div class="hero__aside" data-reveal>
                <div class="scene" data-scene>
  <svg class="scene__svg" viewBox="0 0 560 420" role="img" aria-label="Animación: mensajes, correos y llamadas llegan desordenados, se convierten en tarjetas con folio en un tablero, una persona mueve un pendiente hasta Resuelto y la persona que lo reportó consulta su folio y ve que ya está resuelto.">
    <defs><clipPath id="sc-tr"><rect x="384" y="200" width="48" height="80" rx="14"/></clipPath><clipPath id="p2-tr"><path d="M387 213Q387 200 401 200H417Q431 200 431 213L428 277H390Z"/></clipPath></defs>
    <rect class="sc-wall" x="0" y="0" width="560" height="330"/>
    <rect class="sc-wall2" x="0" y="262" width="560" height="68"/><path class="sc-line" d="M0 262H560"/>
    <rect class="sc-floor" x="0" y="330" width="560" height="90"/><path class="sc-line" d="M0 330H560"/>
    <path class="sc-tile" d="M0 356H560M0 384H560M120 330L72 420M290 330L284 420M450 330L500 420"/>
    <ellipse class="sc-shd" cx="438" cy="333" rx="36" ry="5"/>
    <g class="sp-all">
      <rect class="sp-bd" x="20" y="20" width="302" height="234" rx="14"/>
      <text class="sp-bt" x="34" y="42">Tablero</text><rect class="sp-tg" x="258" y="30" width="52" height="16" rx="8"/><text class="sp-tgt" x="284" y="41.500" text-anchor="middle">Equipo</text>
      <rect class="sp-col" x="28" y="54" width="94" height="192" rx="8"/><rect class="sp-col" x="126" y="54" width="94" height="192" rx="8"/><rect class="sp-col" x="224" y="54" width="94" height="192" rx="8"/>
      <text class="sp-ch" x="36" y="70">Pendiente</text><text class="sp-ch" x="134" y="70">En proceso</text><text class="sp-ch gr" x="232" y="70">Resuelto</text>
      <circle class="sp-cd" cx="116" cy="66" r="3"/><circle class="sp-cd" cx="214" cy="66" r="3"/><circle class="sp-cd g" cx="312" cy="66" r="3"/>
      <g class="sp-c sp-c1"><rect class="sp-cb" x="34" y="82" width="82" height="40" rx="8"/><text class="sp-fo" x="42" y="96">TK0041</text><text class="sp-ti" x="42" y="109">Impresora</text><rect class="sp-ln" x="42" y="113" width="40" height="3" rx="1.500"/></g><g class="sp-c sp-c3"><rect class="sp-cb" x="34" y="178" width="82" height="40" rx="8"/><text class="sp-fo" x="42" y="192">TK0043</text><text class="sp-ti" x="42" y="205">Cotizar licencias</text><rect class="sp-ln" x="42" y="209" width="40" height="3" rx="1.500"/></g><g class="sp-c sp-c2"><rect class="sp-cb" x="34" y="130" width="82" height="40" rx="8"/><text class="sp-fo" x="42" y="144">TK0042</text><text class="sp-ti" x="42" y="157">Alta usuario</text><rect class="sp-ln" x="42" y="161" width="40" height="3" rx="1.500"/><g class="sp-av"><circle class="sp-avb" cx="100" cy="144" r="7"/><circle class="sc-skinf" cx="100" cy="143" r="4.500"/><path class="sc-hair" d="M95.500 142a4.500 4.500 0 0 1 9 0z"/></g><g class="sp-ok"><circle class="sc-tagc" cx="101" cy="162" r="6"/><path class="sc-ck3" d="M98 162l2.200 2.200 4-4.400"/></g></g>
      <g class="sp-m sp-m1"><rect class="sp-mb" x="57" y="90" width="36" height="25" rx="4"/><path class="sp-mp" d="M57 92l18 13 18-13"/></g><g class="sp-m sp-m2"><rect class="sp-mb" x="55" y="137" width="40" height="26" rx="9"/><path class="sp-mb" d="M62 163l-3 8 10-8z"/><circle class="sp-md" cx="67" cy="150" r="2.200"/><circle class="sp-md" cx="75" cy="150" r="2.200"/><circle class="sp-md" cx="83" cy="150" r="2.200"/></g><g class="sp-m sp-m3"><circle class="sp-mb" cx="75" cy="198" r="15"/><path class="sp-mp" d="M69 192q-2 4 2 9t9 3l2-4-4-3-2 2q-3-1-4-5l2-2-3-4z"/></g>
      <g class="sp-cur"><path class="sp-cp" d="M0 0l0 18 5-4 3.500 8 3-1.500-3.500-8 6.500-.5z" transform="translate(96 150)"/></g>
      <g class="sp-tag sc-tag"><rect class="sc-tagb" x="30" y="274" width="184" height="32" rx="16"/><circle class="sc-tagc" cx="50" cy="290" r="9.500"/><path class="sc-ck" d="M45 290l3.500 3.500 6.500-7"/><text class="sc-txt2" x="66" y="295">TK0042 resuelto</text></g>
      <g class="sp-person"><g class="sp2" transform="translate(30,0)">
      <g class="sc-armb sp2-b"><path class="p2-sleeve" style="stroke-width:13" d="M426 218L432 242"/><path class="p2-fore" style="stroke-width:9" d="M432 242L431 262"/><ellipse class="p2-skin" cx="431" cy="266" rx="5.2" ry="6"/></g>
      <g class="sc-legl sp2-ll">
        <path class="sc-pant" d="M390.500 270H405.500L404 323H391.500Z"/>
        <path class="sc-shoe" d="M406 322L392 322Q386 324 382 329Q380.500 332.500 385 332.500H408Q411.500 332.500 411.500 329Q411.500 324 406 322Z"/>
        <path class="p2-sole" d="M381.500 330.200H411.200L411.400 332.500H384Q381.600 332.500 381.500 330.200Z"/>
      </g>
      <g class="sc-legr sp2-lr">
        <path class="sc-pant" d="M411.500 270H426.500L425 323H412.500Z"/>
        <path class="p2-shade" d="M411.500 270H426.500L425 323H412.500Z"/>
        <path class="sc-shoe" d="M427 322L413 322Q407 324 403 329Q401.500 332.500 406 332.500H429Q432.500 332.500 432.500 329Q432.500 324 427 322Z"/>
        <path class="p2-sole" d="M402.500 330.200H432.200L432.400 332.500H405Q402.600 332.500 402.500 330.200Z"/>
      </g>
      <g class="sc-body">
        <path class="sc-shirt" d="M387 213Q387 200 401 200H417Q431 200 431 213L428 277H390Z"/>
        <path class="sc-shirt2" clip-path="url(#p2-tr)" d="M414 196H436V281H408Q415 240 414 196Z"/>
        <path class="p2-placket" d="M408 211V268"/>
        <path class="sc-belt" d="M390 268H428L428.200 275H389.800Z"/><rect class="sc-buckle" x="404" y="268" width="8" height="7" rx="1.500"/>
        <path class="p2-lan" d="M405 211L411 233M417 211L412 233"/><rect class="p2-badge" x="406" y="232" width="12" height="15" rx="2.500"/><rect class="p2-badgel" x="408.500" y="235" width="7" height="2.200" rx="1.100"/><rect class="p2-badgel" style="opacity:.45" x="408.500" y="239.500" width="7" height="1.800" rx=".9"/>
        <path class="p2-skin" d="M401.500 187H414.500V201Q408 207 401.500 201Z"/>
        <path class="p2-shade" d="M401.500 187H414.500V193Q408 197 401.500 193Z"/>
        <path class="p2-collar" d="M399.500 200.500L408 213L416.500 200.500L412 199Q408 203 404 199Z"/>
        <ellipse class="p2-skin" cx="408" cy="175" rx="15.500" ry="17.500"/>
        <path class="p2-hair" d="M391.500 172C389.500 160 397 155.500 408 155.500C420 155.500 426.500 163.500 425.500 177C425.200 181.500 424.500 185 422 187.500C421.800 182 419.500 176.500 414 172.500C408 168.500 398.500 170 391.500 172Z"/>
        <path class="p2-hair" d="M396 164.500Q404 158.500 414 162Q405 161 396 164.500Z" style="opacity:.55"/>
        <ellipse class="p2-skin" cx="418.200" cy="180" rx="3.200" ry="4.200"/>
        <path class="p2-shade" d="M416.800 178Q419.500 180 417 183"/>
        <ellipse class="p2-skin" cx="392.600" cy="180.400" rx="2.900" ry="3.300"/>
        <circle class="p2-blush" cx="397" cy="183.500" r="3.400"/>
        <g class="sc-eyeg sp2-eye"><ellipse class="p2-eyew" cx="401.200" cy="177" rx="3.100" ry="3.300"/><circle class="p2-iris" cx="400.200" cy="177.300" r="1.900"/><circle class="p2-eyew" cx="399.500" cy="176.500" r=".65"/></g>
        <path class="p2-line" d="M396.500 172.300Q401 169.800 405.500 171.300"/>
        <path class="p2-mouth sp2-m1" style="opacity:0;stroke-width:1.7" d="M398.5 187.8H403"/>
        <path class="p2-mouth sp2-m2" d="M397 186.200Q401 191 406 186"/>
      </g>
      <g class="sc-arma sp2-a">
        <path class="p2-sleeve" style="stroke-width:14" d="M391 214L369 225"/>
        <path class="p2-cuff" d="M369.600 224.700L367.500 225.800" style="stroke-width:12"/>
        <path class="p2-fore" style="stroke-width:9.500" d="M368.500 225.500L346 226"/>
        <ellipse class="p2-skin" cx="341.500" cy="225.500" rx="6.300" ry="5.400"/>
        <ellipse class="p2-skin" cx="340" cy="219.600" rx="2.200" ry="4" transform="rotate(-20 340 219.600)"/>
        <circle class="sc-shirt" cx="392" cy="214.500" r="8.500" style="fill:var(--sc-shirt)"/>
      </g>
    </g></g>
    </g>
    <g class="sc-card">
      <rect class="sc-cardb" x="326" y="8" width="222" height="148" rx="18"/>
      <rect class="sc-logo" x="340" y="20" width="16" height="16" rx="4"/><path class="sc-ck2" d="M344 28l3 3 5-5.500"/>
      <text class="sc-t0" x="362" y="32.500">SmartPend</text><text class="sc-t3" x="534" y="32.500" text-anchor="end">Seguimiento</text>
      <text class="sc-t1" x="340" y="58">Folio TK0042</text><text class="sc-t2" x="340" y="72">Alta de usuario</text>
      <rect class="sp-trk" x="352" y="86" width="172" height="3" rx="1.500"/><rect class="sp-trf" x="352" y="86" width="172" height="3" rx="1.500"/>
      <g class="sp-s s1"><circle class="sp-sd" cx="352" cy="88" r="7"/><circle class="sp-sc" cx="352" cy="88" r="7"/><path class="sc-ck3" d="M349 88l2 2 3.500-4"/></g>
      <g class="sp-s s2"><circle class="sp-sd" cx="438" cy="88" r="7"/><circle class="sp-sc" cx="438" cy="88" r="7"/><path class="sc-ck3" d="M435 88l2 2 3.500-4"/></g>
      <g class="sp-s s3"><circle class="sp-sd" cx="524" cy="88" r="7"/><circle class="sp-sc" cx="524" cy="88" r="7"/><path class="sc-ck3" d="M521 88l2 2 3.500-4"/></g>
      <text class="sc-t3" x="352" y="108" text-anchor="start">Recibido</text><text class="sc-t3" x="438" y="108" text-anchor="middle">En proceso</text><text class="sc-t3" x="524" y="108" text-anchor="end">Resuelto</text>
      <g class="sc-sent sp-res"><rect class="sc-sentb" x="340" y="120" width="196" height="26" rx="13"/><circle class="sc-sentc" cx="372" cy="133" r="7"/><path class="sc-ck3" d="M368.500 133l2.500 2.500 4.500-5"/><text class="sc-sentt" x="386" y="138">Resuelto sin llamar</text></g>
    </g>
  </svg>
</div>
                <p class="hero__note"><?= a_icon('shield') ?><span><strong>Trazabilidad completa</strong>Cada cambio queda registrado con su autor y su fecha.</span></p>
            </div>
        </div>
    </div>
</section>

<!-- Qué es ---------------------------------------------------------------- -->
<section class="section section--tight" id="producto">
    <div class="container">
        <h2 class="sr-only">Qué es SmartPend, para quién es y qué resuelve</h2>
        <div class="mosaic" data-reveal>
            <div class="card card--big">
                <p class="eyebrow">Qué es</p>
                <h3>Un sistema de pendientes, no otro chat</h3>
                <p>Registras un pendiente, lo asocias a una empresa y un servicio, defines responsable y fecha, y el sistema conserva todo el rastro hasta cerrarlo.</p>
                <p class="muted">SmartPend concentra los pendientes de tu organización en un solo lugar: quién lo pidió, quién responde, en qué va y qué se hizo. Con historial completo de cada movimiento e inteligencia artificial que acelera el registro y la clasificación.</p>
            </div>
            <div class="card card--md">
                <p class="eyebrow">Para quién</p>
                <h3>Equipos que responden a terceros</h3>
                <p class="muted">Áreas de soporte, TI, operaciones y servicio que atienden solicitudes de varios clientes o departamentos y necesitan saber en qué va cada una.</p>
            </div>
            <div class="card card--md">
                <p class="eyebrow">Qué resuelve</p>
                <h3>El seguimiento que hoy vive disperso</h3>
                <p class="muted">Elimina el rastreo por correo, los mensajes sueltos y las hojas de cálculo, y quita las llamadas de «¿cómo va lo mío?» con consulta directa por folio.</p>
            </div>
        </div>
    </div>
</section>

<!-- Tus clientes ---------------------------------------------------------- -->
<section class="section section--tight" id="clientes">
    <div class="container">
        <div class="split" data-reveal>
            <div class="split__body">
                <p class="eyebrow">Tus clientes</p>
                <h2>Que reporten y consulten sin llamarte</h2>
                <p class="muted">Publica un formulario en tu propio sitio para que quien reporta lo haga sin tener cuenta, reciba un folio y siga el avance por su cuenta.</p>
                <ul class="feat-list">
                    <li><span class="ico"><?= a_icon('form') ?></span><div><strong>Formularios públicos embebibles</strong><p>Se embebe con un iframe o se comparte como enlace, y lo configuras por completo: qué campos ve el visitante, a quién se le asigna, colores, PIN de acceso opcional y límites contra abuso.</p></div></li>
                    <li><span class="ico"><?= a_icon('search') ?></span><div><strong>Consulta por folio</strong><p>Quien reporta recibe un folio y ve su estado (recibido, en proceso o resuelto) sin acceder a tu operación.</p></div></li>
                    <li><span class="ico"><?= a_icon('sparkle') ?></span><div><strong>Clasificación automática por IA</strong><p>Si lo activas, lo que llega desde tu sitio se etiqueta solo, sin que nadie tenga que revisarlo primero.</p></div></li>
                </ul>
            </div>
            <div class="split__media split__media--tall">
                <?= sp_ventana('assets/img/formulario.png', 'Formulario público de SmartPend embebido en un sitio web', 1200, 1760, 'tu sitio web') ?>
                <p class="cap">Formulario público embebido en tu sitio</p>
            </div>
        </div>
    </div>
</section>

<!-- Capacidades ----------------------------------------------------------- -->
<section class="section" id="capacidades">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Capacidades</p>
            <h2>Del pendiente suelto al proceso ordenado</h2>
            <p class="lede">Del registro a la resolución, con formularios para tus clientes, reportes para quien decide y avisos para quien sigue de cerca.</p>
        </header>

        <div data-reveal>
            <div class="tabs" role="tablist" aria-label="Capacidades de SmartPend">
                <?php foreach ($CAPACIDADES as $i => $c): ?>
                <button class="tab" type="button" role="tab" id="tab-<?= a_e($c['id']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?> aria-controls="panel-<?= a_e($c['id']) ?>"><?= a_e($c['tab']) ?></button>
                <?php endforeach; ?>
            </div>
            <?php foreach ($CAPACIDADES as $i => $c): ?>
            <div class="cap-panel" role="tabpanel" id="panel-<?= a_e($c['id']) ?>" aria-labelledby="tab-<?= a_e($c['id']) ?>"<?= $i === 0 ? '' : ' hidden' ?>>
                <div class="split">
                    <div class="split__body">
                        <p class="eyebrow"><?= a_e($c['eyebrow']) ?></p>
                        <h3 style="font-size:clamp(1.5rem,1.4vw + 1rem,2rem)"><?= a_e($c['h3']) ?></h3>
                        <p class="muted"><?= a_e($c['p']) ?></p>
                        <ul class="feat-list">
                            <?php foreach ($c['items'] as $it): ?>
                            <li><span class="ico"><?= a_icon($it[0]) ?></span><div><strong><?= a_e($it[1]) ?></strong><p><?= a_e($it[2]) ?></p></div></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="split__media">
                        <?= sp_ventana($c['img'], $c['alt'], $c['w'], $c['h']) ?>
                        <p class="cap"><?= a_e($c['cap']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="group" data-reveal>
            <h3>Y todo lo que sostiene la operación</h3>
            <div class="items items--3"><?= sp_items($SOSTIENE) ?></div>
            <details class="more">
                <summary>Ver todas las capacidades avanzadas<?= a_icon('chevron') ?></summary>
                <h3 style="margin-top:22px;margin-bottom:0">Capacidades avanzadas</h3>
                <div class="items items--3"><?= sp_items($AVANZADAS) ?></div>
            </details>
        </div>
    </div>
</section>

<!-- Por qué SmartPend ----------------------------------------------------- -->
<section class="section section--sunken" id="resultados">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Por qué SmartPend</p>
            <h2>¿Por qué usar SmartPend?</h2>
            <p class="lede">Descubre cómo nuestro sistema transforma la gestión de tareas en tu organización.</p>
        </header>

        <div class="why" data-reveal>
            <div class="why__col">
                <p class="eyebrow">Maximiza la eficiencia de tu equipo</p>
                <h3>SmartPend elimina las barreras de comunicación</h3>
                <p class="muted">SmartPend está diseñado para eliminar las barreras de comunicación y optimizar los flujos de trabajo, permitiendo que tu equipo se concentre en lo que realmente importa: resolver los pendientes.</p>
                <ul class="checks">
                    <li><div><strong>Ahorra tiempo</strong><p>Dicta el pendiente por voz y la IA lo redacta y lo clasifica.</p></div></li>
                    <li><div><strong>Mejora la colaboración</strong><p>Facilita el trabajo en equipo entre departamentos.</p></div></li>
                    <li><div><strong>Incrementa productividad</strong><p>Optimiza recursos y elimina cuellos de botella.</p></div></li>
                    <li><div><strong>Garantiza trazabilidad</strong><p>Registro completo de cada acción y responsable.</p></div></li>
                    <li><div><strong>Prioriza lo urgente</strong><p>Ordenado por estado y fecha estimada: lo que vence antes aparece primero.</p></div></li>
                    <li><div><strong>Mejor decisión operativa</strong><p>Dashboard con métricas claras: sin asignar, totales reales y carga por empresa.</p></div></li>
                </ul>
            </div>
            <div class="why__col">
                <p class="eyebrow">Integración con tus procesos</p>
                <p class="muted">SmartPend se adapta a tu forma de trabajar, no al revés.</p>
                <div>
                    <details class="disc" open>
                        <summary class="disc__sum"><span class="disc__t">Personalización a la medida</span><?= a_icon('chevron', 'disc__chev') ?></summary>
                        <div class="disc__body"><p>Configuras tus empresas, servicios, etiquetas, roles y permisos. Si necesitas un módulo a la medida de tu operación, escríbenos.</p></div>
                    </details>
                    <details class="disc">
                        <summary class="disc__sum"><span class="disc__t">La misma información para todos</span><?= a_icon('chevron', 'disc__chev') ?></summary>
                        <div class="disc__body"><p>Todo el equipo trabaja sobre la misma información desde cualquier equipo, y la campana avisa de los cambios sin recargar la página.</p></div>
                    </details>
                    <details class="disc">
                        <summary class="disc__sum"><span class="disc__t">Asistencia inteligente</span><?= a_icon('chevron', 'disc__chev') ?></summary>
                        <div class="disc__body"><p>La IA trabaja donde ahorra tiempo: redacta el pendiente a partir de tu voz, sugiere el servicio y las etiquetas, y propone los pendientes de un proyecto.</p></div>
                    </details>
                    <details class="disc">
                        <summary class="disc__sum"><span class="disc__t">Gobernanza por grupos y reglas</span><?= a_icon('chevron', 'disc__chev') ?></summary>
                        <div class="disc__body"><p>Configura permisos de visibilidad entre departamentos una sola vez y aplícalos en pendientes, proyectos, búsquedas y accesos directos de forma automática.</p></div>
                    </details>
                </div>
                <div class="dif">
                    <p class="eyebrow">La diferencia SmartPend</p>
                    <p class="lede">No ofrecemos solo un software, ofrecemos una solución completa a tus necesidades de gestión. Con soporte directo del equipo que lo desarrolla y actualizaciones constantes, tu inversión evoluciona contigo.</p>
                </div>
            </div>
        </div>

        <div class="facts" data-reveal>
            <div class="fact"><b>Historial de cada movimiento</b><span>Con su autor y su fecha.</span></div>
            <div class="fact"><b>Sin instalar nada</b><span>Se entra desde el navegador.</span></div>
            <div class="fact"><b>Diez reportes</b><span>Con exportación a Excel, CSV y PDF.</span></div>
            <div class="fact"><b>Mensual sin plazo forzoso</b><span>Cancela cuando quieras.</span></div>
        </div>
    </div>
</section>

<!-- Precios --------------------------------------------------------------- -->
<section class="section" id="precios">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Precios</p>
            <h2>Un precio por usuario, sin planes</h2>
            <p class="lede">Pagas por cada usuario con cuenta activa, y el precio baja con cada usuario que sumas. Incluye soporte, mantenimiento y las actualizaciones que se publiquen.</p>
        </header>
        <div data-reveal><?= sp_tarifas_html(ALKES_CENTRAL . '/?producto=smartpend') ?></div>
        <ul class="plan-foot" data-reveal>
            <li><?= a_icon('check') ?>Soporte y mantenimiento incluidos</li>
            <li><?= a_icon('check') ?>Actualizaciones sin costo adicional</li>
            <li><?= a_icon('check') ?>Sin instalaciones ni servidores propios</li>
            <li><?= a_icon('check') ?>Precios en pesos mexicanos</li>
        </ul>
    </div>
</section>

<!-- Preguntas frecuentes -------------------------------------------------- -->
<section class="section section--sunken" id="preguntas">
    <div class="container">
        <div class="faq">
            <div class="faq__intro" data-reveal>
                <p class="eyebrow">Preguntas frecuentes</p>
                <h2>Lo que más nos preguntan sobre SmartPend</h2>
                <p class="muted">¿Tienes otra duda? Escríbenos a <a href="mailto:<?= ALKES_MAIL ?>"><?= ALKES_MAIL ?></a>.</p>
            </div>
            <div class="faq__list" data-reveal>
                <?php foreach (sp_faq() as $i => $f): ?>
                <details class="disc"<?= $i === 0 ? ' open' : '' ?>>
                    <summary class="disc__sum"><span class="disc__t"><?= a_e($f['q']) ?></span><?= a_icon('chevron', 'disc__chev') ?></summary>
                    <div class="disc__body"><p><?= a_e($f['a']) ?></p></div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Confianza ------------------------------------------------------------- -->
<section class="section" id="confianza">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Confianza</p>
            <h2>Un equipo con nombre, correo y teléfono</h2>
            <p class="lede">SmartPend es un producto de Alkes. Nuestro equipo desarrolla, opera y da soporte al mismo sistema.</p>
        </header>
        <div class="tiles" data-reveal>
            <div class="tile"><?= a_icon('cloud') ?><h3>Plataforma en la nube</h3><p>Sin instalaciones ni servidores propios. Entras por el navegador y empiezas a trabajar.</p></div>
            <div class="tile"><?= a_icon('shield') ?><h3>Infraestructura protegida</h3><p>Servidor con motor de seguridad anti-DDoS y acceso cifrado en todo el sistema.</p></div>
            <div class="tile"><?= a_icon('refresh') ?><h3>Actualizaciones incluidas</h3><p>Las nuevas funciones y correcciones llegan sin costo adicional durante tu periodo.</p></div>
            <div class="tile"><?= a_icon('lock') ?><h3>Tus datos, separados</h3><p>Permisos por grupos y visibilidad controlada entre áreas, empresas y servicios.</p></div>
        </div>
        <div class="team" data-reveal>
            <div>
                <h3>Soporte del equipo que construye SmartPend</h3>
                <p>Nuestro equipo desarrolla, opera y da soporte a SmartPend de principio a fin. Para soporte, una demostración guiada o un módulo a la medida de tu operación, hablas directamente con el equipo que construye el sistema.</p>
            </div>
            <div class="team__links">
                <a class="btn btn--primary" href="<?= a_e(sp_url_agendar()) ?>"><?= a_icon('mail') ?>Escribir al equipo</a>
                <a class="btn btn--line" href="tel:<?= ALKES_TEL ?>"><?= a_icon('phone') ?><?= ALKES_TEL_TXT ?></a>
                <a class="btn btn--line" href="<?= SP_WHATSAPP ?>" target="_blank" rel="noopener">WhatsApp<?= a_icon('external') ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Novedades ------------------------------------------------------------- -->
<section class="section section--sunken" id="novedades">
    <div class="container narrow">
        <header class="head" data-reveal>
            <p class="eyebrow">Novedades</p>
            <h2>El producto se mueve, y queda por escrito</h2>
            <p class="lede">Historial público de versiones: lo que se entregó, cuándo y qué cambia para quien lo usa.</p>
        </header>
        <div data-reveal>
            <?php include __DIR__ . '/_novedades_recientes.php'; ?>
            <details class="hist">
                <summary>Ver historial completo, de v6.0.0 a v1.0.0<?= a_icon('chevron') ?></summary>
                <div style="margin-top:10px"><?php include __DIR__ . '/_novedades_historial.php'; ?></div>
            </details>
        </div>

        <div class="block">
            <header class="head head--sub" data-reveal>
                <p class="eyebrow">En camino</p>
                <h2 style="font-size:clamp(1.4rem,1vw + 1.1rem,1.8rem)">Lo que sigue</h2>
            </header>
            <div class="roadmap" data-reveal>
                <div class="roadmap__item"><h3>Asignación por grupo</h3><p>Envía un pendiente a un equipo completo, no solo a una persona.</p></div>
                <div class="roadmap__item"><h3>Flujo de aprobación</h3><p>Pasos de validación antes de dar por cerrado un pendiente.</p></div>
                <div class="roadmap__item"><h3>Sugerencia de asignado por IA</h3><p>El sistema propone quién debería atenderlo según el historial.</p></div>
                <div class="roadmap__item"><h3>Sugerencia por departamento (IA)</h3><p>Identifica automáticamente el departamento o rol más adecuado para resolver cada pendiente, reduciendo tiempos de derivación.</p></div>
                <div class="roadmap__item"><h3>Resúmenes automáticos (IA)</h3><p>Botón "Resumir" que genera un resumen breve del caso a partir de notas e historial, junto con próximos pasos sugeridos para supervisores.</p></div>
                <div class="roadmap__item"><h3>Mensajes al cliente/usuario por IA</h3><p>Generación automática del cuerpo del correo o mensaje al cliente/usuario al resolver un pendiente, usando el contexto del caso. Siempre editable antes de enviar.</p></div>
                <div class="roadmap__item"><h3>Bot de Telegram</h3><p>Crea y consulta pendientes desde la mensajería que ya usas.</p></div>
            </div>
            <div class="notice" data-reveal><?= a_icon('alert') ?><p>Las fechas, el orden y la disponibilidad de esta funcionalidad están sujetos a cambios sin previo aviso.</p></div>
        </div>
    </div>
</section>

<!-- Tecnología ------------------------------------------------------------ -->
<section class="section section--tight" id="tecnologia">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Tecnología</p>
            <h2>Construido con tecnología de punta</h2>
            <p class="lede">Utilizamos un stack tecnológico moderno y robusto para garantizar el rendimiento, la seguridad y la escalabilidad.</p>
        </header>
        <div class="tech" data-reveal>
            <div><?= a_icon('server') ?><h3>Backend</h3><p>PHP con Medoo ORM y Jaxon para un backend rápido y eficiente.</p></div>
            <div><?= a_icon('database') ?><h3>Base de datos</h3><p>MariaDB, garantizando integridad y velocidad en el manejo de datos.</p></div>
            <div><?= a_icon('image') ?><h3>Frontend</h3><p>Bootstrap, DataTables, FullCalendar y SweetAlert2 para una interfaz moderna y responsiva.</p></div>
            <div><?= a_icon('ethernet') ?><h3>Servidor</h3><p>Nginx con un potente motor de seguridad anti DDoS.</p></div>
        </div>
    </div>
</section>

<!-- Cierre ---------------------------------------------------------------- -->
<section class="section section--tight">
    <div class="container">
        <div class="cta" data-reveal>
            <div>
                <p class="eyebrow">Empieza hoy</p>
                <h2>Que tu equipo deje de perseguir pendientes</h2>
                <p class="lede">Entra al demo con las credenciales de prueba, o contrata por usuario y empieza a operar el mismo día.</p>
            </div>
            <div class="cta__act">
                <a class="btn btn--cta btn--lg" href="<?= a_e($URL_APP) ?>" data-open-demo><?= a_icon('play') ?>Probar el demo</a>
                <a class="btn btn--line btn--lg" href="#precios">Ver precios</a>
                <a class="link" href="<?= a_e(sp_url_agendar()) ?>">Agendar demostración guiada<?= a_icon('arrow') ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Contacto -------------------------------------------------------------- -->
<section class="section section--sunken" id="contacto">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Contacto</p>
            <h2>Hablemos</h2>
            <p class="lede">Soporte técnico, dudas comerciales o módulos a la medida. Tu mensaje llega a una bandeja real.</p>
        </header>
        <div class="contact" data-reveal>
            <a class="contact__item" href="mailto:<?= ALKES_MAIL ?>"><?= a_icon('mail') ?><span class="contact__k">Correo</span><span class="contact__v"><?= ALKES_MAIL ?></span><span class="contact__d">Respuesta en horario hábil.</span></a>
            <a class="contact__item" href="tel:<?= ALKES_TEL ?>"><?= a_icon('phone') ?><span class="contact__k">Teléfono</span><span class="contact__v"><?= ALKES_TEL_TXT ?></span><span class="contact__d">Para demostraciones guiadas.</span></a>
            <a class="contact__item" href="<?= SP_WHATSAPP ?>" target="_blank" rel="noopener"><?= a_icon('phone') ?><span class="contact__k">WhatsApp</span><span class="contact__v"><?= ALKES_TEL_TXT ?></span><span class="contact__d">Escríbenos por mensaje.</span></a>
            <a class="contact__item" href="<?= a_e($URL_APP) ?>"><?= a_icon('login') ?><span class="contact__k">Acceso</span><span class="contact__v">app.smartpend.alkes.mx</span><span class="contact__d">Entra a tu cuenta de SmartPend.</span></a>
        </div>
        <div class="redes" data-reveal>
            <span class="redes__k">Síguenos</span>
            <?php foreach (sp_redes() as $r): ?>
            <a href="<?= a_e($r['u']) ?>" target="_blank" rel="noopener"><?= a_e($r['n']) ?><?= a_icon('external') ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ecosistema ------------------------------------------------------------ -->
<section class="section section--tight" id="ecosistema">
    <div class="container">
        <header class="head" data-reveal>
            <p class="eyebrow">Ecosistema Alkes</p>
            <h2>SmartPend no está solo</h2>
            <p class="lede">Cinco productos desarrollados, alojados y operados por Alkes. Mismo soporte, misma casa.</p>
        </header>
        <?= a_ecosistema_grid($PROD) ?>
    </div>
</section>

</main>

<footer class="footer">
    <div class="container footer__top">
        <div class="footer__brand">
            <span class="brand"><?= sp_logo(34) ?><span>SmartPend</span></span>
            <p>Sistema de gestión de pendientes y proyectos. Centraliza la operación, ordena las prioridades y conserva la trazabilidad de cada solicitud.</p>
        </div>
        <div class="footer__cols">
            <div class="footer__col">
                <h2 class="footer__h">Producto</h2>
                <ul>
                    <li><a href="#producto">Qué es</a></li>
                    <li><a href="#capacidades">Capacidades</a></li>
                    <li><a href="#precios">Precios</a></li>
                    <li><a href="#preguntas">Preguntas frecuentes</a></li>
                    <li><a href="#novedades">Novedades</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h2 class="footer__h">Accesos</h2>
                <ul>
                    <li><a href="<?= a_e($URL_APP) ?>">Iniciar sesión</a></li>
                    <li><a href="<?= a_e($URL_APP) ?>" data-open-demo>Probar el demo</a></li>
                    <li><a href="<?= ALKES_CENTRAL ?>">Contratar</a></li>
                    <li><a href="<?= ALKES_SITIO ?>">Alkes</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h2 class="footer__h">Legal</h2>
                <ul>
                    <li><a href="/terminos/">Términos y condiciones</a></li>
                    <li><a href="/privacidad/">Aviso de privacidad</a></li>
                    <li><a href="/cookies/">Cookies</a></li>
                    <li><a href="/encargo-de-datos/">Tratamiento de datos</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h2 class="footer__h">Síguenos</h2>
                <ul>
                    <?php foreach (sp_redes() as $r): ?>
                    <li><a href="<?= a_e($r['u']) ?>" target="_blank" rel="noopener"><?= a_e($r['n']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="footer__bot">
            <p>© <?= date('Y') ?> Alkes. SmartPend es un producto de Alkes. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<?= sp_dialogo_demo($URL_APP) ?>

<script src="assets/js/smartpend.js" defer></script>
</body>
</html>
