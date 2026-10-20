
<?php
$estudiantes = [
    "Julio" => [
        "edad" => 17,
        "promedio" => 4.5
    ],
    "Daniela" => [
        "edad" => 18,
        "promedio" => 4.2
    ],
    "Juan" => [
        "edad" => 17,
        "promedio" => 3.8
    ]
];

if (isset($_POST["nombre"])) {
    $nombre = trim($_POST["nombre"]);
    $encontrado = false;

    foreach ($estudiantes as $estudiante => $datos) {
        if (strcasecmp($estudiante, $nombre) == 0) {
            echo "Nombre: " . $estudiante . "<br>";
            echo "Edad: " . $datos["edad"] . "<br>";
            echo "Promedio: " . $datos["promedio"];

            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        echo "Estudiante no encontrado.";
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
    <button type="submit">Consultar</button>
</form>
</body>
</html>
