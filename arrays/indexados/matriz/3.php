
<?php
$matriz = [
    [2, 4, 6],
    [1, 3, 5],
    [7, 8, 9]
];

$suma = 0;

for ($fila = 0; $fila < 3; $fila++) {
    for ($columna = 0; $columna < 3; $columna++) {
        $suma = $suma + $matriz[$fila][$columna];
    }
}

echo "La suma total es: " . $suma;
?>
