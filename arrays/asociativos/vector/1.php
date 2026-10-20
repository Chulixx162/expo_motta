
<?php
$persona = [
    "nombre" => "Juan",
    "edad" => 17,
    "ciudad" => "Neiva",
    "profesion" => "Estudiante"
];

foreach ($persona as $clave => $valor) {
    echo ucfirst($clave) . ": " . $valor . "<br>";
}
?>
