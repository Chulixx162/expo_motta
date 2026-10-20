
<?php
$notas = [
    [4.0, 3.5, 4.5],
    [3.0, 4.0, 3.5],
    [4.5, 4.8, 4.2],
    [2.5, 3.0, 3.5]
];

for ($fila = 0; $fila < 4; $fila++) {
    $suma = 0;

    for ($columna = 0; $columna < 3; $columna++) {
        $suma = $suma + $notas[$fila][$columna];
    }

    $promedio = $suma / 3;

    echo "Estudiante " . ($fila + 1)
         . " - Promedio: "
         . number_format($promedio, 2)
         . "<br>";
}
?>
