<?php
function generarSecuencia() {
    yield 1;
    yield 2;
    yield 3;
}
foreach (generarSecuencia() as $v) { echo $v . " "; }
?>