<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Conversor</title></head>
<body>
    <form method="POST">
        <label>Cantidad en USD:</label>
        <input type="number" step="0.01" name="dolares" required>
        <button type="submit">Convertir</button>
    </form>

    <?php
    if ($_POST) {
        $dolares = floatval($_POST['dolares']);
        $tasaCambio = 4050.0; 
        
        $convertir = fn(float $usd, float $tasa): float => $usd * $tasa;
        $convertido = $convertir($dolares, $tasaCambio);

        echo "<p>Valor en moneda local: $" . number_format($convertido, 2) . "</p>";
    }
    ?>
</body>
</html>