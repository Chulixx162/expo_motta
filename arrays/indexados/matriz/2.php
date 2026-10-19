
<?php
$matriz = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

for ($fila = 0; $fila < 3; $fila++) {
    $suma = 0;

    for ($columna = 0; $columna < 3; $columna++) {
        $suma = $suma + $matriz[$fila][$columna];
    }

    echo "Suma de la fila " . ($fila + 1)
         . ": " . $suma . "<br>";
}
?>
