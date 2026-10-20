
<?php
$notas = [4.0, 3.5, 4.2, 3.8, 4.5];
$suma = 0;

for ($i = 0; $i < count($notas); $i++) {
    $suma = $suma + $notas[$i];
}

$promedio = $suma / count($notas);

echo "Suma de las notas: " . $suma . "<br>";
echo "Promedio: " . number_format($promedio, 2);
?>
