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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($nombreProyecto) ?> · Estructura del proyecto</title>
<style>
:root {
    --fondo: #eef1f4;
    --panel: #ffffff;
    --lateral: #e4e9ee;
    --tinta: #1b2733;
    --tinta-suave: #5b6b7b;
    --linea: #d3dbe3;
    --acento: #1f5f8b;
    --acento-fondo: #d6e6f2;
    --carpeta: #c0801a;
    --codigo-fondo: #f6f8fa;
    --radio: 6px;
}
@media (prefers-color-scheme: dark) {
    :root {
        --fondo: #151b22;
        --panel: #1c242d;
        --lateral: #19212a;
        --tinta: #e3e9ef;
        --tinta-suave: #93a3b3;
        --linea: #2d3945;
        --acento: #6db3e3;
        --acento-fondo: #22384a;
        --carpeta: #e0a244;
        --codigo-fondo: #151b22;
    }
}
* { box-sizing: border-box; }
html, body { height: 100%; }
body {
    margin: 0;
    background: var(--fondo);
    color: var(--tinta);
    font: 15px/1.5 "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif;
}
a { color: inherit; text-decoration: none; }
:focus-visible { outline: 2px solid var(--acento); outline-offset: 2px; }

.app { display: grid; grid-template-columns: 290px 1fr; min-height: 100%; }

/* Menú lateral */
.lateral {
    background: var(--lateral);
    border-right: 1px solid var(--linea);
    padding: 20px 14px;
    position: sticky; top: 0; height: 100vh; overflow: auto;
}
.marca { display: block; padding: 4px 8px 16px; font-weight: 700; font-size: 16px; overflow-wrap: anywhere; }
.marca small { display: block; font-weight: 400; color: var(--tinta-suave); font-size: 13px; }
.lateral ul { list-style: none; margin: 0; padding: 0; }
.lateral ul ul { margin-left: 14px; padding-left: 8px; border-left: 1px solid var(--linea); }
.nodo {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 8px; border-radius: var(--radio);
    overflow-wrap: anywhere;
}
.nodo:hover { background: var(--acento-fondo); }
.nodo.actual { background: var(--acento-fondo); color: var(--acento); font-weight: 600; }
.nodo.raiz { font-weight: 600; }
.chev { width: 10px; height: 10px; flex: none; position: relative; }
.chev::before {
    content: ""; position: absolute; inset: 1px 2px 1px 1px;
    border-right: 1.6px solid var(--tinta-suave); border-bottom: 1.6px solid var(--tinta-suave);
    transform: rotate(-45deg);
}
.nodo.abierto > .chev::before { transform: rotate(45deg); inset: 0 2px 2px 1px; }

.ico { width: 18px; height: 18px; flex: none; }
.ico.carpeta { fill: var(--carpeta); }
.ico.archivo { color: var(--tinta-suave); }

/* Contenido */
main { padding: 28px clamp(16px, 4vw, 48px) 48px; min-width: 0; }
.migas { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; font-size: 14px; margin-bottom: 6px; }
.migas a { color: var(--acento); padding: 2px 4px; border-radius: 4px; }
.migas a:hover { text-decoration: underline; }
.migas .sep { color: var(--tinta-suave); }
.migas .fin { font-weight: 600; padding: 2px 4px; }
h1 { font-size: 26px; margin: 0 0 4px; overflow-wrap: anywhere; }
.resumen { color: var(--tinta-suave); margin: 0 0 20px; }

.herramientas { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 14px; }
.buscar {
    flex: 1 1 240px; max-width: 360px;
    padding: 8px 12px; font: inherit;
    border: 1px solid var(--linea); border-radius: var(--radio);
    background: var(--panel); color: var(--tinta);
}
.opcion { color: var(--tinta-suave); font-size: 14px; }
.opcion a { color: var(--acento); text-decoration: underline; }

.tabla-envoltura { background: var(--panel); border: 1px solid var(--linea); border-radius: var(--radio); overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 10px 14px; white-space: nowrap; }
th { font-size: 13px; font-weight: 600; color: var(--tinta-suave); border-bottom: 1px solid var(--linea); }
tbody tr + tr td { border-top: 1px solid var(--linea); }
tbody tr:hover { background: var(--acento-fondo); }
td.nombre { white-space: normal; min-width: 220px; overflow-wrap: anywhere; }
td.nombre a, td.nombre .item { display: inline-flex; align-items: center; gap: 10px; }
td.nombre a:hover span { text-decoration: underline; }
td.num, th.num { text-align: right; }
td.meta { color: var(--tinta-suave); font-size: 14px; }
.acciones a { color: var(--acento); font-size: 14px; }
.acciones a:hover { text-decoration: underline; }
.vacio { padding: 28px; text-align: center; color: var(--tinta-suave); }
.sin-resultados { display: none; }

/* Vista de código */
.aviso { padding: 12px 16px; border: 1px solid var(--linea); border-radius: var(--radio); background: var(--panel); }
.codigo-barra { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 10px; }
.codigo-barra a { color: var(--acento); }
.codigo-barra a:hover { text-decoration: underline; }
pre.codigo {
    margin: 0; padding: 16px; overflow: auto; max-height: 75vh;
    background: var(--codigo-fondo); border: 1px solid var(--linea); border-radius: var(--radio);
    font: 13px/1.55 ui-monospace, "Cascadia Code", Consolas, Menlo, monospace;
    tab-size: 4;
}

/* Móvil */
@media (max-width: 800px) {
    .app { grid-template-columns: 1fr; }
    .lateral { position: static; height: auto; max-height: 45vh; border-right: 0; border-bottom: 1px solid var(--linea); }
    th.col-fecha, td.col-fecha { display: none; }
}
</style>
</head>
<body>
<div class="app">

    <!-- Menú de navegación -->
    <nav class="lateral" aria-label="Estructura de carpetas">
        <a class="marca" href="<?= h(enlace([])) ?>">
            <?= h($nombreProyecto) ?>
            <small>Raíz del proyecto</small>
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
            <p class="resumen"><?= h(tamano(filesize($verAbs))) ?> · modificado el <?= date('d/m/Y H:i', filemtime($verAbs)) ?></p>
            <div class="codigo-barra">
                <a href="<?= h(enlace(['dir' => $relDir])) ?>">Volver a la carpeta</a>
                <a href="<?= h(urlArchivo($verRel)) ?>" target="_blank" rel="noopener">Abrir archivo en el navegador</a>
            </div>
            <pre class="codigo"><code><?= h($verContenido) ?></code></pre>

        <?php else: ?>

            <h1><?= $relDir === '' ? h($nombreProyecto) : h(basename($relDir)) ?></h1>
            <p class="resumen">
                <?= count($carpetas) ?> <?= count($carpetas) === 1 ? 'carpeta' : 'carpetas' ?>
                y <?= count($archivos) ?> <?= count($archivos) === 1 ? 'archivo' : 'archivos' ?>
            </p>

            <?php if ($verError): ?>
                <p class="aviso" role="alert"><?= h($verError) ?></p><br>
            <?php endif; ?>

            <div class="herramientas">
                <input class="buscar" type="search" id="filtro" placeholder="Filtrar por nombre" aria-label="Filtrar por nombre" autocomplete="off">
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
                            <td class="meta">Carpeta</td><td class="meta num">—</td><td class="meta col-fecha">—</td><td></td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($carpetas as $c):
                        $rel = ($relDir === '' ? '' : $relDir . '/') . $c; ?>
                        <tr data-nombre="<?= h(strtolower($c)) ?>">
                            <td class="nombre">
                                <a href="<?= h(enlace(['dir' => $rel])) ?>"><?= ICONO_CARPETA ?><span><?= h($c) ?></span></a>
                            </td>
                            <td class="meta">Carpeta</td>
                            <td class="meta num">—</td>
                            <td class="meta col-fecha"><?= date('d/m/Y H:i', (int) filemtime($absDir . '/' . $c)) ?></td>
                            <td class="acciones"><a href="<?= h(enlace(['dir' => $rel])) ?>">Abrir</a></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($archivos as $a):
                        $rel = ($relDir === '' ? '' : $relDir . '/') . $a;
                        $ext = strtolower(pathinfo($a, PATHINFO_EXTENSION)); ?>
                        <tr data-nombre="<?= h(strtolower($a)) ?>">
                            <td class="nombre">
                                <a href="<?= h(urlArchivo($rel)) ?>" target="_blank" rel="noopener"><?= ICONO_ARCHIVO ?><span><?= h($a) ?></span></a>
                            </td>
                            <td class="meta"><?= $ext !== '' ? h(strtoupper($ext)) : 'Archivo' ?></td>
                            <td class="meta num"><?= h(tamano((int) filesize($absDir . '/' . $a))) ?></td>
                            <td class="meta col-fecha"><?= date('d/m/Y H:i', (int) filemtime($absDir . '/' . $a)) ?></td>
                            <td class="acciones">
                                <?php if (esTexto($a)): ?>
                                    <a href="<?= h(enlace(['ver' => $rel])) ?>">Ver código</a>
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
    if (!filtro) return;
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
})();
</script>
</body>
</html>