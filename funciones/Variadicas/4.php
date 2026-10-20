<?php
function calcularPromedio(...$notas): float {
    if (empty($notas)) return 0.0;
    return array_sum($notas) / count($notas);
}
echo "Promedio: " . calcularPromedio(4.5, 3.8, 5.0, 4.0);
?>