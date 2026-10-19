
<?php
$numeros = [8, 12, 15, 20, 25, 30, 35, 40];

if (isset($_POST["numero"])) {
    $buscado = (int) $_POST["numero"];
    $encontrado = false;

    for ($i = 0; $i < count($numeros); $i++) {
        if ($numeros[$i] == $buscado) {
            $encontrado = true;
            break;
        }
    }

    if ($encontrado) {
        echo "El número $buscado sí existe en el array.";
    } else {
        echo "El número $buscado no existe en el array.";
    }

    echo "<br><br>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
    <label>Ingrese un número:</label>
    <input type="number" name="numero" required>
    <button type="submit">Buscar</button>
</form>
</body>
</html>
