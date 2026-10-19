<?php
$estudiantes = [
    "Julio" => 17,
    "Daniela" => 18,
    "Juan" => 17,
    "Karoll" => 18,
    "Jaime" => 19
];

if (isset($_POST["nombre"])) {
    $nombre = trim($_POST["nombre"]);
    $encontrado = false;

    foreach ($estudiantes as $estudiante => $edad) {
        if (strcasecmp($estudiante, $nombre) == 0) {
            echo "Estudiante encontrado: " . $estudiante
                . "<br>Edad: " . $edad;

            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        echo "El estudiante no existe.";
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
        <label>Nombre del estudiante:</label>
        <input type="text" name="nombre" required>
        <button type="submit">Buscar</button>
    </form>

</body>

</html>