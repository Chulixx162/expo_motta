<?php
function esPar(int $numero): bool {
    return $numero % 2 === 0;
}
echo esPar(4) ? "Es par" : "Es impar"; 
?>