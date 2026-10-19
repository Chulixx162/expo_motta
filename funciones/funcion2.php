<?php
// ============================================
//  FUNCIONES CON PARÁMETROS (sin estilos)
// ============================================

// 1. Área de un rectángulo: recibe DOS datos (base y altura)
function areaRectangulo($base, $altura) {
    return $base * $altura;
}

// 2. Precio con descuento: recibe el precio y el porcentaje
function calcularDescuento($precio, $porcentaje) {
    $descuento = $precio * $porcentaje / 100;
    return $precio - $descuento;
}

// 3. Cuenta regresiva (usa WHILE)
function cuentaRegresiva($n) {
    $texto = "";
    while ($n >= 0) {
        $texto = $texto . $n . " ";
        $n--;
    }
    return $texto;
}

// 4. Contar vocales de una palabra (usa FOR + IF)
function contarVocales($palabra) {
    $total = 0;
    $palabra = strtolower($palabra); // pasamos todo a minúscula

    for ($i = 0; $i < strlen($palabra); $i++) {
        $letra = $palabra[$i]; // la letra que está en la posición $i

        if ($letra == "a" || $letra == "e" || $letra == "i" || $letra == "o" || $letra == "u") {
            $total++;
        }
    }
    return $total;
}

// 5. Convertir grados Celsius a Fahrenheit
function aFahrenheit($celsius) {
    return $celsius * 9 / 5 + 32;
}

// ---------- RECIBIR DATOS ----------
$mostrar = false;

if (isset($_POST["opcion"])) {
    $mostrar = true;
    $opcion = (int) $_POST["opcion"];
    $num1 = (float) $_POST["num1"];
    $num2 = (float) $_POST["num2"];
    $palabra = $_POST["palabra"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Funciones 2</title>
</head>
<body>

<h1>Funciones con parámetros</h1>

<form method="POST">

    Elige una opción:<br>
    <select name="opcion">
        <option value="1">1. Área de un rectángulo (usa número 1 = base y número 2 = altura)</option>
        <option value="2">2. Descuento (usa número 1 = precio y número 2 = porcentaje)</option>
        <option value="3">3. Cuenta regresiva (usa número 1)</option>
        <option value="4">4. Contar vocales (usa la palabra)</option>
        <option value="5">5. Celsius a Fahrenheit (usa número 1)</option>
    </select>
    <br><br>

    Número 1: <input type="number" name="num1" step="any" value="0"><br><br>
    Número 2: <input type="number" name="num2" step="any" value="0"><br><br>
    Palabra: <input type="text" name="palabra"><br><br>

    <button type="submit">Ejecutar</button>
</form>

<?php
if ($mostrar) {
    echo "<hr>";
    echo "<h3>Resultado</h3>";

    if ($opcion == 1) {

        echo "El área es: " . areaRectangulo($num1, $num2);

    } elseif ($opcion == 2) {

        echo "El precio con descuento es: " . calcularDescuento($num1, $num2);

    } elseif ($opcion == 3) {

        if ($num1 > 50) {
            echo "Usa un número de 50 o menos";
        } else {
            echo cuentaRegresiva($num1);
        }

    } elseif ($opcion == 4) {

        echo "La palabra '$palabra' tiene " . contarVocales($palabra) . " vocales";

    } elseif ($opcion == 5) {

        echo "$num1 °C equivalen a " . aFahrenheit($num1) . " °F";
    }
}
?>

</body>
</html>