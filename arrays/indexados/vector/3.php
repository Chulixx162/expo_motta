
<?php
$numeros = [12, 5, 28, 3, 17, 9];

$mayor = $numeros[0];
$menor = $numeros[0];

for ($i = 1; $i < count($numeros); $i++) {
    if ($numeros[$i] > $mayor) {
        $mayor = $numeros[$i];
    }

    if ($numeros[$i] < $menor) {
        $menor = $numeros[$i];
    }
}

echo "Número mayor: " . $mayor . "<br>";
echo "Número menor: " . $menor;
?>
