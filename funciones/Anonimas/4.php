<?php
$tasaImpuesto = 0.16; 

$calcularConImpuesto = function(float $precio) use ($tasaImpuesto): string {
    $total = $precio + ($precio * $tasaImpuesto);
    return "Total con impuesto: $" . number_format($total, 2);
};

echo $calcularConImpuesto(150.00); 
?>