<?php
$htmlGenerado = "";
function construirElemento(string $etiqueta, ...$textos): string {
    $contenido = implode(" ", $textos);
    return "<{$etiqueta}>{$contenido}</{$etiqueta}>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tag = $_POST['etiqueta'];
    $texto1 = $_POST['texto1'];
    $texto2 = $_POST['texto2'];
    $htmlGenerado = construirElemento($tag, $texto1, $texto2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Constructor HTML</title></head>
<body>
    <form method="POST">
        <label>Etiqueta HTML (h1, p, div):</label><br><input type="text" name="etiqueta" required><br><br>
        <label>Texto parte 1:</label><br><input type="text" name="texto1" required><br><br>
        <label>Texto parte 2:</label><br><input type="text" name="texto2"><br><br>
        <button type="submit">Generar</button>
    </form>
    <?php if ($htmlGenerado): ?>
        <h3>Resultado renderizado:</h3>
        <div><?php echo $htmlGenerado; ?></div>
        <p>Código fuente: <code><?php echo htmlspecialchars($htmlGenerado); ?></code></p>
    <?php endif; ?>
</body>
</html>