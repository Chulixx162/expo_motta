<?php
function calcularEnvio(string $zona): float {
    
    $zonaLimpia = strtolower(trim($zona));
    
    switch ($zonaLimpia) {
        case 'norte':
            return 10.00;
        case 'sur':
            return 15.00;
        case 'centro':
            return 5.00;        
        default:
            return 25.00; 
    }
}

echo "Costo de envío: $" . calcularEnvio("Norte"); 