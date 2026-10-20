<?php
$notas = [2.8, 4.5, 3.2, 1.9, 3.8];
$notaMinima = 3.0;
$bono = 0.5; 


$evaluarNota = fn(float $nota): string => 
    ($nota + $bono >= $notaMinima) 
        ? "Aprobado (" . ($nota + $bono) . ")" 
        : "Reprobado (" . ($nota + $bono) . ")";


$boletin = array_map($evaluarNota, $notas);


echo implode(" | ", $boletin);
?>