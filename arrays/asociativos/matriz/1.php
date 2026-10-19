
<?php
$estudiantes = [
    "estudiante1" => [
        "nombre" => "Julio",
        "edad" => 17,
        "grado" => "11"
    ],
    "estudiante2" => [
        "nombre" => "Daniela",
        "edad" => 18,
        "grado" => "11"
    ],
    "estudiante3" => [
        "nombre" => "Juan",
        "edad" => 17,
        "grado" => "11"
    ]
];

foreach ($estudiantes as $codigo => $datos) {
    echo "<strong>" . $codigo . "</strong><br>";

    foreach ($datos as $clave => $valor) {
        echo ucfirst($clave) . ": " . $valor . "<br>";
    }

    echo "<br>";
}
?>
