<?php
/**
 * index.php — Explorador de la estructura del proyecto
 * Colócalo en la raíz del proyecto. Muestra carpetas y archivos,
 * con un menú lateral de navegación y migas de pan.
 *
 * IMPORTANTE: úsalo solo en desarrollo. No lo dejes en producción,
 * porque expone la estructura de tu proyecto.
 */

declare(strict_types=1);

/* ---------- Configuración ---------- */
const MAX_VER_BYTES = 1048576; // 1 MB máximo para ver código
const EXT_TEXTO = [
    'php', 'html', 'htm', 'css', 'js', 'json', 'xml', 'txt', 'md', 'sql', 'env',
    'ini', 'yml', 'yaml', 'log', 'csv', 'htaccess', 'svg', 'ts', 'tsx', 'jsx',
    'vue', 'sh', 'conf', 'lock', 'gitignore',
];

$ROOT = realpath(__DIR__);
$mostrarOcultos = isset($_GET['ocultos']) && $_GET['ocultos'] === '1';

/* ---------- Utilidades ---------- */
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Resuelve una ruta relativa dentro de la raíz. Devuelve null si se sale de ella. */
function resolver(string $root, string $rel): ?string
{
    $rel = trim(str_replace('\\', '/', $rel), '/');
    $real = realpath($root . ($rel === '' ? '' : '/' . $rel));
    if ($real === false) {
        return null;
    }
    if ($real === $root || strpos($real, $root . DIRECTORY_SEPARATOR) === 0) {
        return $real;
    }
    return null;
}

function relativa(string $root, string $abs): string
{
    return str_replace('\\', '/', ltrim(substr($abs, strlen($root)), '/\\'));
}

function enlace(array $params): string
{
    global $mostrarOcultos;
    if ($mostrarOcultos) {
        $params['ocultos'] = '1';
    }
    $params = array_filter($params, static fn($v) => $v !== '' && $v !== null);
    return '?' . http_build_query($params);
}

function urlArchivo(string $rel): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $base . '/' . implode('/', array_map('rawurlencode', explode('/', $rel)));
}

function tamano(int $bytes): string
{
    $u = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    $n = (float) $bytes;
    while ($n >= 1024 && $i < count($u) - 1) {
        $n /= 1024;
        $i++;
    }
    return ($i === 0 ? (string) $bytes : number_format($n, 1, ',', '.')) . ' ' . $u[$i];
}

/** Devuelve [carpetas, archivos] de un directorio, ordenados por nombre. */
function listar(string $dir, bool $ocultos): array
{
    $carpetas = [];
    $archivos = [];
    $items = @scandir($dir) ?: [];
    foreach ($items as $nombre) {
        if ($nombre === '.' || $nombre === '..') {
            continue;
        }
        if (!$ocultos && $nombre[0] === '.') {
            continue;
        }
        if (is_dir($dir . DIRECTORY_SEPARATOR . $nombre)) {
            $carpetas[] = $nombre;
        } else {
            $archivos[] = $nombre;
        }
    }
    natcasesort($carpetas);
    natcasesort($archivos);
    return [array_values($carpetas), array_values($archivos)];
}

function esTexto(string $nombre): bool
{
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    return in_array($ext, EXT_TEXTO, true) || ($ext === '' && $nombre[0] === '.');
}

/** Menú lateral: solo despliega las ramas que están en la ruta activa. */
function arbol(string $root, string $relDir, string $relActiva, bool $ocultos): void
{
    $abs = $root . ($relDir === '' ? '' : '/' . $relDir);
    [$carpetas] = listar($abs, $ocultos);
    if (!$carpetas) {
        return;
    }
    echo '<ul>';
    foreach ($carpetas as $c) {
        $rel = ($relDir === '' ? '' : $relDir . '/') . $c;
        $enRuta = $relActiva === $rel || strpos($relActiva . '/', $rel . '/') === 0;
        $actual = $relActiva === $rel;
        echo '<li>';
        echo '<a class="nodo' . ($actual ? ' actual' : '') . ($enRuta ? ' abierto' : '') . '" href="' . h(enlace(['dir' => $rel])) . '"'
            . ($actual ? ' aria-current="page"' : '') . '>';
        echo '<span class="chev" aria-hidden="true"></span>' . ICONO_CARPETA . '<span>' . h($c) . '</span></a>';
        if ($enRuta) {
            arbol($root, $rel, $relActiva, $ocultos);
        }
        echo '</li>';
    }
    echo '</ul>';
}

const ICONO_CARPETA = '<svg class="ico carpeta" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6.5A1.5 1.5 0 0 1 4.5 5h4.2c.4 0 .8.2 1.1.5L11 7h8.5A1.5 1.5 0 0 1 21 8.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 17.5z"/></svg>';
const ICONO_ARCHIVO = '<svg class="ico archivo" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v11.5A1.5 1.5 0 0 1 17.5 21h-10A1.5 1.5 0 0 1 6 19.5v-15A1.5 1.5 0 0 1 7.500 3zM14 3v5h5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';

/* ---------- Estado de la petición ---------- */
$relDir = (string) ($_GET['dir'] ?? '');
$absDir = resolver($ROOT, $relDir);
if ($absDir === null || !is_dir($absDir)) {
    $absDir = $ROOT;
}
$relDir = relativa($ROOT, $absDir);

// Modo "ver código"
$verRel = (string) ($_GET['ver'] ?? '');
$verAbs = $verRel !== '' ? resolver($ROOT, $verRel) : null;
$verError = null;
$verContenido = null;
if ($verRel !== '') {
    if ($verAbs === null || !is_file($verAbs)) {
        $verError = 'No se encontró el archivo solicitado.';
    } elseif (!esTexto(basename($verAbs))) {
        $verError = 'Este tipo de archivo no se puede mostrar como texto.';
    } elseif (filesize($verAbs) > MAX_VER_BYTES) {
        $verError = 'El archivo supera 1 MB y no se muestra aquí.';
    } else {
        $verContenido = (string) file_get_contents($verAbs);
        $relDir = relativa($ROOT, dirname($verAbs));
        $absDir = dirname($verAbs);
    }
}

[$carpetas, $archivos] = listar($absDir, $mostrarOcultos);
$nombreProyecto = basename($ROOT);

// Migas de pan
$migas = [['Raíz', '']];
if ($relDir !== '') {
    $acum = '';
    foreach (explode('/', $relDir) as $parte) {
        $acum .= ($acum === '' ? '' : '/') . $parte;
        $migas[] = [$parte, $acum];
    }
}

/** Clase de color según extensión (solo estética). */
function claseExt(string $ext): string
{
    $mapa = [
        'html' => 'html', 'htm' => 'html', 'php' => 'php',
        'css' => 'css', 'js' => 'js', 'ts' => 'js', 'tsx' => 'js', 'jsx' => 'js', 'vue' => 'js',
        'json' => 'data', 'xml' => 'data', 'yml' => 'data', 'yaml' => 'data', 'csv' => 'data', 'sql' => 'data',
        'md' => 'doc', 'txt' => 'doc', 'log' => 'doc',
        'png' => 'img', 'jpg' => 'img', 'jpeg' => 'img', 'gif' => 'img', 'webp' => 'img', 'svg' => 'img', 'ico' => 'img',
    ];
    return 'e-' . ($mapa[$ext] ?? 'otro');
}

function seEjecuta(string $ext): bool
{
    return in_array($ext, ['html', 'htm', 'php', 'svg'], true);
}

/** Línea de código → un <span> por línea (para numerarlas con CSS). */
function lineasCodigo(string $texto): string
{
    $out = '';
    foreach (preg_split('/\r\n|\r|\n/', $texto) as $linea) {
        $out .= '<span class="l">' . h($linea) . "</span>\n";
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="dark">
<title><?= h($nombreProyecto) ?> · Estructura del proyecto</title>
<style>
:root {
    --fondo: #050506;
    --lateral: #0a0a0c;
    --panel: #0e0e11;
    --panel-2: #141418;
    --linea: #1f1f27;
    --linea-fuerte: #2b2b36;
    --tinta: #ececf1;
    --tinta-suave: #8b8b99;
    --acento: #8b7bff;
    --acento-2: #22d3ee;
    --acento-fondo: rgba(139, 123, 255, .12);
    --carpeta: #f5b445;
    --codigo-fondo: #08080a;
    --radio: 10px;
    color-scheme: dark;
}
* { box-sizing: border-box; }
html, body { height: 100%; }
body {
    margin: 0;
    color: var(--tinta);
    background:
        radial-gradient(900px 420px at 85% -8%, rgba(139, 123, 255, .13), transparent 70%),
        radial-gradient(700px 360px at 0% 110%, rgba(34, 211, 238, .06), transparent 70%),
        var(--fondo);
    background-attachment: fixed;
    font: 15px/1.5 "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
}
a { color: inherit; text-decoration: none; }
:focus-visible { outline: 2px solid var(--acento); outline-offset: 2px; border-radius: 6px; }
::selection { background: rgba(139, 123, 255, .35); }
* { scrollbar-width: thin; scrollbar-color: #2b2b36 transparent; }

.app { display: grid; grid-template-columns: 290px 1fr; min-height: 100%; }

/* ---------- Menú lateral ---------- */
.lateral {
    background: linear-gradient(180deg, var(--lateral), #070709);
    border-right: 1px solid var(--linea);
    padding: 20px 14px;
    position: sticky; top: 0; height: 100vh; overflow: auto;
}
.marca {
    display: flex; align-items: center; gap: 12px;
    padding: 6px 8px 18px; margin-bottom: 10px;
    border-bottom: 1px solid var(--linea);
    overflow-wrap: anywhere;
}
.marca .logo {
    flex: none; width: 38px; height: 38px; border-radius: 11px;
    display: grid; place-items: center;
    background: linear-gradient(135deg, var(--acento), var(--acento-2));
    box-shadow: 0 6px 20px -6px rgba(139, 123, 255, .65);
}
.marca .logo svg { width: 20px; height: 20px; fill: none; stroke: #050506; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.marca strong { display: block; font-size: 16px; line-height: 1.2; }
.marca small { display: block; font-weight: 400; color: var(--tinta-suave); font-size: 12.5px; }

.lateral ul { list-style: none; margin: 0; padding: 0; }
.lateral ul ul { margin-left: 14px; padding-left: 8px; border-left: 1px solid var(--linea); }
.nodo {
    display: flex; align-items: center; gap: 7px;
    padding: 6px 9px; border-radius: 8px; margin: 1px 0;
    color: #c9c9d3; overflow-wrap: anywhere;
    transition: background .15s, color .15s, transform .15s;
}
.nodo:hover { background: var(--panel-2); color: #fff; }
.nodo.actual {
    background: var(--acento-fondo); color: #fff; font-weight: 600;
    box-shadow: inset 2px 0 0 var(--acento);
}
.nodo.raiz { font-weight: 600; }
.chev { width: 10px; height: 10px; flex: none; position: relative; }
.chev::before {
    content: ""; position: absolute; inset: 1px 2px 1px 1px;
    border-right: 1.6px solid var(--tinta-suave); border-bottom: 1.6px solid var(--tinta-suave);
    transform: rotate(-45deg); transition: transform .15s;
}
.nodo.abierto > .chev::before { transform: rotate(45deg); inset: 0 2px 2px 1px; }

.ico { width: 18px; height: 18px; flex: none; }
.ico.carpeta { fill: var(--carpeta); filter: drop-shadow(0 0 6px rgba(245, 180, 69, .25)); }
.ico.archivo { color: inherit; }
.fi { display: inline-flex; }

/* ---------- Contenido ---------- */
main { padding: 28px clamp(16px, 4vw, 48px) 56px; min-width: 0; max-width: 1200px; }
.migas {
    display: inline-flex; flex-wrap: wrap; align-items: center; gap: 2px;
    font-size: 13.5px; margin-bottom: 16px;
    padding: 5px 10px; border: 1px solid var(--linea); border-radius: 999px; background: var(--panel);
}
.migas a { color: var(--acento-2); padding: 1px 5px; border-radius: 6px; transition: background .15s; }
.migas a:hover { background: rgba(34, 211, 238, .1); }
.migas .sep { color: #4a4a58; }
.migas .fin { font-weight: 600; padding: 1px 5px; }

h1 { font-size: clamp(24px, 3.4vw, 32px); letter-spacing: -.02em; margin: 0 0 10px; overflow-wrap: anywhere; }
.chips { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 22px; }
.chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 3px 11px; font-size: 13px; color: var(--tinta-suave);
    border: 1px solid var(--linea); border-radius: 999px; background: var(--panel);
}
.chip b { color: var(--tinta); font-weight: 600; }
.chip i { width: 7px; height: 7px; border-radius: 50%; background: var(--acento); }
.chip i.c { background: var(--carpeta); }
.chip i.a { background: var(--acento-2); }

.herramientas { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 14px; }
.campo { position: relative; flex: 1 1 260px; max-width: 380px; }
.campo svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; fill: none; stroke: var(--tinta-suave); stroke-width: 2; stroke-linecap: round; }
.buscar {
    width: 100%; padding: 9px 12px 9px 36px; font: inherit; color: var(--tinta);
    border: 1px solid var(--linea); border-radius: 999px; background: var(--panel);
    transition: border-color .15s, box-shadow .15s;
}
.buscar::placeholder { color: #666674; }
.buscar:focus { outline: none; border-color: var(--acento); box-shadow: 0 0 0 4px rgba(139, 123, 255, .15); }
.opcion { color: var(--tinta-suave); font-size: 14px; }
.opcion a { color: var(--acento-2); }
.opcion a:hover { text-decoration: underline; }

/* Tabla */
.tabla-envoltura {
    background: var(--panel); border: 1px solid var(--linea); border-radius: 14px;
    overflow-x: auto; box-shadow: 0 20px 50px -30px #000;
}
table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 11px 16px; white-space: nowrap; }
th {
    font-size: 11.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
    color: var(--tinta-suave); background: #0b0b0e; border-bottom: 1px solid var(--linea);
}
tbody tr { transition: background .15s; animation: entra .35s ease both; }
tbody tr + tr td { border-top: 1px solid #17171d; }
tbody tr:hover { background: #15151b; }
tbody tr:hover td:first-child { box-shadow: inset 2px 0 0 var(--acento); }
@keyframes entra { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
tbody tr:nth-child(n+2)  { animation-delay: .02s; }
tbody tr:nth-child(n+4)  { animation-delay: .05s; }
tbody tr:nth-child(n+7)  { animation-delay: .08s; }
tbody tr:nth-child(n+11) { animation-delay: .11s; }

td.nombre { white-space: normal; min-width: 220px; overflow-wrap: anywhere; }
td.nombre a { display: inline-flex; align-items: center; gap: 10px; font-weight: 500; }
td.nombre a:hover span:last-child { color: #fff; text-decoration: underline; text-underline-offset: 3px; }
td.num, th.num { text-align: right; }
td.meta { color: var(--tinta-suave); font-size: 14px; }

.tag {
    display: inline-block; padding: 1px 8px; border-radius: 6px;
    font-size: 11.5px; font-weight: 700; letter-spacing: .04em;
    color: var(--c, var(--tinta-suave));
    background: color-mix(in srgb, var(--c, #8b8b99) 14%, transparent);
    border: 1px solid color-mix(in srgb, var(--c, #8b8b99) 28%, transparent);
}
.tag.dir { --c: var(--carpeta); }

/* Colores por tipo de archivo */
.e-html { --c: #ff7a45; } .e-php  { --c: #9a8cff; } .e-css  { --c: #4fb3ff; }
.e-js   { --c: #f5d547; } .e-data { --c: #4ade80; } .e-doc  { --c: #cbd5e1; }
.e-img  { --c: #f472b6; } .e-otro { --c: #8b8b99; }
.fi { color: var(--c, #8b8b99); }

.acciones { display: flex; gap: 6px; align-items: center; }
.btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; font-size: 13px; font-weight: 500; line-height: 1.4;
    color: #d8d8e2; background: var(--panel-2); border: 1px solid var(--linea-fuerte);
    border-radius: 999px; cursor: pointer; font-family: inherit;
    transition: background .15s, border-color .15s, color .15s, transform .15s;
}
.btn:hover { border-color: var(--acento); color: #fff; background: var(--acento-fondo); transform: translateY(-1px); }
.btn.primario { color: #08080a; border-color: transparent; background: linear-gradient(135deg, var(--acento), var(--acento-2)); font-weight: 600; }
.btn.primario:hover { color: #08080a; filter: brightness(1.1); background: linear-gradient(135deg, var(--acento), var(--acento-2)); }
.btn svg { width: 13px; height: 13px; fill: currentColor; }

.vacio { padding: 36px; text-align: center; color: var(--tinta-suave); }
.sin-resultados { display: none; }

/* ---------- Vista de código ---------- */
.aviso { padding: 12px 16px; border: 1px solid #5b2430; border-radius: var(--radio); background: #1a0b10; color: #ffb4c0; }
.codigo-barra { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 12px; }
.codigo-barra .grupo { display: flex; gap: 8px; flex-wrap: wrap; }
.codigo-caja { border: 1px solid var(--linea); border-radius: 14px; overflow: hidden; background: var(--codigo-fondo); box-shadow: 0 20px 50px -30px #000; }
.codigo-cabecera {
    display: flex; align-items: center; gap: 8px; padding: 10px 14px;
    background: #0d0d11; border-bottom: 1px solid var(--linea);
    font-size: 13px; color: var(--tinta-suave);
}
.codigo-cabecera .pt { width: 11px; height: 11px; border-radius: 50%; background: #ff5f57; }
.codigo-cabecera .pt:nth-child(2) { background: #febc2e; }
.codigo-cabecera .pt:nth-child(3) { background: #28c840; }
.codigo-cabecera span.n { margin-left: 8px; }
pre.codigo {
    margin: 0; padding: 14px 0; overflow: auto; max-height: 72vh;
    font: 13px/1.6 ui-monospace, "Cascadia Code", "JetBrains Mono", Consolas, Menlo, monospace;
    tab-size: 4; counter-reset: n; color: #d6d6e0;
}
pre.codigo code { display: block; min-width: max-content; }
.l { display: block; padding: 0 18px 0 0; min-height: 1.6em; counter-increment: n; }
.l::before {
    content: counter(n); display: inline-block; width: 52px; margin-right: 16px; padding-right: 12px;
    text-align: right; color: #4a4a58; border-right: 1px solid #17171d; user-select: none;
}
.l:hover { background: #101015; }
.l:hover::before { color: var(--acento); }

/* Móvil */
@media (max-width: 800px) {
    .app { grid-template-columns: 1fr; }
    .lateral { position: static; height: auto; max-height: 45vh; border-right: 0; border-bottom: 1px solid var(--linea); }
    th.col-fecha, td.col-fecha { display: none; }
}
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
}
</style>
</head>
<body>
<div class="app">

    <!-- Menú de navegación -->
    <nav class="lateral" aria-label="Estructura de carpetas">
        <a class="marca" href="<?= h(enlace([])) ?>">
            <span class="logo" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 7 3 12l5 5M16 7l5 5-5 5M14 4l-4 16"/></svg></span>
            <span>
                <strong><?= h($nombreProyecto) ?></strong>
                <small>Raíz del proyecto</small>
            </span>
        </a>
        <ul>
            <li>
                <a class="nodo raiz<?= $relDir === '' ? ' actual' : '' ?>" href="<?= h(enlace([])) ?>"<?= $relDir === '' ? ' aria-current="page"' : '' ?>>
                    <span class="chev" aria-hidden="true"></span><?= ICONO_CARPETA ?><span>/ (raíz)</span>
                </a>
                <?php arbol($ROOT, '', $relDir, $mostrarOcultos); ?>
            </li>
        </ul>
    </nav>

    <main>
        <!-- Migas de pan -->
        <nav class="migas" aria-label="Ruta actual">
            <?php foreach ($migas as $i => [$texto, $ruta]): ?>
                <?php if ($i > 0): ?><span class="sep" aria-hidden="true">/</span><?php endif; ?>
                <?php if ($i === count($migas) - 1 && $verContenido === null): ?>
                    <span class="fin" aria-current="page"><?= h($texto) ?></span>
                <?php else: ?>
                    <a href="<?= h(enlace(['dir' => $ruta])) ?>"><?= h($texto) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($verContenido !== null): ?>
                <span class="sep" aria-hidden="true">/</span>
                <span class="fin" aria-current="page"><?= h(basename($verRel)) ?></span>
            <?php endif; ?>
        </nav>

        <?php if ($verContenido !== null): ?>

            <h1><?= h(basename($verRel)) ?></h1>
            <div class="chips">
                <span class="chip"><i></i><b><?= h(tamano(filesize($verAbs))) ?></b></span>
                <span class="chip"><i class="a"></i>modificado el <b><?= date('d/m/Y H:i', filemtime($verAbs)) ?></b></span>
            </div>
            <div class="codigo-barra">
                <a class="btn" href="<?= h(enlace(['dir' => $relDir])) ?>">← Volver a la carpeta</a>
                <div class="grupo">
                    <button class="btn" type="button" id="copiar">Copiar código</button>
                    <a class="btn primario" href="<?= h(urlArchivo($verRel)) ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>Abrir en el navegador
                    </a>
                </div>
            </div>
            <div class="codigo-caja">
                <div class="codigo-cabecera"><span class="pt"></span><span class="pt"></span><span class="pt"></span><span class="n"><?= h(basename($verRel)) ?></span></div>
                <pre class="codigo"><code id="codigo"><?= lineasCodigo($verContenido) ?></code></pre>
            </div>

        <?php else: ?>

            <h1><?= $relDir === '' ? h($nombreProyecto) : h(basename($relDir)) ?></h1>
            <div class="chips">
                <span class="chip"><i class="c"></i><b><?= count($carpetas) ?></b> <?= count($carpetas) === 1 ? 'carpeta' : 'carpetas' ?></span>
                <span class="chip"><i class="a"></i><b><?= count($archivos) ?></b> <?= count($archivos) === 1 ? 'archivo' : 'archivos' ?></span>
            </div>

            <?php if ($verError): ?>
                <p class="aviso" role="alert"><?= h($verError) ?></p><br>
            <?php endif; ?>

            <div class="herramientas">
                <div class="campo">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input class="buscar" type="search" id="filtro" placeholder="Filtrar por nombre" aria-label="Filtrar por nombre" autocomplete="off">
                </div>
                <span class="opcion">
                    <?php if ($mostrarOcultos): ?>
                        Mostrando archivos ocultos ·
                        <a href="?<?= h(http_build_query(array_filter(['dir' => $relDir]))) ?>">Ocultarlos</a>
                    <?php else: ?>
                        <a href="?<?= h(http_build_query(array_filter(['dir' => $relDir, 'ocultos' => '1']))) ?>">Mostrar archivos ocultos</a>
                    <?php endif; ?>
                </span>
            </div>

            <div class="tabla-envoltura">
            <?php if (!$carpetas && !$archivos && $relDir === ''): ?>
                <p class="vacio">Esta carpeta está vacía.</p>
            <?php else: ?>
                <table id="tabla">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="num">Tamaño</th>
                            <th class="col-fecha">Modificado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($relDir !== ''): ?>
                        <?php $padre = dirname($relDir); $padre = $padre === '.' ? '' : $padre; ?>
                        <tr class="fijo">
                            <td class="nombre">
                                <a href="<?= h(enlace(['dir' => $padre])) ?>"><?= ICONO_CARPETA ?><span>..  (subir un nivel)</span></a>
                            </td>
                            <td class="meta"><span class="tag dir">CARPETA</span></td><td class="meta num">—</td><td class="meta col-fecha">—</td><td></td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($carpetas as $c):
                        $rel = ($relDir === '' ? '' : $relDir . '/') . $c; ?>
                        <tr data-nombre="<?= h(strtolower($c)) ?>">
                            <td class="nombre">
                                <a href="<?= h(enlace(['dir' => $rel])) ?>"><?= ICONO_CARPETA ?><span><?= h($c) ?></span></a>
                            </td>
                            <td class="meta"><span class="tag dir">CARPETA</span></td>
                            <td class="meta num">—</td>
                            <td class="meta col-fecha"><?= date('d/m/Y H:i', (int) filemtime($absDir . '/' . $c)) ?></td>
                            <td class="acciones"><a class="btn" href="<?= h(enlace(['dir' => $rel])) ?>">Abrir</a></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($archivos as $a):
                        $rel = ($relDir === '' ? '' : $relDir . '/') . $a;
                        $ext = strtolower(pathinfo($a, PATHINFO_EXTENSION));
                        $cls = claseExt($ext); ?>
                        <tr data-nombre="<?= h(strtolower($a)) ?>">
                            <td class="nombre">
                                <a href="<?= h(urlArchivo($rel)) ?>" target="_blank" rel="noopener"><span class="fi <?= $cls ?>"><?= ICONO_ARCHIVO ?></span><span><?= h($a) ?></span></a>
                            </td>
                            <td class="meta"><span class="tag <?= $cls ?>"><?= $ext !== '' ? h(strtoupper($ext)) : 'ARCHIVO' ?></span></td>
                            <td class="meta num"><?= h(tamano((int) filesize($absDir . '/' . $a))) ?></td>
                            <td class="meta col-fecha"><?= date('d/m/Y H:i', (int) filemtime($absDir . '/' . $a)) ?></td>
                            <td class="acciones">
                                <?php if (seEjecuta($ext)): ?>
                                    <a class="btn primario" href="<?= h(urlArchivo($rel)) ?>" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>Ejecutar</a>
                                <?php endif; ?>
                                <?php if (esTexto($a)): ?>
                                    <a class="btn" href="<?= h(enlace(['ver' => $rel])) ?>">Ver código</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="vacio sin-resultados" id="sinResultados">Ningún elemento coincide con el filtro.</p>
                <?php if (!$carpetas && !$archivos): ?>
                    <p class="vacio">Esta carpeta está vacía.</p>
                <?php endif; ?>
            <?php endif; ?>
            </div>

        <?php endif; ?>
    </main>
</div>

<script>
(function () {
    var filtro = document.getElementById('filtro');
    if (filtro) {
        var filas = document.querySelectorAll('#tabla tbody tr[data-nombre]');
        var aviso = document.getElementById('sinResultados');
        filtro.addEventListener('input', function () {
            var q = filtro.value.trim().toLowerCase(), visibles = 0;
            filas.forEach(function (tr) {
                var ok = tr.getAttribute('data-nombre').indexOf(q) !== -1;
                tr.style.display = ok ? '' : 'none';
                if (ok) visibles++;
            });
            if (aviso) aviso.style.display = (visibles === 0 && filas.length) ? 'block' : 'none';
        });
    }

    var copiar = document.getElementById('copiar');
    if (copiar) {
        copiar.addEventListener('click', function () {
            var texto = Array.prototype.map.call(document.querySelectorAll('#codigo .l'), function (l) { return l.textContent; }).join('\n');
            var listo = function () {
                copiar.textContent = '¡Copiado!';
                setTimeout(function () { copiar.textContent = 'Copiar código'; }, 1600);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(texto).then(listo);
            } else {
                var t = document.createElement('textarea');
                t.value = texto; document.body.appendChild(t); t.select();
                document.execCommand('copy'); document.body.removeChild(t); listo();
            }
        });
    }
})();
</script>
</body>
</html>