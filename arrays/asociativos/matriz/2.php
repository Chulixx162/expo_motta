
<?php
$estudiantes = [
    "Julio" => [
        "Matematicas" => 4.5,
        "Ingles" => 4.0,
        "Programacion" => 4.8
    ],
    "Daniela" => [
        "Matematicas" => 3.8,
        "Ingles" => 4.2,
        "Programacion" => 4.5
    ],
    "Juan" => [
        "Matematicas" => 3.5,
        "Ingles" => 3.8,
        "Programacion" => 4.0
    ]
];

foreach ($estudiantes as $nombre => $notas) {
    $suma = 0;
    $cantidad = 0;

    foreach ($notas as $asignatura => $nota) {
        $suma = $suma + $nota;
        $cantidad++;
    }

    $promedio = $suma / $cantidad;

    echo $nombre . ": "
        . number_format($promedio, 2)
        . "<br>";
}
?>
