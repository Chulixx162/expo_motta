
<?php
$productos = [
    "Teclado" => 80000,
    "Mouse" => 45000,
    "Monitor" => 650000,
    "Audifonos" => 120000,
    "Memoria USB" => 30000
];

$productoMayor = "";
$productoMenor = "";
$primerProducto = true;

foreach ($productos as $producto => $precio) {
    if ($primerProducto) {
        $mayor = $precio;
        $menor = $precio;
        $productoMayor = $producto;
        $productoMenor = $producto;
        $primerProducto = false;
    } else {
        if ($precio > $mayor) {
            $mayor = $precio;
            $productoMayor = $producto;
        }

        if ($precio < $menor) {
            $menor = $precio;
            $productoMenor = $producto;
        }
    }
}

echo "Producto mas caro: " . $productoMayor
    . " - $" . $mayor . "<br>";

echo "Producto mas barato: " . $productoMenor
    . " - $" . $menor;
?>
