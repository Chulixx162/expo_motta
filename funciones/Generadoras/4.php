<?php
function filtrarLineas(array $lineas, string $filtro) {
    foreach ($lineas as $linea) {
        if (str_contains($linea, $filtro)) {
            yield $linea;
        }
    }
}
$logs = ["INFO: Conexión OK", "ERROR: Base de datos caida", "INFO: Login exitoso"];
foreach (filtrarLineas($logs, "ERROR") as $error) {
    echo $error;
}
?>