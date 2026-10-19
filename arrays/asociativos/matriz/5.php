<?php
$productos = [
    "P001" => [
        "nombre" => "Teclado",
        "precio" => 80000,
        "stock" => 10
    ],
    "P002" => [
        "nombre" => "Mouse",
        "precio" => 45000,
        "stock" => 3
    ],
    "P003" => [
        "nombre" => "Monitor",
        "precio" => 650000,
        "stock" => 7
    ],
    "P004" => [
        "nombre" => "Audifonos",
        "precio" => 120000,
        "stock" => 0
    ]
];

if (isset($_POST["codigo"])) {
    $codigo = strtoupper(trim($_POST["codigo"]));

    if (isset($productos[$codigo])) {
        $producto = $productos[$codigo];

        echo "Código: " . $codigo . "<br>";
        echo "Nombre: " . $producto["nombre"] . "<br>";
        echo "Precio: $" . $producto["precio"] . "<br>";
        echo "Stock: " . $producto["stock"] . "<br>";

        if ($producto["stock"] > 0) {
            echo "Estado: Disponible";
        } else {
            echo "Estado: Agotado";
        }
    } else {
        echo "El código ingresado no existe.";
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
        <label>Código del producto:</label>
        <input type="text" name="codigo" required>
        <button type="submit">Buscar producto</button>
    </form>

</body>

</html>