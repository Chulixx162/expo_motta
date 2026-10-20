<?php
$esAdulto = fn(int $edad) => $edad >= 18;
echo $esAdulto(20) ? "Es mayor" : "Es menor";
?>