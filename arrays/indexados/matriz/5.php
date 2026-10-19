
<?php
$matriz = [
    [10, 20, 30],
    [40, 50, 60],
    [70, 80, 90]
];

if (isset($_POST["numero"])) {
    $buscado = (int) $_POST["numero"];
    $encontrado = false;

    for ($fila = 0; $fila < 3; $fila++) {
        for ($columna = 0; $columna < 3; $columna++) {
            if ($matriz[$fila][$columna] == $buscado) {
                $encontrado = true;

                echo "Número encontrado: " . $buscado . "<br>";
                echo "Fila: " . ($fila + 1) . "<br>";
                echo "Columna: " . ($columna + 1) . "<br>";

                break;
            }
        }

        if ($encontrado) {
            break;
        }
    }

    if (!$encontrado) {
        echo "El número no se encuentra en la matriz.";
    }

    echo "<br><br>";
}
?>

<form method="POST">
    <label>Ingrese el número que desea buscar:</label>
    <input type="number" name="numero" required>
    <button type="submit">Buscar</button>
</form>
