<?php
$precios = [100.0, 250.0, 500.0];
$conDescuento = array_map(fn($p) => $p * 0.90, $precios);
echo implode(", ", $conDescuento);
?>