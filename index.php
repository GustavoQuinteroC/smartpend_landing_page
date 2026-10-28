<?php
$NOINDEX = false;

// Contacto institucional. Se define antes de cargar el kit: el kit solo
// define estas constantes si no existen.
define('ALKES_MAIL',    'soporte@alkes.mx');
define('ALKES_TEL',     '+523339460097');
define('ALKES_TEL_TXT', '333 946 0097');


if (!defined('SP_SITIO'))     { define('SP_SITIO', 'https://smartpend.alkes.mx'); }
if (!defined('SP_WHATSAPP'))  { define('SP_WHATSAPP', 'https://wa.me/523339460097'); }

/** Redes oficiales de Alkes. */
function sp_redes()
{
    return array(
        array('n' => 'Facebook',  'u' => 'https://www.facebook.com/alkes.mx'),
        array('n' => 'Instagram', 'u' => 'https://www.instagram.com/alkes.mx'),
        array('n' => 'LinkedIn',  'u' => 'https://www.linkedin.com/company/alkes-mx'),
        array('n' => 'TikTok',    'u' => 'https://www.tiktok.com/@alkes.mx'),
    );
}

/** Mensaje de partida para pedir una demostración guiada. */
function sp_url_agendar()
{
    return 'mailto:' . ALKES_MAIL . '?subject=' . rawurlencode('Demostración guiada de SmartPend');
}

/** Logo en dos versiones; el CSS muestra la que corresponde al tema. */
function sp_logo($px = 34)
{
    $px = (int) $px;
    return '<img class="logo logo--claro" src="assets/img/logo.png" alt="" width="' . $px . '" height="' . $px . '">'
         . '<img class="logo logo--oscuro" src="assets/img/logo-blanco.png" alt="" width="' . $px . '" height="' . $px . '">';
}

/**
 * Captura dentro de una ventana de navegador. Si existe la versión oscura se
 * pintan las dos y el CSS muestra la del tema activo.
 */
function sp_ventana($archivo, $alt, $w, $h, $url = 'app.smartpend.alkes.mx', $carga = 'lazy')
{
    $existe = is_file(__DIR__ . '/' . $archivo);
    $osc    = preg_replace('/\.(png|jpe?g|webp)$/i', '-oscuro.$1', $archivo);
    $hayOsc = $osc !== $archivo && is_file(__DIR__ . '/' . $osc);
    $o  = '<figure class="win">';
    $o .= '<div class="win__bar"><span class="win__dots" aria-hidden="true"><i></i><i></i><i></i></span>';
    $o .= '<span class="win__url">' . a_e($url) . '</span></div>';
    $o .= '<div class="win__shot" style="--ar:' . (int) $w . ' / ' . (int) $h . '">';
    if ($existe) {
        $o .= '<img src="' . a_e($archivo) . '" alt="' . a_e($alt) . '"' . ($hayOsc ? ' data-tema-img="claro"' : '')
            . ' width="' . (int) $w . '" height="' . (int) $h . '" loading="' . a_e($carga) . '" decoding="async">';
        if ($hayOsc) {
            $o .= '<img src="' . a_e($osc) . '" alt="' . a_e($alt) . '" data-tema-img="oscuro"'
                . ' width="' . (int) $w . '" height="' . (int) $h . '" loading="' . a_e($carga) . '" decoding="async">';
        }
    } else {
        $o .= '<div class="win__falta"><b>Captura pendiente</b><code>' . a_e($archivo) . '</code></div>';
    }
    return $o . '</div></figure>';
}

/** Tarifa por usuario (tarifa.json): el descuento sube 1% por cada usuario adicional hasta el tope, y aplica al precio de todos. */
function sp_tarifa()
{
    static $t = null;
    if ($t !== null) { return $t; }
    $t = array('anual_meses' => 10, 'base' => 100, 'paso' => 0.01, 'tope_descuento' => 0.5);
    $f = __DIR__ . '/tarifa.json';
    if (is_file($f)) {
        $j = json_decode((string) @file_get_contents($f), true);
        if (is_array($j) && !empty($j['base'])) { $t = $j + $t; }
    }
    return $t;
}

/** Mensualidad para $n usuarios: descuento de (n-1) × paso, con tope; el precio por usuario va a centavos. */
function sp_calcular($n)
{
    $n = max(1, (int) $n);
    $t = sp_tarifa();
    $d = min((float) $t['tope_descuento'], ($n - 1) * (float) $t['paso']);
    $precio = round((float) $t['base'] * (1 - $d), 2);
    return array('total' => round($n * $precio, 2), 'precio' => $precio, 'n' => $n, 'descuento' => $d);
}

/** Precio por usuario más alto (calculadora y checkout; la publicidad no lo cita). */
function sp_tarifa_max()
{
    return (float) sp_tarifa()['base'];
}

/** Precio por usuario más bajo (el que se anuncia). */
function sp_tarifa_min()
{
    $t = sp_tarifa();
    return round((float) $t['base'] * (1 - (float) $t['tope_descuento']), 2);
}

/** Usuario desde el que aplica el descuento máximo. */
function sp_tarifa_tope_usuarios()
{
    $t = sp_tarifa();
    return (int) ceil((float) $t['tope_descuento'] / (float) $t['paso']) + 1;
}

/** Sección de precios: explicación y calculadora. Sin JS se ve la explicación. */
function sp_tarifas_html($url_base)
{
    $t = sp_tarifa();
    $meses = (int) $t['anual_meses'];
    $pct = rtrim(rtrim(number_format((float) $t['paso'] * 100, 2, '.', ''), '0'), '.');
    $top = (int) round((float) $t['tope_descuento'] * 100);
    $h  = '<div class="tarifa" data-tarifa="' . a_e(json_encode($t, JSON_UNESCAPED_UNICODE)) . '" data-url="' . a_e($url_base) . '">';

    $h .= '<div class="tarifa__card"><p class="eyebrow">Precio por usuario</p>';
    $h .= '<p class="tarifa__desde"><small>Desde</small><b>$' . a_num(sp_tarifa_min()) . '</b><small>MXN por usuario al mes</small></p>';
    $h .= '<h3>Más usuarios, menos pagas por cada uno</h3>';
    $h .= '<ul class="tarifa__reglas">';
    $h .= '<li>' . a_icon('check') . 'Cada usuario adicional baja ' . $pct . '% el precio de todos tus usuarios.</li>';
    $h .= '<li>' . a_icon('check') . 'Arriba de ' . (sp_tarifa_tope_usuarios() - 1) . ' usuarios, obtienes el descuento máximo de ' . $top . '%.</li>';
    $h .= '<li>' . a_icon('check') . 'Sin planes ni paquetes: todas las funciones para todos tus usuarios.</li>';
    $h .= '<li>' . a_icon('check') . 'Al sumar un usuario se cobra al momento la parte proporcional del periodo. Al reducir tus usuarios contratados, el cobro menor rige desde tu siguiente renovación.</li>';
    $h .= '<li>' . a_icon('check') . 'Con el plan anual solo pagas ' . $meses . ' meses, y los otros ' . (12 - $meses) . ' son gratis.</li>';
    $h .= '</ul></div>';

    $h .= '<div class="tarifa__card tarifa__calc" hidden data-calc>';
    $h .= '<p class="eyebrow">Calcula tu mensualidad</p>';
    $h .= '<label class="tarifa__lbl" for="tarifa-n">¿Cuántos usuarios tendrás?</label>';
    $h .= '<input class="tarifa__in" id="tarifa-n" type="number" inputmode="numeric" min="1" max="100000" value="1">';
    $h .= '<div class="period period--sm" role="group" aria-label="Periodo de facturación">';
    $h .= '<button type="button" data-per="mensual" aria-pressed="true">Mensual</button>';
    $h .= '<button type="button" data-per="anual" aria-pressed="false">Anual<span class="save">−17%</span></button></div>';
    $h .= '<ul class="tarifa__desg" data-desg></ul>';
    $h .= '<p class="tarifa__tot"><span data-tot>$0</span><small data-per-txt>MXN al mes</small></p>';
    $h .= '<p class="tarifa__prom muted" data-prom></p>';
    $h .= '<a class="btn btn--primary btn--block" data-cta href="' . a_e($url_base) . '">Contratar' . a_icon('arrow') . '</a>';
    $h .= '</div>';
    $h .= '</div>';
    return $h;
}

/**
 * Rejilla de planes. Misma lógica de contratación que la del kit; cambia el
 * marcado y se quitan las frases que hablan del tamaño de la organización.
 */
function sp_planes($periodo)
{
    $items = array();
    foreach (a_planes() as $p) {
        if ($p['periodo'] === $periodo || $p['periodo'] === 'unico') { $items[] = $p; }
    }
    if (!$items) {
        return '<p class="muted">Por el momento no hay planes publicados. Escríbenos a '
             . '<a href="mailto:' . ALKES_MAIL . '">' . ALKES_MAIL . '</a>.</p>';
    }

    $clave  = a_producto_clave();
    $ahorro = a_ahorro_anual();
    $h = '<div class="plans">';

    foreach ($items as $p) {
        $rec = !empty($p['recomendado']);
        $per = $p['periodo'] === 'anual' ? 'MXN / año'
             : ($p['periodo'] === 'unico' ? 'MXN / pago único' : 'MXN / mes');

        $h .= '<article class="plan' . ($rec ? ' plan--rec' : '') . '">';
        $h .= '<div class="plan__head"><h3 class="plan__name">' . a_e($p['nombre']) . '</h3>';
        if ($rec) { $h .= '<span class="pill pill--azul">Recomendado</span>'; }
        $h .= '</div>';
        $h .= '<p class="plan__price"><span class="plan__cur">$</span><span class="plan__amt">' . a_num($p['precio'])
            . '</span><span class="plan__per">' . $per . '</span></p>';

        $nota = '';
        if (!empty($p['precio_anterior'])) {
            $nota = 'Antes <s>$' . a_num($p['precio_anterior']) . '</s>';
        } elseif ($p['periodo'] === 'anual') {
            $nota = 'Equivale a $' . a_num(round($p['precio'] / 12)) . ' al mes' . ($ahorro ? '. Ahorras ' . $ahorro . '%.' : '');
        } elseif ($p['periodo'] === 'mensual') {
            $nota = 'Sin plazo forzoso. Cancela cuando quieras.';
        }
        $h .= '<p class="plan__note">' . $nota . '</p>';

        $h .= '<ul class="checks plan__features">';
        foreach ($p['caracteristicas'] as $c) {
            $titulo = $c['titulo'];
            $desc   = !empty($c['descripcion']) ? $c['descripcion'] : '';
            if (stripos($titulo, 'usuarios') !== false) { $desc = ''; }          // La descripción habla de tamaño.
            if ($titulo === 'Tokens de ChatGPT')        { $titulo = 'Dictado y asistente de IA'; }
            $h .= '<li><div><strong>' . a_e($titulo) . '</strong>' . ($desc !== '' ? '<p>' . a_e($desc) . '</p>' : '') . '</div></li>';
        }
        $h .= '</ul>';

        $url = ALKES_CENTRAL . '/?producto=' . urlencode($clave) . '&plan=' . urlencode($p['clave']) . '&periodo=' . urlencode($p['periodo']);
        $h .= '<a class="btn ' . ($rec ? 'btn--primary' : 'btn--line') . ' btn--block" href="' . a_e($url) . '">'
            . 'Contratar ' . a_e($p['nombre']) . a_icon('arrow') . '</a>';
        $h .= '</article>';
    }
    return $h . '</div>';
}

/** Diálogo con las credenciales del demo. */
function sp_dialogo_demo($url_app)
{
    $h  = '<dialog id="demoDialog" aria-labelledby="demoTitulo"><div class="dlg">';
    $h .= '<h2 id="demoTitulo">Acceso al demo</h2>';
    $h .= '<p>Entra a la empresa de demostración con estas credenciales. No captures información real.</p>';
    $h .= '<dl class="dlg__creds"><div><dt>Usuario</dt><dd>demo@alkes.mx</dd></div>';
    $h .= '<div><dt>Contraseña</dt><dd>Demo@Alkes2026</dd></div></dl>';
    $h .= '<p>Para una demostración guiada escríbenos a <a href="' . a_e(sp_url_agendar()) . '">' . ALKES_MAIL . '</a>, '
        . 'llama al <a href="tel:' . ALKES_TEL . '">' . ALKES_TEL_TXT . '</a> o usa '
        . '<a href="' . SP_WHATSAPP . '" target="_blank" rel="noopener">WhatsApp</a>.</p>';
    $h .= '<div class="dlg__foot"><a class="btn btn--primary" href="' . a_e($url_app) . '" target="_blank" rel="noopener">Ir a la app' . a_icon('external') . '</a>';
    $h .= '<button class="btn btn--line" type="button" data-close-demo>Cerrar</button></div>';
    return $h . '</div></dialog>';
}

/** Preguntas frecuentes: la misma lista alimenta la sección y el JSON-LD. */
function sp_faq()
{
    $ahorro = a_ahorro_anual();
    return array(
        array('q' => '¿Qué es SmartPend?',
              'a' => 'Un sistema de Alkes que concentra los pendientes: quién los pidió, quién responde, en qué van y su historial completo.'),
        array('q' => '¿Cuánto cuesta?',
              'a' => sp_faq_precio()),
        array('q' => '¿Cómo se calcula mi mensualidad?',
              'a' => sp_faq_calculo()),
        array('q' => '¿Qué pasa si agrego o quito usuarios a mitad del periodo?',
              'a' => sp_faq_altas()),
        array('q' => '¿Hay planes o límites de funciones?',
              'a' => 'No. El precio depende solo del número de usuarios con cuenta activa; todos tienen las mismas funciones.'),
        array('q' => '¿Mis clientes pueden reportar sin cuenta?',
              'a' => 'Sí. Un formulario público recibe el reporte, entrega un folio y permite consultar el estado.'),
        array('q' => '¿Qué hace la IA?',
              'a' => 'Convierte tu dictado en un pendiente claro, sugiere el servicio y las etiquetas, y propone los pendientes de un proyecto.'),
        array('q' => '¿Hay que instalar algo?',
              'a' => 'No. Es una plataforma en la nube y se entra por el navegador.'),
        array('q' => '¿Puedo probarlo?',
              'a' => 'Sí, con el demo y sus credenciales de prueba.'),
        array('q' => '¿Qué reportes tiene?',
              'a' => 'Diez reportes con comparación contra el periodo anterior, exportables a Excel, CSV o PDF.'),
    );
}

/** Datos estructurados. Todo sale de lo que la página dice; sin reseñas ni calificaciones. */
function sp_jsonld($seo = array())
{
    $ofertas = array(
        '@type' => 'AggregateOffer',
        'priceCurrency' => 'MXN',
        'lowPrice' => (string) sp_tarifa_min(),
        'highPrice' => (string) sp_tarifa_max(),
        'url' => SP_SITIO . '/#precios',
    );
    $faq = array();
    foreach (sp_faq() as $f) {
        $faq[] = array('@type' => 'Question', 'name' => $f['q'],
                       'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f['a']));
    }
    $redes = array();
    foreach (sp_redes() as $r) { $redes[] = $r['u']; }

    $g = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array('@type' => 'Organization', '@id' => ALKES_SITIO . '/#org', 'name' => 'Alkes', 'url' => ALKES_SITIO,
                  'email' => ALKES_MAIL, 'telephone' => ALKES_TEL, 'sameAs' => $redes),
            array('@type' => 'WebSite', '@id' => SP_SITIO . '/#web', 'name' => 'SmartPend', 'url' => SP_SITIO . '/',
                  'inLanguage' => 'es-MX', 'publisher' => array('@id' => ALKES_SITIO . '/#org')),
            array('@type' => 'SoftwareApplication', '@id' => SP_SITIO . '/#app', 'name' => 'SmartPend',
                  'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web',
                  'description' => 'Sistema en la nube para dar seguimiento a pendientes y proyectos con folio, historial, formularios públicos e IA.',
                  'url' => SP_SITIO . '/', 'inLanguage' => 'es-MX', 'image' => SP_SITIO . '/assets/img/og-image.png',
                  'publisher' => array('@id' => ALKES_SITIO . '/#org'), 'offers' => $ofertas),
            array('@type' => 'FAQPage', '@id' => SP_SITIO . '/#faq', 'mainEntity' => $faq),
        ),
    );
    $g = seo_enriquecer($g, $seo);
    return json_encode($g, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
}

/** Texto de precio para las preguntas frecuentes. */
function sp_faq_precio()
{
    $t = sp_tarifa();
    $pct = rtrim(rtrim(number_format((float) $t['paso'] * 100, 2, '.', ''), '0'), '.');
    return 'Desde $' . a_num(sp_tarifa_min()) . ' MXN por usuario al mes. Se cobra por usuario con cuenta activa, y cada usuario adicional baja ' . $pct . '% el precio de todos hasta llegar a ' . (int) round((float) $t['tope_descuento'] * 100) . '% de descuento arriba de ' . (sp_tarifa_tope_usuarios() - 1) . ' usuarios. Con el plan anual solo pagas ' . (int) $t['anual_meses'] . ' meses, y los otros ' . (12 - (int) $t['anual_meses']) . ' son gratis.';
}

function sp_faq_calculo()
{
    $c15 = sp_calcular(15); $c50 = sp_calcular(50);
    return 'Todos tus usuarios pagan el mismo precio, que baja con cada usuario que sumas. Con 15 usuarios son 15 × $' . a_num($c15['precio']) . ' = $' . a_num($c15['total']) . ' MXN al mes; con 50 usuarios son 50 × $' . a_num($c50['precio']) . ' = $' . a_num($c50['total']) . ' MXN al mes. La calculadora de la sección de precios te da el total exacto.';
}

function sp_faq_altas()
{
    return 'Al sumar un usuario se cobra al momento la parte proporcional hasta tu próxima factura, y desde la siguiente el precio por usuario se ajusta a tu nuevo total. Desactivar un usuario libera su lugar para otra persona, pero no cambia el cobro: para pagar menos, reduce tus usuarios contratados en Configuración y la reducción rige desde la siguiente renovación; no hay reembolso a mitad del periodo.';
}

if (!defined('ALKES_SITIO'))    { define('ALKES_SITIO', 'https://alkes.mx'); }
if (!defined('ALKES_CENTRAL'))  { define('ALKES_CENTRAL', 'https://contratar.alkes.mx'); }
if (!defined('ALKES_MAIL'))     { define('ALKES_MAIL', 'gquintero@alkes.mx'); }
if (!defined('ALKES_TEL'))      { define('ALKES_TEL', '+523339460097'); }
if (!defined('ALKES_TEL_TXT'))  { define('ALKES_TEL_TXT', '333 946 0097'); }

/** Escape de todo dato que entre al HTML. */
function a_e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

/**
 * La familia completa. Alimenta la rejilla de ecosistema del pie.
 * Orden fijo en las seis landings: la firma tiene que ser reconocible.
 */
function a_familia()
{
    return array(
        'smartpend' => array('nombre' => 'SmartPend', 'url' => 'https://smartpend.alkes.mx', 'que' => 'Pendientes y proyectos'),
        'geocheck'  => array('nombre' => 'GeoCheck',  'url' => 'https://geocheck.alkes.mx',  'que' => 'Asistencia por ubicación'),
        'mantis'    => array('nombre' => 'Mantis',    'url' => 'https://mantis.alkes.mx',    'que' => 'Mantenimiento de activos'),
        'syspower'  => array('nombre' => 'SysPower',  'url' => 'https://syspower.alkes.mx',  'que' => 'Energía controlada a distancia'),
        'erp'       => array('nombre' => 'Alkes ERP', 'url' => 'https://erp.alkes.mx',       'que' => 'Gestión empresarial y CFDI'),
    );
}

// ---------------------------------------------------------------------------
// Iconos. Trazo 1.75, viewBox 24×24, currentColor. Decorativos siempre.
// ---------------------------------------------------------------------------
function a_icon($nombre, $clase = '')
{
    static $d = null;
    if ($d === null) { $d = array(
    'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'check'      => '<path d="M20 6 9 17l-5-5"/>',
    'close'      => '<path d="M18 6 6 18M6 6l12 12"/>',
    'chevron'    => '<path d="m6 9 6 6 6-6"/>',
    'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'moon'       => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>',
    'sun'        => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
    'external'   => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
    'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 7.6 5.3a1.6 1.6 0 0 0 1.8 0L20.5 7"/>',
    'phone'      => '<path d="M7 3h3l2 5-2.5 1.5a12 12 0 0 0 5 5L16 12l5 2v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4 5.2 2 2 0 0 1 6 3Z"/>',
    'globe'      => '<circle cx="12" cy="12" r="9"/><path d="M3.5 9h17M3.5 15h17M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
    'shield'     => '<path d="M12 3 5 6v5.5c0 4.2 2.9 7.6 7 9.5 4.1-1.9 7-5.3 7-9.5V6Z"/><path d="m9 12 2 2 4-4"/>',
    'play'       => '<circle cx="12" cy="12" r="9"/><path d="M10.5 9 15 12l-4.5 3Z"/>',
    'eye'        => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
    'lock'       => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
    'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5.3l3.3 2"/>',
    'history'    => '<path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1M3.5 5v4h4"/><path d="M12 7.5v5l3.2 1.9"/>',
    'users'      => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5"/><path d="M16 5.3a3.2 3.2 0 0 1 0 6.2M17.5 14.8c2.1.6 3.5 2.3 3.5 4.6"/>',
    'user-check' => '<circle cx="10" cy="8" r="3.2"/><path d="M3.5 20c0-3.3 2.9-5.5 6.5-5.5 1 0 2 .2 2.8.5"/><path d="m15.5 17.5 2 2 4-4"/>',
    'mic'        => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5.5 11.5a6.5 6.5 0 0 0 13 0M12 18v3"/>',
    'tag'        => '<path d="M3.5 11.2V4.5a1 1 0 0 1 1-1h6.7a1 1 0 0 1 .7.3l8.3 8.3a1 1 0 0 1 0 1.4l-6.7 6.7a1 1 0 0 1-1.4 0L3.8 11.9a1 1 0 0 1-.3-.7Z"/><circle cx="7.8" cy="7.8" r="1.3"/>',
    'form'       => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4"/>',
    'report'     => '<path d="M6 3h8l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5"/><path d="M9 17v-3M12 17v-5M15 17v-2"/>',
    'calendar'   => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
    'repeat'     => '<path d="M4 9V8a3 3 0 0 1 3-3h10l-3-3M20 15v1a3 3 0 0 1-3 3H7l3 3"/>',
    'layers'     => '<path d="m12 3 9 5-9 5-9-5Z"/><path d="m3.5 12.5 8.5 4.7 8.5-4.7"/><path d="m3.5 16.8 8.5 4.7 8.5-4.7"/>',
    'filter'     => '<path d="M3.5 5h17l-6.6 7.8V20l-3.8-2.2v-5Z"/>',
    'building'   => '<path d="M4 21V5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v16"/><path d="M15 10h3a2 2 0 0 1 2 2v9M8 7h3M8 11h3M8 15h3M2.5 21h19"/>',
    'bell'       => '<path d="M6 9a6 6 0 1 1 12 0c0 4 1.5 5.5 2 6H4c.5-.5 2-2 2-6Z"/><path d="M10 19a2 2 0 0 0 4 0"/>',
    'cpu'        => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/><path d="M9 3v3M15 3v3M9 18v3M15 18v3M3 9h3M3 15h3M18 9h3M18 15h3"/>',
    'sparkle'    => '<path d="m12 3 1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9Z"/><path d="M18.5 16.5 19 18l1.5.5-1.5.5-.5 1.5-.5-1.5L16.5 18l1.5-.5Z"/>',
    'cloud'      => '<path d="M7 18h10.5a3.5 3.5 0 0 0 .4-7A5.5 5.5 0 0 0 7.3 9.6 4.2 4.2 0 0 0 7 18Z"/>',
    'cloud-check'=> '<path d="M7 17.5h10a3.5 3.5 0 0 0 .4-7A5.5 5.5 0 0 0 7.3 9.1 4.2 4.2 0 0 0 7 17.5Z"/><path d="m9.5 13.5 2 2 3.5-3.5"/>',
    'refresh'    => '<path d="M20 11a8 8 0 0 0-13.8-5.2L3.5 8.5"/><path d="M3.5 4v4.5H8"/><path d="M4 13a8 8 0 0 0 13.8 5.2l2.7-2.7"/><path d="M20.5 20v-4.5H16"/>',
    'checklist'  => '<path d="M9 6h11M9 12h11M9 18h11"/><path d="m3.5 6 1.2 1.2L7 5M3.5 12l1.2 1.2L7 11M3.5 18l1.2 1.2L7 17"/>',
    'geo'        => '<path d="M12 21s7-5.7 7-11a7 7 0 1 0-14 0c0 5.3 7 11 7 11Z"/><circle cx="12" cy="10" r="2.7"/>',
    'target'     => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="1"/>',
    'badge'      => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10.5" r="2.2"/><path d="M5.5 16.5c.5-1.7 1.9-2.6 3.5-2.6s3 .9 3.5 2.6M15 9.5h3.5M15 13h3.5"/>',
    'camera'     => '<path d="M4 7h3l1.5-2h7L17 7h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.6"/>',
    'qr'         => '<rect x="3.5" y="3.5" width="7" height="7" rx="1"/><rect x="13.5" y="3.5" width="7" height="7" rx="1"/><rect x="3.5" y="13.5" width="7" height="7" rx="1"/><path d="M13.5 13.5h3v3h-3zM20.5 13.5v3M17.5 20.5h3M13.5 20.5h1"/>',
    'route'      => '<circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="6" r="2.5"/><path d="M15.5 6H10a4 4 0 0 0 0 8h4a4 4 0 0 1 0 8H8.5"/>',
    'truck'      => '<path d="M3 6h10v10H3zM13 9.5h4l3 3.2V16h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
    'warehouse'  => '<path d="M3 21V8.5L12 4l9 4.5V21"/><path d="M7.5 21v-7h9v7M7.5 17.5h9"/>',
    'factory'    => '<path d="M3 21V10l6 4V10l6 4V6h6v15Z"/><path d="M7 17.5h2M13 17.5h2M18 17.5h1"/>',
    'cart'       => '<path d="M3 4h2.2l2.4 11.2a1.5 1.5 0 0 0 1.5 1.2h8.3a1.5 1.5 0 0 0 1.5-1.2L21 8H6"/><circle cx="9.5" cy="20" r="1.4"/><circle cx="17.5" cy="20" r="1.4"/>',
    'invoice'    => '<path d="M5.5 3h13v18l-2.2-1.5-2.1 1.5-2.2-1.5L9.8 21l-2.1-1.5L5.5 21Z"/><path d="M9 8h6M9 12h6"/>',
    'coins'      => '<ellipse cx="9" cy="7" rx="5.5" ry="2.5"/><path d="M3.5 7v5c0 1.4 2.5 2.5 5.5 2.5s5.5-1.1 5.5-2.5V7"/><path d="M14.5 11.4c3 .2 5.2 1.2 5.2 2.4v4.7c0 1.4-2.5 2.5-5.5 2.5s-5.5-1.1-5.5-2.5v-3.6"/>',
    'server'     => '<rect x="3.5" y="4" width="17" height="6.5" rx="1.5"/><rect x="3.5" y="13.5" width="17" height="6.5" rx="1.5"/><path d="M7 7.2h.01M7 16.8h.01"/>',
    'rack'       => '<rect x="4.5" y="3" width="15" height="18" rx="2"/><path d="M4.5 8.5h15M4.5 14h15M8 5.8h.01M8 11.2h.01M8 17.3h.01"/>',
    'ethernet'   => '<path d="M6 10V6.5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1V10l2 2v5.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V12Z"/><path d="M8.5 12.5v3M12 12.5v3M15.5 12.5v3"/>',
    'wifi'       => '<path d="M2.5 8.5a15 15 0 0 1 19 0M5.8 12.2a10 10 0 0 1 12.4 0M9.2 15.8a5 5 0 0 1 5.6 0"/><circle cx="12" cy="19.3" r="1.2"/>',
    'power'      => '<path d="M12 3v9"/><path d="M7.3 6.3a8 8 0 1 0 9.4 0"/>',
    'zap'        => '<path d="M13.5 2 5 13.5h6L10.5 22 19 10.5h-6Z"/>',
    'display'    => '<rect x="2.5" y="4" width="19" height="12.5" rx="2"/><path d="M8.5 20.5h7M12 16.5v4"/>',
    'wrench'     => '<path d="M20 5.5a5 5 0 0 1-6.6 6.6L6 19.5a2.1 2.1 0 0 1-3-3l7.4-7.4A5 5 0 0 1 17 2.5l-3 3 1.5 3 3 1.5Z"/>',
    'box'        => '<path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9Z"/><path d="M3.5 7.5 12 12l8.5-4.5M12 12v9"/>',
    'chart'      => '<path d="M4 20V4M4 20h16"/><path d="M8 16.5v-4M12 16.5V8M16 16.5v-6.5"/>',
    'gear'       => '<circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v2.2M12 19.3v2.2M4.2 12H2M22 12h-2.2M6.3 6.3 4.8 4.8M19.2 19.2l-1.5-1.5M6.3 17.7l-1.5 1.5M19.2 4.8l-1.5 1.5"/>',
    'database'   => '<ellipse cx="12" cy="6" rx="7.5" ry="3"/><path d="M4.5 6v12c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3V6M4.5 12c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3"/>',
    'code'       => '<path d="m8.5 8-5 4 5 4M15.5 8l5 4-5 4M13.5 4.5l-3 15"/>',
    'book'       => '<path d="M4 4.5A1.5 1.5 0 0 1 5.5 3H19v15.5H5.5A1.5 1.5 0 0 0 4 20Z"/><path d="M4 20a1.5 1.5 0 0 0 1.5 1.5H19"/>',
    'search'     => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
    'image'      => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="8.8" cy="9.8" r="1.6"/><path d="m4 17 4.8-4.5 3.4 3.2 3-2.7 4.8 4.5"/>',
    'alert'      => '<path d="M12 3.5 21 19.5H3Z"/><path d="M12 10v4M12 16.8h.01"/>',
    'restart'    => '<path d="M12 3v5"/><path d="M17.6 6.4a8 8 0 1 1-11.2 0"/>',
    'login'      => '<path d="M14 3.5h4.5a1.5 1.5 0 0 1 1.5 1.5v14a1.5 1.5 0 0 1-1.5 1.5H14"/><path d="M4 12h11M11.5 8.5 15 12l-3.5 3.5"/>',
    'paperclip'  => '<path d="M20 11.5 12.2 19.3a5 5 0 0 1-7-7L12.9 4.4a3.3 3.3 0 0 1 4.7 4.7l-7.7 7.8a1.7 1.7 0 0 1-2.4-2.4l7.2-7.2"/>',
    'github'     => '<path d="M9 19c-4.5 1.4-4.5-2.4-6.5-2.9m13 4.9v-3.5a3 3 0 0 0-.85-2.35c2.85-.3 5.85-1.4 5.85-6.3a4.9 4.9 0 0 0-1.3-3.4 4.6 4.6 0 0 0-.1-3.4s-1.05-.3-3.5 1.3a12 12 0 0 0-6.3 0C6.85.65 5.8.95 5.8.95a4.6 4.6 0 0 0-.1 3.4A4.9 4.9 0 0 0 4.4 7.75c0 4.9 3 6 5.85 6.3a3 3 0 0 0-.85 2.3V20"/>',
    'linkedin'   => '<path d="M15 8.5a5.5 5.5 0 0 1 5.5 5.5v6h-3.5v-6a2 2 0 0 0-4 0v6H9.5V8.8H13v1.9A4.4 4.4 0 0 1 15 8.5Z"/><rect x="2.5" y="8.8" width="3.5" height="11.2"/><circle cx="4.25" cy="4.5" r="1.9"/>',
    ); }
    $p = isset($d[$nombre]) ? $d[$nombre] : $d['check'];
    $c = $clase !== '' ? ' class="' . a_e($clase) . '"' : '';
    return '<svg' . $c . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"'
         . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p . '</svg>';
}

// ---------------------------------------------------------------------------
// Piezas compartidas
// ---------------------------------------------------------------------------

/** Rejilla del ecosistema para el pie. */
function a_ecosistema_grid($actual)
{
    $h = '<div class="eco-grid" data-reveal>';
    foreach (a_familia() as $k => $p) {
        if ($k === $actual) {
            $h .= '<div class="eco-card" aria-current="page">';
            $h .= '<div><b>' . a_e($p['nombre']) . '</b><span>Estás aquí</span></div></div>';
        } else {
            $h .= '<a class="eco-card" href="' . a_e($p['url']) . '">';
            $h .= '<div><b>' . a_e($p['nombre']) . '</b><span>' . a_e($p['que']) . '</span></div></a>';
        }
    }
    return $h . '</div>';
}

/** Encabezado de sección: índice en el riel + eyebrow + título + entradilla. */
function a_head($idx, $eyebrow, $titulo, $lede = '', $extra = '')
{
    $h  = '<header class="head' . ($extra ? ' ' . a_e($extra) : '') . '" data-reveal>';
    $h .= '<p class="idx head__idx">' . a_e($idx) . '</p>';
    $h .= '<div class="head__main">';
    if ($eyebrow !== '') { $h .= '<p class="eyebrow">' . a_e($eyebrow) . '</p>'; }
    $h .= '<h2>' . $titulo . '</h2>';
    if ($lede !== '')    { $h .= '<p class="lede">' . a_e($lede) . '</p>'; }
    return $h . '</div></header>';
}

/**
 * Hueco de captura.
 * Si el archivo existe se pinta la imagen; si no, un marco con la ruta y las
 * dimensiones que debe tener, para sustituirlo sin adivinar nada.
 */
function a_shot($archivo, $alt, $w, $h, $pie = '', $fig = '', $clase = '')
{
    $ruta   = __DIR__ . '/' . $archivo;
    $existe = is_file($ruta);
    $osc    = preg_replace('/\.(png|jpe?g|webp)$/i', '-oscuro.$1', $archivo);
    $existeOsc = $osc !== $archivo && is_file(__DIR__ . '/' . $osc);
    $o  = '<figure class="shot' . ($existe ? '' : ' shot--pendiente')
        . ($clase ? ' ' . a_e($clase) : '') . '" data-reveal>';
    $o .= '<div class="shot__box ticks">';
    $o .= '<div class="shot__frame" style="--ar: ' . (int) $w . ' / ' . (int) $h . '">';
    if ($existe) {
        $o .= '<img src="' . a_e($archivo) . '" alt="' . a_e($alt) . '"'
            . ($existeOsc ? ' data-theme-img="light"' : '')
            . ' width="' . (int) $w . '" height="' . (int) $h . '" loading="lazy" decoding="async">';
        if ($existeOsc) {
            $o .= '<img src="' . a_e($osc) . '" alt="' . a_e($alt) . '" data-theme-img="dark"'
                . ' width="' . (int) $w . '" height="' . (int) $h . '" loading="lazy" decoding="async">';
        }
    } else {
        $o .= '<div class="shot__pending">' . a_icon('image')
            . '<b>Captura pendiente</b>'
            . '<code>' . a_e($archivo) . '</code>'
            . '<small>' . (int) $w . ' × ' . (int) $h . ' px</small></div>';
    }
    $o .= '</div></div>';
    if ($pie !== '') {
        $o .= '<figcaption class="shot__cap">';
        if ($fig !== '') { $o .= '<span class="mono">' . a_e($fig) . '</span>'; }
        $o .= '<span>' . a_e($pie) . '</span></figcaption>';
    }
    return $o . '</figure>';
}

/** Conmutador de tema. Muestra el icono de lo que va a activar. */
function a_tema($clase = 'theme-toggle')
{
    return '<button class="' . a_e($clase) . '" type="button" data-theme-toggle aria-label="Cambiar tema">'
         . a_icon('moon', 'i-moon') . a_icon('sun', 'i-sun')
         . '<span class="theme-toggle__label"></span></button>';
}

/** Bloque del autor: la persona real que desarrolla y da soporte. */
function a_autor($cita)
{
    $h  = '<div class="author" data-reveal>';
    $h .= '<img class="author__photo" src="https://gustavoquintero.dev/assets/img/my-profile-image.jpg"'
        . ' alt="Retrato de Gustavo Quintero" width="84" height="84" loading="lazy" decoding="async">';
    $h .= '<div><h3>Gustavo Quintero</h3>';
    $h .= '<p class="author__role">Desarrollo y operación</p>';
    $h .= '<blockquote><p>«' . a_e($cita) . '»</p></blockquote>';
    $h .= '<div class="author__links">';
    $h .= '<a href="mailto:atencion@gustavoquintero.dev">' . a_icon('mail') . 'atencion@gustavoquintero.dev</a>';
    $h .= '<a href="https://gustavoquintero.dev" target="_blank" rel="noopener">' . a_icon('globe')
        . 'gustavoquintero.dev' . a_icon('external') . '</a>';
    $h .= '<a href="https://github.com/GustavoQuinteroC" target="_blank" rel="noopener">' . a_icon('github')
        . 'GitHub' . a_icon('external') . '</a>';
    $h .= '<a href="https://www.linkedin.com/in/gustavo-quintero-81aa18169/" target="_blank" rel="noopener">' . a_icon('linkedin')
        . 'LinkedIn' . a_icon('external') . '</a>';
    $h .= '</div></div></div>';
    return $h;
}

/** Diálogo con las credenciales del demo. */
function a_dialogo_demo($url_app)
{
    $h  = '<dialog id="demoDialog" aria-labelledby="demoTitulo">';
    $h .= '<div class="dlg"><h2 id="demoTitulo">Acceso al demo</h2>';
    $h .= '<p>Entra a la empresa de demostración con estas credenciales.</p>';
    $h .= '<dl class="dlg__creds">';
    $h .= '<div><dt>Usuario</dt><dd>demo@alkes.mx</dd></div>';
    $h .= '<div><dt>Contraseña</dt><dd>Demo@Alkes2026</dd></div>';
    $h .= '</dl>';
    $h .= '<p>Para una demostración guiada, contacta a <strong>Gustavo Quintero</strong> al correo '
        . '<a href="mailto:' . ALKES_MAIL . '">' . ALKES_MAIL . '</a> o al teléfono '
        . '<a href="tel:' . ALKES_TEL . '">' . ALKES_TEL_TXT . '</a>.</p>';
    $h .= '<div class="dlg__foot">';
    $h .= '<a class="btn btn--primary" href="' . a_e($url_app) . '" target="_blank" rel="noopener">Ir a la app' . a_icon('external') . '</a>';
    $h .= '<button class="btn btn--outline" type="button" data-close-demo>Cerrar</button>';
    $h .= '</div></div></dialog>';
    return $h;
}


function a_cache()
{
    static $d = null;
    if ($d !== null) { return $d; }
    $f = __DIR__ . '/planes.cache.json';
    $d = array('producto' => array(), 'planes' => array());
    if (is_file($f)) {
        $j = json_decode((string) @file_get_contents($f), true);
        if (is_array($j)) { $d = $j + $d; }
    }
    return $d;
}

function a_planes()         { $d = a_cache(); return isset($d['planes']) ? $d['planes'] : array(); }
function a_producto_clave() { $d = a_cache(); return isset($d['producto']['clave']) ? $d['producto']['clave'] : ''; }
function a_url_panel()      { $d = a_cache(); return isset($d['producto']['url_panel']) ? $d['producto']['url_panel'] : ALKES_SITIO; }

function a_num($n) { return number_format((float) $n, (floor($n) == $n ? 0 : 2), '.', ','); }

/** Traduce los iconos del catálogo (Bootstrap Icons) al set propio. */
function a_map_icon($bi)
{
    $m = array(
        'bi-people-fill' => 'users',            'bi-cpu-fill' => 'cpu',
        'bi-paperclip' => 'paperclip',          'bi-tools' => 'wrench',
        'bi-arrow-up-circle-fill' => 'refresh', 'bi-clock-history' => 'history',
        'bi-shield-lock' => 'lock',             'bi-box-seam' => 'box',
        'bi-box-seam-fill' => 'box',            'bi-card-checklist' => 'checklist',
        'bi-geo-alt-fill' => 'geo',             'bi-person-badge-fill' => 'badge',
        'bi-file-earmark-bar-graph' => 'report','bi-display-fill' => 'display',
        'bi-ethernet' => 'ethernet',            'bi-hdd-rack-fill' => 'rack',
        'bi-cloud-check-fill' => 'cloud-check',
    );
    return isset($m[$bi]) ? $m[$bi] : 'check';
}

/** Ahorro del plan anual frente a doce mensualidades. Calculado, nunca escrito. */
function a_ahorro_anual()
{
    $m = array(); $a = array();
    foreach (a_planes() as $p) {
        if ($p['periodo'] === 'mensual') { $m[$p['clave']] = (float) $p['precio']; }
        if ($p['periodo'] === 'anual')   { $a[$p['clave']] = (float) $p['precio']; }
    }
    $pcts = array();
    foreach ($a as $k => $precio) {
        if (!empty($m[$k]) && $m[$k] > 0) {
            $full = $m[$k] * 12;
            if ($full > $precio) { $pcts[$k] = (int) round((1 - $precio / $full) * 100); }
        }
    }
    return $pcts ? max($pcts) : 0;
}

/** Rejilla de planes de un periodo. Los de pago único aparecen en ambos. */
function a_grid_planes($periodo)
{
    $items = array();
    foreach (a_planes() as $p) {
        if ($p['periodo'] === $periodo || $p['periodo'] === 'unico') { $items[] = $p; }
    }
    if (!$items) {
        return '<p class="center muted">Por el momento no hay planes publicados. '
             . '<a class="link-arrow" href="mailto:' . ALKES_MAIL . '">Escríbenos' . a_icon('arrow') . '</a></p>';
    }

    $clave  = a_producto_clave();
    $ahorro = a_ahorro_anual();
    $h = '<div class="plans">';

    foreach ($items as $p) {
        $rec = !empty($p['recomendado']);
        $per = $p['periodo'] === 'anual' ? 'MXN / año'
             : ($p['periodo'] === 'unico' ? 'MXN / pago único' : 'MXN / mes');

        $h .= '<div class="plan' . ($rec ? ' plan--rec' : '') . '">';
        $h .= '<div class="plan__head"><h3 class="plan__name">' . a_e($p['nombre']) . '</h3>';
        if ($rec) { $h .= '<span class="plan__tag">Recomendado</span>'; }
        $h .= '</div>';

        $h .= '<p class="plan__price"><span class="plan__cur">$</span>'
            . '<span class="plan__amt">' . a_num($p['precio']) . '</span>'
            . '<span class="plan__per">' . $per . '</span></p>';

        $nota = '';
        if (!empty($p['precio_anterior'])) {
            $nota = 'Antes <s>$' . a_num($p['precio_anterior']) . '</s>';
        } elseif ($p['periodo'] === 'anual') {
            $nota = 'Equivale a $' . a_num(round($p['precio'] / 12)) . ' al mes'
                  . ($ahorro ? ' · ahorras ' . $ahorro . ' %' : '');
        } elseif ($p['periodo'] === 'mensual') {
            $nota = 'Sin permanencia. Cancela cuando quieras.';
        }
        $h .= '<p class="plan__note">' . $nota . '</p>';

        $h .= '<ul class="plan__features">';
        foreach ($p['caracteristicas'] as $c) {
            $h .= '<li>' . a_icon(a_map_icon(isset($c['icono']) ? $c['icono'] : ''))
                . '<div><strong>' . a_e($c['titulo']) . '</strong>';
            if (!empty($c['descripcion'])) { $h .= '<p>' . a_e($c['descripcion']) . '</p>'; }
            $h .= '</div></li>';
        }
        $h .= '</ul>';

        $url = ALKES_CENTRAL . '/?producto=' . urlencode($clave)
             . '&plan=' . urlencode($p['clave'])
             . '&periodo=' . urlencode($p['periodo']);

        $h .= '<a class="btn ' . ($rec ? 'btn--primary' : 'btn--outline') . ' btn--block" href="' . a_e($url) . '">'
            . 'Contratar ' . a_e($p['nombre']) . a_icon('arrow') . '</a>';
        $h .= '</div>';
    }
    return $h . '</div>';
}

if (!defined('SEO_FUNDADOR_ID')) { define('SEO_FUNDADOR_ID', 'https://gustavoquintero.dev/#persona'); }

/** Persona fundadora: la misma entidad que describe gustavoquintero.dev. */
function seo_fundador()
{
    return array(
        '@type' => 'Person', '@id' => SEO_FUNDADOR_ID,
        'name' => 'Gustavo de Jesús Quintero Cerpa', 'alternateName' => 'Gustavo Quintero',
        'jobTitle' => 'Fundador y desarrollador de Alkes',
        'url' => 'https://gustavoquintero.dev/',
        'sameAs' => array('https://www.linkedin.com/in/gustavo-quintero-81aa18169/', 'https://github.com/GustavoQuinteroC'),
        'address' => array('@type' => 'PostalAddress', 'addressLocality' => 'Guadalajara', 'addressRegion' => 'Jalisco', 'addressCountry' => 'MX'),
    );
}

/** Versión publicada más reciente, leída del historial de novedades. */
function seo_version($dir)
{
    $t = (string) file_get_contents($dir . '/index.php');
    return preg_match('/disc__v">v(\d+\.\d+\.\d+)</', $t, $m) ? $m[1] : '';
}

/**
 * Lista de funciones y capturas a partir de los arreglos de la página.
 * Acepta $CAPACIDADES (con 'items' e 'img'), $SOSTIENE y $AVANZADAS.
 */
function seo_de_pagina($sitio, $capacidades, $extras = array())
{
    $funciones = array();
    $capturas = array();
    foreach ((array) $capacidades as $c) {
        if (!empty($c['h3'])) { $funciones[] = $c['h3']; }
        foreach ((array) (isset($c['items']) ? $c['items'] : array()) as $it) {
            if (is_array($it) && isset($it[1])) { $funciones[] = $it[1]; }
        }
        if (!empty($c['img'])) {
            $capturas[] = array_filter(array('@type' => 'ImageObject', 'url' => $sitio . '/' . ltrim($c['img'], '/'),
                                'caption' => isset($c['alt']) ? $c['alt'] : '',
                                'width' => isset($c['w']) ? (int) $c['w'] : null, 'height' => isset($c['h']) ? (int) $c['h'] : null),
                                function ($v) { return $v !== null && $v !== ''; });
        }
    }
    foreach ((array) $extras as $lista) {
        foreach ((array) $lista as $it) {
            if (is_array($it) && isset($it[1])) { $funciones[] = $it[1]; }
        }
    }
    $funciones = array_values(array_unique(array_filter($funciones)));
    return array('funciones' => $funciones, 'capturas' => $capturas);
}

/** Etiquetas extra del <head>. $s: sitio, nombre, titulo, alt, palabras, noindex. */
function seo_head($s)
{
    $e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
    $sitio = $s['sitio'];
    $og = $sitio . '/assets/img/og-image.png';
    $out = array();
    if (empty($s['noindex'])) {
        $out[] = '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">';
        $out[] = '<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1">';
        $out[] = '<meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1">';
    }
    $out[] = '<meta name="keywords" content="' . $e(implode(', ', $s['palabras'])) . '">';
    $out[] = '<meta name="application-name" content="' . $e($s['nombre']) . '">';
    $out[] = '<meta name="apple-mobile-web-app-title" content="' . $e($s['nombre']) . '">';
    $out[] = '<meta name="creator" content="Gustavo Quintero">';
    $out[] = '<meta name="publisher" content="Alkes">';
    $out[] = '<meta name="classification" content="Software empresarial en la nube">';
    $out[] = '<meta name="language" content="es-MX">';
    $out[] = '<meta name="geo.region" content="MX-JAL">';
    $out[] = '<meta name="geo.placename" content="Guadalajara, Jalisco, México">';
    $out[] = '<meta name="geo.position" content="20.6597;-103.3496">';
    $out[] = '<meta name="ICBM" content="20.6597, -103.3496">';
    $out[] = '<link rel="alternate" hreflang="es-MX" href="' . $sitio . '/">';
    $out[] = '<link rel="alternate" hreflang="es" href="' . $sitio . '/">';
    $out[] = '<link rel="alternate" hreflang="x-default" href="' . $sitio . '/">';
    $out[] = '<link rel="alternate" type="text/markdown" title="Resumen para modelos de lenguaje" href="' . $sitio . '/llms.txt">';
    $out[] = '<link rel="sitemap" type="application/xml" href="' . $sitio . '/sitemap.xml">';
    $out[] = '<link rel="author" href="https://gustavoquintero.dev/">';
    $out[] = '<meta property="og:image:secure_url" content="' . $og . '">';
    $out[] = '<meta property="og:image:type" content="image/png">';
    $out[] = '<meta name="twitter:image:alt" content="' . $e($s['alt']) . '">';
    return implode("\n", $out) . "\n";
}

/**
 * Enriquece el grafo JSON-LD que arma cada landing.
 * $s: sitio, nombre, subcategoria, palabras, version, funciones, capturas,
 *     dispositivos, requisitos, titulo.
 */
function seo_enriquecer($g, $s)
{
    if (empty($s) || empty($g['@graph'])) { return $g; }
    $sitio = $s['sitio'];
    $org = ALKES_SITIO . '/#org';
    foreach ($g['@graph'] as $i => $n) {
        $tipo = isset($n['@type']) ? $n['@type'] : '';
        if ($tipo === 'Organization') {
            $g['@graph'][$i] = array_merge($n, array(
                'alternateName' => array('Alkes Software y Soluciones TI', 'Alkes TI'),
                'description' => 'Empresa de software y soluciones TI en Guadalajara, Jalisco. Desarrolla y opera sistemas en la nube para empresas mexicanas: ERP con CFDI 4.0, pendientes con IA, control de asistencia, mantenimiento y reinicio remoto de equipos.',
                'logo' => array('@type' => 'ImageObject', 'url' => ALKES_SITIO . '/assets/img/logo_alkes.png'),
                'address' => array('@type' => 'PostalAddress', 'addressLocality' => 'Guadalajara', 'addressRegion' => 'Jalisco', 'addressCountry' => 'MX'),
                'areaServed' => array('@type' => 'Country', 'name' => 'México'),
                'founder' => array('@id' => SEO_FUNDADOR_ID),
                'foundingLocation' => array('@type' => 'Place', 'name' => 'Guadalajara, Jalisco, México'),
                'contactPoint' => array('@type' => 'ContactPoint', 'contactType' => 'Ventas y soporte', 'telephone' => ALKES_TEL,
                                        'email' => ALKES_MAIL, 'areaServed' => 'MX', 'availableLanguage' => array('es')),
                'knowsAbout' => array('Software empresarial', 'ERP', 'Facturación electrónica CFDI 4.0', 'Gestión de pendientes',
                                      'Control de asistencia', 'Mantenimiento preventivo', 'Internet de las cosas', 'Inteligencia artificial'),
            ));
        } elseif ($tipo === 'SoftwareApplication') {
            $extra = array(
                'applicationSubCategory' => $s['subcategoria'],
                'keywords' => implode(', ', $s['palabras']),
                'countriesSupported' => 'MX',
                'availableOnDevice' => $s['dispositivos'],
                'browserRequirements' => $s['requisitos'],
                'provider' => array('@id' => $org),
                'creator' => array('@id' => SEO_FUNDADOR_ID),
                'author' => array('@id' => SEO_FUNDADOR_ID),
                'isAccessibleForFree' => false,
                'mainEntityOfPage' => array('@id' => $sitio . '/#pagina'),
            );
            if (!empty($s['version'])) {
                $extra['softwareVersion'] = $s['version'];
                $extra['releaseNotes'] = $sitio . '/#novedades';
            }
            if (!empty($s['funciones'])) { $extra['featureList'] = $s['funciones']; }
            if (!empty($s['capturas'])) { $extra['screenshot'] = $s['capturas']; }
            $g['@graph'][$i] = array_merge($n, $extra);
        } elseif ($tipo === 'WebSite') {
            $g['@graph'][$i] = array_merge($n, array(
                'description' => $s['titulo'],
                'creator' => array('@id' => SEO_FUNDADOR_ID),
                'copyrightHolder' => array('@id' => $org),
            ));
        }
    }
    $g['@graph'][] = array(
        '@type' => 'WebPage', '@id' => $sitio . '/#pagina', 'url' => $sitio . '/', 'name' => $s['titulo'],
        'isPartOf' => array('@id' => $sitio . '/#web'), 'about' => array('@id' => $sitio . '/#app'),
        'mainEntity' => array('@id' => $sitio . '/#app'), 'inLanguage' => 'es-MX',
        'primaryImageOfPage' => array('@type' => 'ImageObject', 'url' => $sitio . '/assets/img/og-image.png', 'width' => 1200, 'height' => 630),
        'breadcrumb' => array('@id' => $sitio . '/#migas'),
        'dateModified' => date('Y-m-d', (int) @filemtime(__DIR__ . '/index.php')),
        'publisher' => array('@id' => $org),
    );
    $g['@graph'][] = array(
        '@type' => 'BreadcrumbList', '@id' => $sitio . '/#migas',
        'itemListElement' => array(
            array('@type' => 'ListItem', 'position' => 1, 'name' => 'Alkes', 'item' => ALKES_SITIO . '/'),
            array('@type' => 'ListItem', 'position' => 2, 'name' => $s['nombre'], 'item' => $sitio . '/'),
        ),
    );
    $g['@graph'][] = seo_fundador();
    return $g;
}


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
// SEO y GEO: datos para seo_head() y seo_enriquecer() (ver seo_head).
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
                        <details class="disc disc--latest" open>
                <summary class="disc__sum">
                    <span class="disc__v">v6.4.0</span>
                    <span class="disc__t">Un pendiente resuelto o cancelado queda como está</span>
                    <span class="disc__d">26 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Historial confiable</strong><p>Un pendiente resuelto o cancelado ya no se edita ni se reasigna. Puedes seguir agregando notas, archivos, seguidores y etiquetas, o duplicarlo para empezar otro.</p></div></li>
                    <li><div><strong>Los archivos se conservan</strong><p>En un pendiente cerrado puedes subir archivos, pero ya no se borran.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.3.1</span>
                    <span class="disc__t">Mejora interna del manejo de archivos</span>
                    <span class="disc__d">24 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Sin cambios en tu forma de trabajar</strong><p>Se ordenó el código que guarda tus archivos para que sea el mismo en todos los sistemas de Alkes.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.3.0</span>
                    <span class="disc__t">Mi nube: tus archivos en un solo lugar</span>
                    <span class="disc__d">23 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Mi nube</strong><p>En el menú de tu nombre, después de «Mi perfil y ajustes»: todos tus archivos con el pendiente o proyecto al que pertenecen, para verlos, descargarlos, borrarlos o subir archivos sueltos. Cada quien ve solo lo suyo.</p></div></li>
                    <li><div><strong>Tu espacio, siempre a la vista</strong><p>Cada usuario tiene su propio espacio y una barra te dice cuánto llevas usado, con aviso cuando se está llenando.</p></div></li>
                    <li><div><strong>Archivos en tabla</strong><p>Los archivos de pendientes y proyectos se ven en una tabla con búsqueda y orden. Los borra quien los subió o un administrador.</p></div></li>
                    <li><div><strong>Formulario público</strong><p>Los archivos que adjunta quien reporta se guardan con el pendiente; si no hay espacio, el pendiente se crea igual y se le avisa.</p></div></li>
                    <li><div><strong>Archivos mejor protegidos</strong><p>Cada archivo se revisa por su contenido real y solo se descarga con permiso.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.6</span>
                    <span class="disc__t">Captura más a prueba de errores</span>
                    <span class="disc__d">17 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Proyectos coherentes</strong><p>Un proyecto no puede terminar antes de empezar, y su cliente y quien lo lleva deben ser de tu organización.</p></div></li>
                    <li><div><strong>Pendientes programados editables</strong><p>Ya puedes editar un pendiente programado que ya inició; antes la fecha de inicio pasada lo impedía.</p></div></li>
                    <li><div><strong>Teléfono y correo del cliente</strong><p>Se revisa que tengan un formato válido.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.5</span>
                    <span class="disc__t">El visor de PDF funciona en el celular</span>
                    <span class="disc__d">16 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>PDF dentro del mismo visor</strong><p>En Chrome para Android y otros navegadores sin visor propio, el PDF ahora se ve en el modal, página por página, con su botón Descargar.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.4</span>
                    <span class="disc__t">«Recuérdame» en hasta 5 equipos</span>
                    <span class="disc__d">16 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Cada equipo se recuerda por separado</strong><p>Puedes recordar hasta 5 equipos a la vez; al recordar el sexto se olvida el que lleva más tiempo sin usarse.</p></div></li>
                    <li><div><strong>Lista en Mi perfil</strong><p>En Mi perfil › Seguridad ves tus equipos recordados con navegador, sistema, IP y último uso, y puedes olvidar el que quieras.</p></div></li>
                    <li><div><strong>Más seguro</strong><p>Cerrar sesión olvida solo ese equipo, y cambiar la contraseña o dar de baja a un usuario olvida sus equipos recordados. Cada persona inicia sesión una vez más y vuelve a marcar «Recordarme».</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.3</span>
                    <span class="disc__t">Correos de recuperación funcionando</span>
                    <span class="disc__d">16 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Se corrigió el envío de correos</strong><p>Se resolvió un error que impedía mandar el código de recuperación de contraseña y los correos de documentos.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.2</span>
                    <span class="disc__t">Recuperar contraseña y acceso desde el celular</span>
                    <span class="disc__d">16 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Recupera tu contraseña desde el acceso</strong><p>Escribes tu correo, recibes un código de 6 dígitos que vence a los 15 minutos y con él pones tu contraseña nueva.</p></div></li>
                    <li><div><strong>Acceso cómodo en el celular</strong><p>La pantalla de acceso se ajusta a cualquier tamaño, en vertical y en horizontal, sin hacer scroll ni tapar la tarjeta con el teclado.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.1</span>
                    <span class="disc__t">Tablas con el mismo control de orden</span>
                    <span class="disc__d">15 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Un solo control de orden</strong><p>Las tablas de Reportes y el modal de selección ordenan desde el encabezado con el mismo menú (ascendente, descendente, limpiar) que el resto.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.2.0</span>
                    <span class="disc__t">Botones iguales y en el mismo orden en todos los módulos</span>
                    <span class="disc__d">15 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Cada botón, siempre del mismo color</strong><p>Guardar y Borrador en azul, Procesar en verde, Cancelar y Eliminar en rojo, todas las autorizaciones en amarillo, Imprimir y Regresar en gris. Los mismos colores en todos los módulos.</p></div></li>
                    <li><div><strong>Siempre en el mismo orden</strong><p>De izquierda a derecha, por relevancia: primero guardar, luego las acciones, las cancelaciones y las autorizaciones. Al extremo derecho van Enviar, Información y Regresar.</p></div></li>
                    <li><div><strong>Sin menús escondidos</strong><p>Todos los botones están a la vista, uno por uno, rellenos y con su ícono. Ya no hay botones de solo contorno ni menús «Más».</p></div></li>
                    <li><div><strong>Mejor en el celular</strong><p>En pantallas chicas los botones muestran solo su ícono para que quepan, y la acción principal conserva su texto.</p></div></li>
                    <li><div><strong>El botón de correo, más corto</strong><p>Ahora se llama simplemente Enviar.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.1.0</span>
                    <span class="disc__t">Envía pendientes, proyectos y fichas de clientes por correo</span>
                    <span class="disc__d">15 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Del pendiente al correo de tu cliente</strong><p>Con el botón Enviar mandas la ficha de un pendiente, el informe de un proyecto o la ficha de un cliente. El PDF va como archivo adjunto.</p></div></li>
                    <li><div><strong>Revisas antes de enviar</strong><p>Una ventana te muestra el correo del cliente, el asunto y el mensaje para que los ajustes, y eliges qué secciones incluye el PDF.</p></div></li>
                    <li><div><strong>Con el nombre de tu organización</strong><p>El correo sale a nombre de tu organización, con tu firma y tu pie. Las respuestas de tus clientes llegan al correo que elijas.</p></div></li>
                    <li><div><strong>Con tu propio servidor de correo</strong><p>Si lo prefieres, configura el servidor de correo de tu organización para que los envíos salgan desde tu dominio y sin el límite de uso justo.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.0.6</span>
                    <span class="disc__t">Formularios más claros, con ícono en cada campo</span>
                    <span class="disc__d">14 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Un ícono en cada campo</strong><p>Cada dato que capturas lleva su ícono al lado izquierdo, para reconocer de un vistazo qué se pide.</p></div></li>
                    <li><div><strong>Nombre y campo en la misma línea</strong><p>El nombre del campo queda a la izquierda y el campo a su derecha, en todos los formularios y ventanas.</p></div></li>
                    <li><div><strong>Tarjetas con título e ícono</strong><p>Cada bloque de una pantalla se identifica con su ícono y su título.</p></div></li>
                    <li><div><strong>Configuración y datos para facturar con el mismo orden</strong><p>Las pantallas de Configuración, suscripción y datos para facturar siguen el mismo acomodo que el resto del sistema.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.0.5</span>
                    <span class="disc__t">Imprime proyectos, pendientes y clientes como documento</span>
                    <span class="disc__d">14 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Un PDF listo para imprimir</strong><p>El botón Imprimir, al pie del formulario de un proyecto, un pendiente o un cliente, genera un documento con los datos del registro.</p></div></li>
                    <li><div><strong>Tú eliges qué incluye</strong><p>En una ventana marcas las secciones que quieres (pendientes, notas, personas, archivos, imágenes, historial, servicios contratados) y ves cuántos elementos tiene cada una. Recuerda tu última elección.</p></div></li>
                    <li><div><strong>Se ve sin salir de la pantalla</strong><p>El documento se abre en una ventana, igual que los archivos adjuntos, con la opción de descargarlo.</p></div></li>
                    <li><div><strong>Con el logo y el nombre de tu organización</strong><p>Arriba lleva el logo y el nombre de tu organización. SmartPend aparece solo como un crédito discreto al pie.</p></div></li>
                    <li><div><strong>Los reportes, con el mismo estilo</strong><p>El PDF y la impresión de los reportes usan el mismo diseño neutro y la misma cabecera.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.0.4</span>
                    <span class="disc__t">Cobro por usuario, cupo editable y asistente de IA por usuario</span>
                    <span class="disc__d">13 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <ul>
                    <li><div><strong>Tú decides cuántos usuarios contratas</strong><p>Ya no hay planes ni topes por tipo de usuario. Contratas un número de usuarios y cada persona activa ocupa un lugar, sea cual sea su rol.</p></div></li>
                    <li><div><strong>Cambia tu cupo desde Configuración</strong><p>El administrador sube o baja el total de varios en varios y ve la vista previa del cobro antes de confirmar. Al subir, se cobra al momento la parte proporcional; al bajar, el cambio se aplica en la renovación.</p></div></li>
                    <li><div><strong>Aviso cuando el cupo se llena</strong><p>Dar de alta o reactivar a alguien con el cupo lleno no genera ningún cobro: el sistema avisa y tú decides si amplías.</p></div></li>
                    <li><div><strong>Asistente de IA que crece contigo</strong><p>El dictado de pendientes y el asistente usan un cupo mensual de IA que aumenta con cada usuario contratado.</p></div></li>
                    <li><div><strong>Datos para facturar</strong><p>En Configuración puedes capturar, si los necesitas, los datos fiscales de tu suscripción.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.0.3</span>
                    <span class="disc__t">La misma forma de trabajar en SmartPend, Mantis y GeoCheck</span>
                    <span class="disc__d">8 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>El pendiente abierto, en su tarjeta</strong><p>El folio y el estado del registro que consultas salen en el encabezado de la tarjeta principal del formulario, junto a sus datos. Arriba ya no se repite el nombre del módulo.</p></div></li>
                    <li><div><strong>Manual de usuario desde tu nombre</strong><p>La ayuda se abre desde el menú de tu nombre, arriba a la derecha, y ya no ocupa espacio al pie del menú lateral.</p></div></li>
                    <li><div><strong>Logo y foto con un solo botón</strong><p>En Configuración y en tu perfil, el logo de la organización y tu foto se cambian con un botón propio y se ven al instante. El estado de la suscripción sale en español.</p></div></li>
                    <li><div><strong>Formularios y dashboard más limpios</strong><p>Cada formulario tiene Regresar al pie y sus tarjetas se maximizan. Los indicadores del dashboard dejan los degradados y usan los colores de tu tema.</p></div></li>
                    <li><div><strong>Maximizar junto al buscador</strong><p>En los listados, el botón para ver la tabla a pantalla completa está a un lado de Buscar.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
            <summary class="disc__sum">
            <span class="disc__v">v6.0.2</span>
            <span class="disc__t">Pendientes sin asignar visibles a todos</span>
            <span class="disc__d">8 oct 2026</span>
            <?= a_icon('chevron', 'disc__chev') ?>
            </summary>
            <div class="disc__body"><ul>
            <li><div><strong>Sin asignar, visible para todo el equipo</strong><p>Un pendiente o plantilla sin persona asignada ahora aparece en la lista de cualquier integrante de la organización, igual que en Jira, ClickUp o Asana. Antes solo lo veía quien lo creó o quien estaba en su mismo grupo.</p></div></li>
            <li><div><strong>El creador siempre ve su propio pendiente</strong><p>Quien registra un pendiente o una plantilla programada ahora lo ve siempre, aunque lo asigne a alguien de otro grupo o a un área diferente.</p></div></li>
            <li><div><strong>Proyectos sin responsable visibles al equipo</strong><p>Un proyecto sin responsable asignado ya aparece para todos los integrantes de la organización, en lugar de quedar oculto.</p></div></li>
            </ul></div>
            </details>
            <details class="disc">
            <summary class="disc__sum">
            <span class="disc__v">v6.0.1</span>
            <span class="disc__t">Dashboard mejor espaciado</span>
            <span class="disc__d">6 oct 2026</span>
            <?= a_icon('chevron', 'disc__chev') ?>
            </summary>
            <div class="disc__body"><ul>
            <li><div><strong>Indicadores con aire</strong><p>Las tarjetas de color y las barras de avance del dashboard ya no se ven pegadas.</p></div></li>
            </ul></div>
            </details>

            <details class="hist">
                <summary>Ver historial completo, de v6.0.0 a v1.0.0<?= a_icon('chevron') ?></summary>
                <div style="margin-top:10px">            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v6.0.0</span>
                    <span class="disc__t">Interfaz renovada, tablero de pendientes y búsqueda en todo el sistema</span>
                    <span class="disc__d">4 oct 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>Interfaz renovada de punta a punta</strong><p>SmartPend estrena diseño completo. El encabezado, el menú y el pie se quedan fijos mientras trabajas, y tablas, calendario, gráficas y avisos pasan a sus versiones actuales. Se trabaja igual de cómodo en el celular, la tablet o la computadora.</p></div></li>
                    <li><div><strong>Tablero de pendientes</strong><p>Además de la lista, los pendientes se ven en un tablero con una columna por estado. Arrastra una tarjeta para iniciarlo, resolverlo o cancelarlo: se abre la misma confirmación del formulario, con las mismas reglas. Los vencidos se marcan en rojo, y un interruptor deja solo los asignados a ti.</p></div></li>
                    <li><div><strong>Búsqueda en todo el sistema</strong><p>Escribe un folio, un cliente o un proyecto en la barra de arriba, desde cualquier pantalla. Los resultados llegan agrupados en pendientes, proyectos, clientes y usuarios, con lo buscado resaltado. Solo aparece lo que tú puedes abrir.</p></div></li>
                    <li><div><strong>Mi perfil y ajustes, sin salir de lo que haces</strong><p>Desde tu nombre, arriba a la derecha, se abre una ventana con tus datos y tus cifras del mes, el cambio de contraseña, la opción de cerrar sesión en los demás equipos, la apariencia y tu actividad reciente.</p></div></li>
                    <li><div><strong>Tú eliges qué avisos recibir</strong><p>Un interruptor por tipo de aviso: cambios de estado, asignaciones, notas, archivos, etiquetas y seguidores, cambios de datos y lo que haces tú mismo. La campana distingue de un vistazo lo nuevo de lo leído.</p></div></li>
                    <li><div><strong>Tema automático, 20 colores y modo compacto</strong><p>Además de claro y oscuro, el tema automático sigue al de tu equipo. El color de énfasis suma 14 tonos nuevos, y el modo compacto deja más filas a la vista en pantallas bajas.</p></div></li>
                    <li><div><strong>Un dashboard que dice cómo va el trabajo</strong><p>Cada indicador lleva directo al listado de ese estado. Tres barras miden qué tanto está al día: abiertos dentro de su fecha, abiertos con alguien asignado y resueltos a tiempo en el mes. Los proyectos en curso muestran su avance y su equipo, con los atrasados primero, y la actividad reciente se lee como una línea de tiempo.</p></div></li>
                    <li><div><strong>Historial, notas y adjuntos más fáciles de seguir</strong><p>El historial es una línea de tiempo agrupada por día. Las notas se leen como una conversación, y una nota rápida se envía con Enter. Los adjuntos se arrastran con barra de progreso, se ven en cuadrícula con miniaturas y las fotos se recorren en galería.</p></div></li>
                    <li><div><strong>El pendiente avisa cuando se le acaba el tiempo</strong><p>Al abrir un pendiente vencido, un aviso dice desde cuándo; si vence en las próximas 24 horas, también lo advierte. Una cinta en la esquina marca si está vencido, resuelto o cancelado.</p></div></li>
                    <li><div><strong>Listados que caben en la pantalla</strong><p>El texto largo se acomoda en varias líneas para que las columnas quepan, y si aun así hay que desplazarse, la columna de acciones se queda a la vista. Cada columna tiene su propio filtro (contiene, empieza con, vacío y más) y cualquier listado se exporta a PDF.</p></div></li>
                    <li><div><strong>Imprime un pendiente o un proyecto</strong><p>Sale como documento, con el logo de tu organización, todas sus secciones una tras otra y la fecha de impresión, sin los botones de la pantalla.</p></div></li>
                    <li><div><strong>Menú con buscador y con tu logo</strong><p>Escribe el nombre de un módulo y ábrelo con Enter; las teclas <kbd>/</kbd> o <kbd>Ctrl</kbd>+<kbd>K</kbd> llevan al buscador desde cualquier pantalla. El menú muestra el logo de tu organización y cuántos pendientes tienes abiertos, y se puede reducir a una fila de íconos.</p></div></li>
                    <li><div><strong>Manual de usuario dentro del sistema</strong><p>Un botón al pie del menú abre la ayuda justo en la sección de la pantalla donde estás: 44 preguntas con buscador.</p></div></li>
                    <li><div><strong>Errores claros en los formularios</strong><p>Si un dato falta o no es válido, el campo se marca en rojo con el mensaje debajo, usando el nombre que ves en pantalla. En las ventanas largas, el título y los botones se quedan fijos y solo se desplaza el contenido.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Impacto en tu día a día</h4>
                    <ul>
                    <li><div><strong>Los mismos flujos, con otra cara</strong><p>Lo que ya sabías hacer se hace igual. Cerrar sesión cambia de lugar: ahora está en el menú de tu nombre, arriba a la derecha.</p></div></li>
                    <li><div><strong>Para ordenar, el botón junto al título</strong><p>En los listados, el clic sobre el título de una columna ya no la ordena; se ordena con su botón. El filtro de cada columna sube del pie de la tabla al encabezado.</p></div></li>
                    <li><div><strong>Tu nombre y tu correo los cambia un administrador</strong><p>Desde Usuarios, como siempre. En tu perfil se ven, pero no se editan.</p></div></li>
                    <li><div><strong>Amarillo y Claro dejan de ser colores de énfasis</strong><p>Con texto blanco no se leían. Quien los tenía pasa a Gris y puede elegir otro desde Mi perfil y ajustes.</p></div></li>
                    <li><div><strong>Sale el estado «En revisión»</strong><p>Ningún paso del sistema llevaba a él. Los pendientes abiertos son los que están en Pendiente o En proceso.</p></div></li>
                    <li><div><strong>En proyectos, los seguidores ya no borran archivos</strong><p>Un seguidor consulta el proyecto y sube archivos, pero borrarlos queda para quien puede operarlo, igual que en los pendientes.</p></div></li>
                    <li><div><strong>Una recarga la primera vez</strong><p>Si tenías SmartPend abierto durante la actualización, recarga la página antes de seguir.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>El botón Duplicar se multiplicaba</strong><p>En Proyectos y Pendientes programados aparecía un botón Duplicar más cada vez que se cambiaba un campo. Ahora siempre hay uno.</p></div></li>
                    <li><div><strong>La ✕ que escondía secciones</strong><p>Las tarjetas de los formularios tenían un botón que ocultaba la sección entera, sin forma de recuperarla salvo recargando la página. Se quitó.</p></div></li>
                    <li><div><strong>El buscador de una columna reordenaba la tabla</strong><p>En las tablas dentro de los formularios, hacer clic en el buscador de una columna cambiaba el orden de las filas.</p></div></li>
                    <li><div><strong>Modo oscuro incompleto</strong><p>La fila de los días del calendario salía en blanco y las gráficas no cambiaban de color con el tema.</p></div></li>
                    <li><div><strong>«Sin asignar» contaba de más</strong><p>El indicador del dashboard sumaba pendientes resueltos y cancelados sin persona asignada. Ahora cuenta solo los abiertos.</p></div></li>
                    <li><div><strong>Movimientos de «Desconocido»</strong><p>Lo que registran el formulario público y los pendientes programados aparecía en el historial a nombre de «Desconocido». Ahora dice «Sistema».</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
            <summary class="disc__sum">
            <span class="disc__v">v5.2.2</span>
            <span class="disc__t">Íconos a color y contratación sin tropiezos</span>
            <span class="disc__d">3 oct 2026</span>
            <?= a_icon('chevron', 'disc__chev') ?>
            </summary>
            <div class="disc__body"><ul>
            <li><div><strong>Íconos que se reconocen</strong><p>El ícono de la pestaña se lee igual en fondo claro y oscuro, y el de la pantalla de inicio del celular conserva los colores del logo.</p></div></li>
            <li><div><strong>Contratar sin errores</strong><p>Se corrigió una validación de seguridad que podía interrumpir el pago al contratar un plan.</p></div></li>
            </ul></div>
            </details>
            <details class="disc">
            <summary class="disc__sum">
            <span class="disc__v">v5.2.1</span>
            <span class="disc__t">Lo asignado a ti siempre se ve</span>
            <span class="disc__d">3 oct 2026</span>
            <?= a_icon('chevron', 'disc__chev') ?>
            </summary>
            <div class="disc__body"><ul>
            <li><div><strong>Lo tuyo, aunque no estés en un grupo</strong><p>Los pendientes asignados a ti, los proyectos a tu cargo y las tareas programadas que te tocan se ven y se operan siempre, estés o no en un grupo.</p></div></li>
            <li><div><strong>Adjuntos que se abren a quien debe</strong><p>Un adjunto de un pendiente que cambió de organización ya no le aparece bloqueado a quien sí tiene acceso.</p></div></li>
            <li><div><strong>Pagar después de un cobro fallido</strong><p>Al ponerte al corriente, tu cuenta sale del periodo de gracia en ese momento.</p></div></li>
            <li><div><strong>Íconos nuevos en el navegador</strong><p>Favicon e ícono de inicio renovados.</p></div></li>
            </ul></div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v5.2.0</span>
                    <span class="disc__t">Revisión de seguridad de todo el sistema</span>
                    <span class="disc__d">14 sep 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>Auditoría completa y endurecimiento</strong><p>Se revisó el sistema de punta a punta —sesiones, formularios, permisos entre organizaciones, entrega de archivos y registro de actividad— y se aplicaron las correcciones que salieron de esa revisión. No cambia cómo se trabaja: es trabajo de cimientos.</p></div></li>
                    <li><div><strong>Los archivos adjuntos exigen sesión y permisos</strong><p>Antes, quien tuviera la dirección exacta de un adjunto podía abrirlo sin entrar al sistema. Ahora toda descarga comprueba quién eres y si ese registro es tuyo o de tu grupo. <strong>Si guardaste el enlace directo de algún archivo, deja de funcionar</strong>: los adjuntos se abren desde el pendiente o el proyecto, como siempre.</p></div></li>
                    <li><div><strong>Cada organización, más aislada de las demás</strong><p>Se reforzaron las comprobaciones de pertenencia en la administración de usuarios y grupos, para que ninguna acción pueda alcanzar registros de otra organización.</p></div></li>
                    <li><div><strong>Las contraseñas ya no llegan a la bitácora</strong><p>El registro de actividad oculta contraseñas, códigos y claves antes de escribir nada en disco.</p></div></li>
                    <li><div><strong>Los fallos ya no muestran detalles internos</strong><p>Si algo se rompe, el sistema lo anota en su bitácora en vez de enseñar rutas y detalles técnicos en pantalla.</p></div></li>
                    <li><div><strong>Folios sin choques</strong><p>Dos altas al mismo tiempo ya no pueden llevarse el mismo número de folio.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Impacto en tu día a día</h4>
                    <ul>
                    <li><div><strong>Nada que aprender de nuevo</strong><p>Las pantallas y los flujos son los mismos. Lo único que se nota es el punto de los adjuntos.</p></div></li>
                    <li><div><strong>Una recarga la primera vez</strong><p>Si tenías SmartPend abierto durante la actualización, la primera acción en esa pestaña te pedirá recargar la página. Después, normal.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>«Acceso denegado» al consultar un pendiente</strong><p>Al abrir un pendiente que sí se podía consultar, saltaba un aviso de «No tienes permisos sobre este registro» aunque el pendiente se viera bien detrás. Pasaba cuando la persona asignada no pertenecía a ningún grupo. Ya no aparece: el registro se consulta con normalidad y solo se ocultan los botones de lo que esa persona no puede hacer.</p></div></li>
                    <li><div><strong>El bloque de seguidores se quedaba vacío</strong><p>En esos mismos casos, la lista de seguidores no se pintaba y no decía por qué. Ahora se ve siempre que puedas consultar el registro.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v5.1.1</span>
                    <span class="disc__t">Correcciones sobre la 5.1.0</span>
                    <span class="disc__d">9 sep 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>Servicios y Categorías no se dejaban editar</strong><p>Al guardar un cambio en un servicio o en una categoría, el sistema respondía «permiso denegado» incluso a quien tenía todos los permisos concedidos. Ya se editan con normalidad, y darlos de baja exige el permiso de eliminar, como en el resto del sistema.</p></div></li>
                    <li><div><strong>Detalle de escritura en dos avisos</strong><p>Los mensajes de alta de cliente y de pendiente mostraban los signos de exclamación al revés.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v5.1.0</span>
                    <span class="disc__t">Roles y permisos a tu medida, IA que planifica proyectos y menos fricción al capturar</span>
                    <span class="disc__d">9 sep 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>Roles y permisos a la medida de tu organización</strong><p>Se acabó el "administrador o nada". Ahora defines roles con permisos por módulo —ver, crear, editar, dar de baja, operar y un permiso avanzado para actuar sobre el trabajo de otros— y puedes concederle o quitarle un permiso suelto a una persona concreta sin inventar un rol nuevo. Ningún módulo queda cerrado por sistema: quien tú decidas puede entrar a Usuarios, Servicios o Reportes.</p></div></li>
                    <li><div><strong>Genera los pendientes de un proyecto con IA</strong><p>Describe el proyecto y pide la lista de pendientes. Puedes dar instrucciones concretas —"uno por cada sucursal: Norte, Centro y Sur"— y la propuesta llega con su descripción, fecha, servicio y etiquetas. Se revisa y se edita antes de crear nada: hasta que no presionas Crear, no existe ningún pendiente.</p></div></li>
                    <li><div><strong>Duplicar como plantilla</strong><p>Un pendiente, proyecto o tarea programada se abre como punto de partida de uno nuevo, con sus datos, etiquetas y seguidores ya cargados. No se copia nada: se llena el alta y tú decides qué cambiar antes de guardar. En las tareas programadas ahorra volver a elegir toda la recurrencia.</p></div></li>
                    <li><div><strong>Alta rápida de catálogos sin salir del formulario</strong><p>Si al registrar algo falta el cliente, el servicio, la categoría o la etiqueta, se crean ahí mismo con un botón junto al desplegable y quedan seleccionados al instante, respetando lo que cada quien tiene permitido crear.</p></div></li>
                    <li><div><strong>Seguir un registro deja de ser automático</strong><p>Ser responsable ya no obliga a seguir el registro: los seguidores se ponen y se quitan a voluntad. Y un seguidor puede ver el registro aunque no comparta grupo, para casos como el administrativo que prepara una cotización y suma al vendedor.</p></div></li>
                    <li><div><strong>Los listados se desplazan de lado</strong><p>En pantallas estrechas las columnas ya no se esconden detrás de un botón: el listado conserva todas sus columnas y se desliza horizontalmente, que es como se lee una tabla.</p></div></li>
                    <li><div><strong>Confirmación al guardar, en todo el sistema</strong><p>Los formularios guardan solo, campo a campo, pero antes no decían nada. Ahora cada cambio confirma en pantalla que quedó registrado.</p></div></li>
                    <li><div><strong>Menos fricción para empezar</strong><p>Al abrir un alta, el sistema avisa de qué catálogos faltan por crear antes y te lleva a crearlos. Las descripciones de los catálogos y la categoría de un servicio dejan de ser obligatorias, y las fechas de registro se llenan solas.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Impacto en tu día a día</h4>
                    <ul>
                    <li><div><strong>Cada quien hace exactamente lo suyo</strong><p>Dejas de repartir permisos de administrador solo para que alguien pueda dar de alta un cliente, y dejas de bloquear a quien únicamente necesitaba una parte del sistema.</p></div></li>
                    <li><div><strong>Un proyecto se planifica en minutos</strong><p>De un título y una descripción a una lista de pendientes revisada, con responsable y fecha, sin capturarlos uno por uno.</p></div></li>
                    <li><div><strong>Arrancar el sistema cuesta menos</strong><p>Una organización nueva puede empezar a capturar desde el primer día: lo que falte se crea sobre la marcha, sin ir y venir entre catálogos.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>Notificaciones al momento</strong><p>Los avisos aparecen en cuanto ocurre el movimiento, ya no con hasta un minuto de retraso.</p></div></li>
                    <li><div><strong>Movimientos que no avisaban</strong><p>Iniciar, resolver o cancelar un pendiente, agregar una nota y cualquier cambio en una tarea programada ahora sí notifican a quien sigue el registro. Y quien realiza la acción también recibe su aviso si sigue el registro.</p></div></li>
                    <li><div><strong>Avisos que mentían</strong><p>Quitar una etiqueta o un seguidor que ya no estaban confirmaba el cambio y escribía historial aunque no hubiera pasado nada.</p></div></li>
                    <li><div><strong>Borrar un adjunto deja rastro</strong><p>Eliminar un archivo de un pendiente no quedaba registrado en su historial.</p></div></li>
                    <li><div><strong>Altas en blanco que no se podían guardar</strong><p>Las fechas obligatorias del alta de cliente y de usuario nacían vacías, así que un formulario recién abierto daba error al guardar.</p></div></li>
                    <li><div><strong>El modal de pendientes de un proyecto</strong><p>Ya cierra al guardar; antes se quedaba abierto si se había usado el selector de etiquetas o seguidores.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v5.0.0</span>
                    <span class="disc__t">Formularios para tu web, reportes, etiquetas y seguidores</span>
                    <span class="disc__d">29 ago 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>Formularios públicos para tu sitio web</strong><p>Publica un formulario en tu página para que tus clientes reporten sin tener cuenta. Se embebe con un iframe o se comparte como enlace, y lo configuras por completo: qué campos ve el visitante, a quién se le asigna, colores, PIN de acceso opcional y límites contra abuso.</p></div></li>
                    <li><div><strong>Consulta de folio para quien reporta</strong><p>Quien envía un reporte recibe un folio y puede seguir su avance desde el mismo formulario, sin llamar ni escribir. Solo ve el estado y las fechas.</p></div></li>
                    <li><div><strong>Módulo de Reportes</strong><p>Diez reportes agrupados por familias, con periodo, comparación contra el periodo anterior equivalente y exportación a Excel, CSV o PDF. El Dashboard se reenfoca como pantalla operativa del día a día.</p></div></li>
                    <li><div><strong>Etiquetas, con sugerencia por IA</strong><p>Clasifica pendientes, proyectos y tareas programadas con etiquetas de color propias de tu organización. La IA lee la descripción y propone las que aplican, también en el dictado por voz.</p></div></li>
                    <li><div><strong>Seguidores y notificaciones en la app</strong><p>Suma personas a un pendiente, proyecto o tarea programada y se enteran dentro de la plataforma cada vez que algo se mueve, con una campana de avisos siempre a la mano.</p></div></li>
                    <li><div><strong>Expediente completo del proyecto</strong><p>Notas, archivos e historial también a nivel de proyecto, más una pestaña de involucrados con el avance de cada persona del equipo.</p></div></li>
                    <li><div><strong>Más autonomía para el equipo</strong><p>Un miembro del equipo puede tomar un pendiente libre o ponerse como responsable de un proyecto sin esperar a un administrador. Las tareas programadas dejan de ser exclusivas de administradores y ahora llevan su propio historial de cambios.</p></div></li>
                    <li><div><strong>Protección reforzada</strong><p>Los formularios públicos validan los archivos por su contenido real, limitan los envíos por día y controlan desde qué sitios se pueden incrustar. Se endureció además la protección de toda la plataforma frente a incrustaciones no autorizadas.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Impacto en tu día a día</h4>
                    <ul>
                    <li><div><strong>Tus clientes entran solos</strong><p>Los reportes llegan desde tu propia web ya clasificados y con responsable, sin capturarlos a mano ni perder correos.</p></div></li>
                    <li><div><strong>Menos llamadas de seguimiento</strong><p>Quien reporta consulta el avance de su folio por su cuenta, cuando quiera.</p></div></li>
                    <li><div><strong>Decisiones con datos, no con intuición</strong><p>Compara el periodo actual contra el anterior y exporta lo que necesites presentar.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>Aislamiento entre organizaciones</strong><p>El Dashboard ya no podía mostrar información de otra organización bajo ningún escenario.</p></div></li>
                    <li><div><strong>Folios sin duplicados</strong><p>La generación de folios pasó a ser atómica: dos altas simultáneas ya no pueden generar el mismo número.</p></div></li>
                    <li><div><strong>Filtros de listados persistentes</strong><p>Los filtros, el orden y la paginación de un listado ya no se pierden al entrar a un registro y volver.</p></div></li>
                    <li><div><strong>Notas e historial más confiables</strong><p>Se corrigieron fechas y datos que en algunos casos no se guardaban correctamente en el historial y las notas.</p></div></li>
                    <li><div><strong>Historial del proyecto más completo</strong><p>Ahora refleja también la actividad de sus pendientes: asignaciones, avances y cancelaciones.</p></div></li>
                    <li><div><strong>Dictado por voz más estable</strong><p>Cambiar de cliente durante el dictado ya no borra el servicio que ya se había elegido.</p></div></li>
                    <li><div><strong>Campana de notificaciones</strong><p>Se corrigió su alineación y su comportamiento en pantallas de celular.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v4.0.0</span>
                    <span class="disc__t">Permisos por grupos, filtros rápidos y mejoras operativas</span>
                    <span class="disc__d">23 feb 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Resumen de la versión</h4>
                    <ul>
                    <li><div><strong>Permisos por grupos y visibilidad</strong><p>Controla qué equipos ven y gestionan información de otros, con acceso total para administradores.</p></div></li>
                    <li><div><strong>Orden inteligente de pendientes</strong><p>Ahora se prioriza: En proceso, Pendiente y después fecha más reciente.</p></div></li>
                    <li><div><strong>Dashboard: usuarios con más pendientes corregido</strong><p>Se separan correctamente las columnas Pendientes, Total y % resueltos a tiempo.</p></div></li>
                    <li><div><strong>Dashboard: card de pendientes sin asignar</strong><p>Se reemplazó la métrica anterior por una vista directa de pendientes huérfanos.</p></div></li>
                    <li><div><strong>Tablas sin selección de filas</strong><p>Se eliminó el conteo de filas seleccionadas para una interfaz más limpia en listados resumen.</p></div></li>
                    <li><div><strong>Filtros rápidos en listados</strong><p>Pestañas por estado en Pendientes y Proyectos para cambiar de vista con un clic.</p></div></li>
                    <li><div><strong>Hipervínculos a registros relacionados</strong><p>Botones dinámicos para abrir Proyecto y Pendiente anterior en nueva pestaña desde formularios.</p></div></li>
                    <li><div><strong>Nuevas columnas de relación en pendientes</strong><p>Se añadieron Anterior y Proyecto con acceso directo al registro relacionado.</p></div></li>
                    <li><div><strong>Dashboard: gráfica por empresa</strong><p>Se reemplazó la gráfica por servicio para mostrar carga operativa por empresa activa.</p></div></li>
                    <li><div><strong>Fecha de solución protegida</strong><p>Solo se habilita al marcar el pendiente como resuelto y se bloquean cambios inválidos.</p></div></li>
                    <li><div><strong>Solicitante por defecto en proyectos</strong><p>Los pendientes creados desde proyectos se precargan y guardan como "Interno (Proyecto)".</p></div></li>
                    <li><div><strong>Restricción de módulos críticos</strong><p>Usuarios no admin no ven ni pueden acceder a Usuarios, Grupos, Servicios y Categorías.</p></div></li>
                    <li><div><strong>Pegar archivos desde portapapeles</strong><p>En "Subir archivos" ahora puedes arrastrar, seleccionar o pegar.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v3.0.0</span>
                    <span class="disc__t">Calendario, pendientes programados e IA en todo el sistema</span>
                    <span class="disc__d">01 ene 2026</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Mejoras del sistema</h4>
                    <ul>
                    <li><div><strong>Columna "Pendiente" resumida en listados</strong><p>Todas las tablas que muestran pendientes ahora incluyen una columna "Pendiente" que muestra solo los primeros 50 caracteres de la descripción, permitiendo listados más limpios y fáciles de leer.</p></div></li>
                    <li><div><strong>Historial de notas por pendiente</strong><p>Las notas ya no están limitadas a una sola. Cada pendiente cuenta ahora con un historial de notas, incluyendo las notas previas que se migraron como notas de tipo "Solución" dentro del historial.</p></div></li>
                    <li><div><strong>Fecha tentativa de resolución</strong><p>Cada pendiente puede registrar una fecha tentativa de resolución, lo que facilita la planificación y priorización del trabajo.</p></div></li>
                    <li><div><strong>Nuevo módulo de calendario</strong><p>Se agregó un calendario que se basa en la fecha tentativa de resolución y respeta el código de colores de los estados de los pendientes, ofreciendo una vista visual y clara de la carga de trabajo.</p></div></li>
                    <li><div><strong>Mayor flexibilidad en servicios por empresa</strong><p>Se eliminó la obligación de ligar previamente servicios a una empresa. Ahora puedes registrar pendientes aunque la empresa no tenga asignado el servicio en su catálogo, agilizando el alta de casos.</p></div></li>
                    <li><div><strong>Filtros avanzados en módulos clave</strong><p>Se incorporaron filtros avanzados en las tablas principales de los módulos de pendientes, empresas, proyectos y usuarios, reduciendo el tiempo necesario para encontrar información específica.</p></div></li>
                    <li><div><strong>IA integrada en gran parte del sistema</strong><p>El motor de IA ahora participa en más procesos, como en las modales de alta de pendientes sugiriendo automáticamente el servicio más adecuado y apoyando a los módulos que lo permiten.</p></div></li>
                    <li><div><strong>Accesos rápidos desde empresas, proyectos y usuarios</strong><p>En las tablas de empresas, proyectos y usuarios se añadieron botones en la columna de acciones para ir directamente al módulo de pendientes con el registro seleccionado.</p></div></li>
                    <li><div><strong>Previsualización de pendientes</strong><p>Ahora es posible abrir una ventana emergente para ver una previsualización del pendiente desde varias tablas. Desde esta ventana también se puede ir directamente al módulo de pendientes con el registro seleccionado.</p></div></li>
                    <li><div><strong>Asignación de pendientes desde más módulos</strong><p>Ahora puedes asignar pendientes a usuarios y proyectos directamente desde los módulos que permiten alta de pendientes: empresas, usuarios, proyectos y dictado de pendientes.</p></div></li>
                    <li><div><strong>Programador de pendientes recurrentes</strong><p>Se añadió un programador avanzado que permite crear pendientes repetitivos con periodicidad diaria, semanal, mensual, anual o personalizada, definiendo fechas de inicio y fin para controlar el periodo activo.</p></div></li>
                    <li><div><strong>Nuevo motor de dictado por voz mejorado</strong><p>Cuéntale a la IA que solicita el cliente/usuario y el problema que presenta, y la IA reformateará el mensaje para registrar el pendiente de forma clara para el técnico.</p></div></li>
                    <li><div><strong>Listados ordenados por relevancia</strong><p>Se ajustó el comportamiento de los listados iniciales que antes se mostraban de forma uniforme. Ahora se prioriza la información más relevante, dando mayor visibilidad a los pendientes con mayor prioridad y antigüedad, para que la atención del equipo se enfoque primero en lo más crítico.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Corrección de errores (bugs)</h4>
                    <ul>
                    <li><div><strong>Validaciones en altas desde modales</strong><p>Se corrigió un problema que permitía agregar pendientes desde algunas ventanas modales sin datos obligatorios, como la empresa solicitante.</p></div></li>
                    <li><div><strong>Fecha de finalización de proyectos</strong><p>Ahora, cuando se termina el último pendiente de un proyecto, se asigna correctamente la fecha de finalización del mismo.</p></div></li>
                    <li><div><strong>Confirmación al registrar pendientes</strong><p>Se solucionó un bug que impedía mostrar la confirmación de registro de pendientes en los módulos de empresas y usuarios.</p></div></li>
                    <li><div><strong>Botón "Limpiar filtros" en tablas iniciales</strong><p>Se corrigió el botón de limpieza de filtros en las tablas principales de cada módulo, que anteriormente no eliminaba los filtros aplicados.</p></div></li>
                    <li><div><strong>Guardado de proyecto asociado a nuevos pendientes</strong><p>Se corrigió un problema que impedía guardar el proyecto seleccionado al registrar un nuevo pendiente asociado a un proyecto.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Nuevos módulos</h4>
                    <ul>
                    <li><div><strong>Módulo de calendario</strong><p>Nueva vista de calendario para consultar pendientes por fecha de forma visual y ordenada.</p></div></li>
                    <li><div><strong>Módulo de pendientes programados</strong><p>Gestor especializado para administrar pendientes recurrentes y su programación de forma centralizada.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v2.0.0</span>
                    <span class="disc__t">Gestión de Proyectos y Asignación Rápida de Servicios</span>
                    <span class="disc__d">10 nov 2025</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Nuevas funcionalidades para mejorar tu productividad</h4>
                    <ul>
                    <li><div><strong>Organización por proyectos</strong><p>Agrupa tus tareas por proyectos para una mejor organización y seguimiento.</p></div></li>
                    <li><div><strong>Asignación masiva de servicios</strong><p>Asigna todos los servicios a una empresa con un solo clic.</p></div></li>
                    <li><div><strong>Vinculación de tareas con proyectos</strong><p>Relaciona cada pendiente con un proyecto específico para mayor control.</p></div></li>
                    <li><div><strong>Visualización del progreso</strong><p>Barra de progreso animada que muestra el avance de cada proyecto en tiempo real.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Mejoras en la experiencia de usuario</h4>
                    <ul>
                    <li><div><strong>Ventanas mejoradas</strong><p>Corrección en la visualización de tablas en ventanas emergentes.</p></div></li>
                    <li><div><strong>Menú reorganizado</strong><p>Nueva estructura más intuitiva con acceso directo a proyectos.</p></div></li>
                    <li><div><strong>Interfaz mejorada</strong><p>Títulos actualizados en formularios de usuarios y servicios.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Beneficios para tu equipo</h4>
                    <ul>
                    <li><div><strong>Mayor eficiencia</strong><p>Reduce el tiempo en tareas administrativas con la asignación masiva.</p></div></li>
                    <li><div><strong>Mejor visibilidad</strong><p>Control visual del progreso de cada proyecto en tiempo real.</p></div></li>
                    <li><div><strong>Colaboración mejorada</strong><p>Asignación de tareas a proyectos específicos para equipos más enfocados.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v1.0.1</span>
                    <span class="disc__t">Mejoras en la Experiencia de Usuario</span>
                    <span class="disc__d">06 nov 2025</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Correcciones para una experiencia más fluida</h4>
                    <ul>
                    <li><div><strong>Eliminación de procesos duplicados</strong><p>Corregido un problema que causaba inicialización redundante en el formulario de pendientes.</p></div></li>
                    <li><div><strong>Mejora en las notificaciones</strong><p>Las alertas ya no interrumpen tu trabajo mientras escribes.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Impacto en tu día a día</h4>
                    <ul>
                    <li><div><strong>Flujo de trabajo ininterrumpido</strong><p>Mantén el foco en tus tareas sin distracciones innecesarias.</p></div></li>
                    <li><div><strong>Rendimiento optimizado</strong><p>Eliminación de procesos redundantes que afectaban el rendimiento.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Mejoras generales</h4>
                    <ul>
                    <li><div><strong>Actualización del sistema</strong><p>Versión actualizada con las últimas mejoras de estabilidad.</p></div></li>
                    </ul>
                </div>
            </details>
            <details class="disc">
                <summary class="disc__sum">
                    <span class="disc__v">v1.0.0</span>
                    <span class="disc__t">Lanzamiento Oficial de SmartPend</span>
                    <span class="disc__d">oct 2025</span>
                    <?= a_icon('chevron', 'disc__chev') ?>
                </summary>
                <div class="disc__body">
                    <h4 class="disc__group">Presentamos SmartPend: gestión de tareas en un solo sistema</h4>
                    <ul>
                    <li><div><strong>Control completo de pendientes</strong><p>Registra, asigna y da seguimiento a todas tus tareas con estados personalizables.</p></div></li>
                    <li><div><strong>Gestión de clientes</strong><p>Organiza toda la información de tus empresas y clientes.</p></div></li>
                    <li><div><strong>Catálogo de servicios</strong><p>Clasifica y asigna servicios a cada empresa.</p></div></li>
                    <li><div><strong>Administración de usuarios</strong><p>Sistema de autenticación y perfiles de usuario.</p></div></li>
                    <li><div><strong>Dictado inteligente</strong><p>Crea tareas usando tu voz con nuestra tecnología de IA.</p></div></li>
                    <li><div><strong>Gestión de documentos</strong><p>Adjunta y organiza archivos relacionados con cada tarea.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Características destacadas</h4>
                    <ul>
                    <li><div><strong>Diseño responsivo</strong><p>Accede desde cualquier dispositivo con una experiencia optimizada.</p></div></li>
                    </ul>
                    <h4 class="disc__group">Seguridad y confiabilidad</h4>
                    <ul>
                    <li><div><strong>Acceso cifrado</strong><p>Tu sistema se abre por una conexión cifrada.</p></div></li>
                    <li><div><strong>Acceso desde el navegador</strong><p>Entra a tu sistema desde cualquier dispositivo, sin instalar nada.</p></div></li>
                    </ul>
                </div>
            </details>
</div>
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
                    <li><a href="/terminos.html">Términos y condiciones</a></li>
                    <li><a href="/privacidad.html">Aviso de privacidad</a></li>
                    <li><a href="/cookies.html">Cookies</a></li>
                    <li><a href="/encargo-de-datos.html">Tratamiento de datos</a></li>
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
