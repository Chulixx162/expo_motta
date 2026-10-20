<?php
function generarPares() {
    for ($i = 2; $i <= 10; $i += 2) {
        yield $i;
    }
}
foreach (generarPares() as $par) { echo $par . " "; }
?>