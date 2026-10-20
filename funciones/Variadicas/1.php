<?php
function sumarTodo(...$nums): int {
    return array_sum($nums);
}
echo sumarTodo(2, 4, 6, 8); 
?>