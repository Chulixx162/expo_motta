<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Buscador</title></head>
<body>
    <form method="POST">
        <label>Frase completa:</label><br><input type="text" name="frase" required style="width:300px;"><br><br>
        <label>Palabra a buscar:</label><br><input type="text" name="palabra" required><br><br>
        <button type="submit">Buscar</button>
    </form>

    <?php
    if (isset($_POST['frase']) && isset($_POST['palabra'])) {
        $frase = $_POST['frase'];
        $palabraBuscada = $_POST['palabra'];
        
        $buscar = function(string $texto, string $busqueda): string {
            return mb_stripos($texto, $busqueda) !== false ? "¡Encontrado!" : "No encontrado";
        };
        
        $resultadoBusqueda = $buscar($frase, $palabraBuscada);
        echo "<p>Resultado: <strong>$resultadoBusqueda</strong></p>";
    }
    ?>
</body>
</html>