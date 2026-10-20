<?php
function obtenerMaximo(...$numeros): int {
    if (empty($numeros)) return 0;
    return max($numeros);
}
echo obtenerMaximo(12, 45, 89, 3, 67); 
?>