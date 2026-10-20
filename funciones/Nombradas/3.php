<?php
function calcularTotalFactura(float $subtotal, float $impuestoPorc = 0.19, float $envio = 10.0): float {
    $totalImpuesto = $subtotal * $impuestoPorc;
    return $subtotal + $totalImpuesto + $envio;
}
echo "Total a pagar: $" . calcularTotalFactura(100.00); 
?>