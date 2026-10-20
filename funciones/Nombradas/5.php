<?php
function calcularPropina(float $consumo, float $porcentajePropina): float {
    $propina = $consumo * ($porcentajePropina / 100);
    return round($propina, 2);
}

$propina = null;
$total = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consumo = floatval($_POST['consumo']);
    $porcentaje = floatval($_POST['porcentaje']);
    
    $propina = calcularPropina($consumo, $porcentaje);
    $total = round($consumo + $propina, 2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Calculadora de Propinas</title></head>
<body>
    <form method="POST">
        <label>Monto de consumo ($):</label> <input type="number" step="0.01" name="consumo" required><br><br>
        <label>Porcentaje de propina (%):</label> <input type="number" name="porcentaje" value="10" required><br><br>
        <button type="submit">Calcular</button>
    </form>
    <?php if ($propina !== null): ?>
        <p>Propina: $<?php echo $propina; ?></p>
        <p>Total a pagar: $<?php echo $total; ?></p>
    <?php endif; ?>
</body>
</html>