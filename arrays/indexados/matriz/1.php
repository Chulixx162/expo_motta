
<?php
$matriz = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

for ($fila = 0; $fila < 3; $fila++) {
    for ($columna = 0; $columna < 3; $columna++) {
        echo $matriz[$fila][$columna] . " ";
    }

    echo "<br>";
}
?>
