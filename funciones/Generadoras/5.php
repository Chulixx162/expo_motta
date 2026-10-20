<?php
function generarTabla(int $base, int $limite) {
    for ($i = 1; $i <= $limite; $i++) {
        yield "$base x $i = " . ($base * $i);
    }
}

$lineasTabla = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $base = intval($_POST['base']);
    $limite = intval($_POST['limite']);
    foreach (generarTabla($base, $limite) as $res) {
        $lineasTabla[] = $res;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Tabla Generadora</title></head>
<body>
    <form method="POST">
        <label>Número base:</label> <input type="number" name="base" required><br><br>
        <label>Hasta qué número:</label> <input type="number" name="limite" required><br><br>
        <button type="submit">Generar Tabla</button>
    </form>
    <?php if (!empty($lineasTabla)): ?>
        <ul>
            <?php foreach ($lineasTabla as $linea): ?>
                <li><?php echo $linea; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>