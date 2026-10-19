
<?php
$inventario = [
    "Arroz" => 12,
    "Leche" => 3,
    "Huevos" => 24,
    "Pan" => 4,
    "Aceite" => 8
];

foreach ($inventario as $producto => $cantidad) {
    echo $producto . ": " . $cantidad . " unidades";

    if ($cantidad < 5) {
        echo " - Stock bajo";
    }

    echo "<br>";
}
?>
