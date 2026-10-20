<?php
function unirFrases(string $separador, ...$palabras): string {
    return implode($separador, $palabras);
}
echo unirFrases(" - ", "PHP", "Es", "Genial");
?>