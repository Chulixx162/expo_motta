<?php
$sufijo = " - Sistema Web";
$formatear = function(string $titulo) use ($sufijo): string {
    return $titulo . $sufijo;
};
echo $formatear("Panel de Control");
?>