<?php
$datos = [15, -3, 20, -8, 42, 0];
$soloPositivos = array_filter($datos, function($n) {
    return $n >= 0;
});
echo implode(", ", $soloPositivos);
?>