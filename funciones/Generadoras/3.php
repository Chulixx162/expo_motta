<?php
function cuentaRegresiva(int $inicio) {
    for ($i = $inicio; $i >= 0; $i--) {
        yield $i;
    }
}
foreach (cuentaRegresiva(4) as $val) { echo $val . "... "; }
?>