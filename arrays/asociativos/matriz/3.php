
<?php
$ventas = [
    "Carlos" => [
        "Teclados" => 150000,
        "Mouse" => 90000,
        "Monitores" => 500000
    ],
    "Laura" => [
        "Teclados" => 200000,
        "Mouse" => 120000,
        "Monitores" => 600000
    ],
    "Pedro" => [
        "Teclados" => 100000,
        "Mouse" => 80000,
        "Monitores" => 450000
    ]
];

$totalGeneral = 0;

foreach ($ventas as $vendedor => $productos) {
    $totalVendedor = 0;

    foreach ($productos as $producto => $valor) {
        $totalVendedor = $totalVendedor + $valor;
    }

    echo $vendedor . ": $"
        . $totalVendedor . "<br>";

    $totalGeneral = $totalGeneral + $totalVendedor;
}

echo "<br>Total general: $" . $totalGeneral;
?>
