<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Calculadora de Propinas</title></head>
<body>
    <form method="POST">
        <label>Monto de consumo ($):</label> <input type="number" step="0.01" name="consumo" required><br><br>
        <label>Porcentaje de propina (%):</label> <input type="number" name="porcentaje" value="10" required><br><br>
        <button type="submit">Calcular</button>
    </form>

    <?php
    function calcularPropina(float $consumo, float $porcentajePropina): float {
        return round($consumo * ($porcentajePropina / 100), 2);
    }

    if (isset($_POST['consumo']) && isset($_POST['porcentaje'])) {
        $consumo = floatval($_POST['consumo']);
        $porcentaje = floatval($_POST['porcentaje']);
        
        $propina = calcularPropina($consumo, $porcentaje);
        $total = round($consumo + $propina, 2);

        echo "Propina: $$propina <br>";
        echo "Total a pagar: $$total <br>";
    }
    ?>
</body>
</html>