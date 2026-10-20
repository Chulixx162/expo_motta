<?php
function calcularPropina(float $consumo, float $porcentajePropina): array {
    $propina = $consumo * ($porcentajePropina / 100);
    $total = $consumo + $propina;
    return ["propina" => round($propina, 2), "total" => round($total, 2)];
}

$resultado = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consumo = floatval($_POST['consumo']);
    $porcentaje = floatval($_POST['porcentaje']);
    $resultado = calcularPropina($consumo, $porcentaje);
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
    <?php if ($resultado): ?>
        <p>Propina: $<?php echo $resultado['propina']; ?></p>
        <p>Total a pagar: $<?php echo $resultado['total']; ?></p>
    <?php endif; ?>
</body>
</html>