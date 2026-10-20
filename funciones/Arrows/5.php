<?php
$convertido = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dolares = floatval($_POST['dolares']);
    $tasaCambio = 4050.0; 
    
    $convertir = fn(float $usd, float $tasa) => $usd * $tasa;
    $convertido = $convertir($dolares, $tasaCambio);
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Conversor</title></head>
<body>
    <form method="POST">
        <label>Cantidad en USD:</label>
        <input type="number" step="0.01" name="dolares" required>
        <button type="submit">Convertir</button>
    </form>
    <?php if ($convertido !== null): ?>
        <p>Valor en moneda local: $<?php echo number_format($convertido, 2); ?></p>
    <?php endif; ?>
</body>
</html>