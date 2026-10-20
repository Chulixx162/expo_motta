
<?php
$notas = [
    "Matematicas" => 4.5,
    "Ingles" => 3.8,
    "Programacion" => 4.7,
    "Bases de datos" => 4.0
];

$suma = 0;
$cantidad = 0;

foreach ($notas as $asignatura => $nota) {
    echo $asignatura . ": " . $nota . "<br>";

    $suma = $suma + $nota;
    $cantidad++;
}

$promedio = $suma / $cantidad;

echo "<br>Promedio general: "
    . number_format($promedio, 2);
?>
