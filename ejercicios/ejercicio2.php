<?php
// ============================================
//  SISTEMA DE NOTAS SIMPLE (if, for, while)
// ============================================

$mostrar = false; // por defecto no mostramos resultado

// 1. Si el formulario fue enviado, calculamos todo
if (isset($_POST["nombre"])) {

    $mostrar = true;
    $nombre = htmlspecialchars($_POST["nombre"]);

    // WHILE: suma las 3 notas
    $suma = 0;
    $i = 1;
    while ($i <= 3) {
        $suma = $suma + (float) $_POST["nota" . $i];
        $i++; // no olvidar: si no, el while nunca termina
    }

    $promedio = round($suma / 3, 2);

    // IF: decidimos el estado según el promedio
    if ($promedio >= 4.5) {
        $estado = "Excelente ⭐";
        $color = "#d4a017";
    } elseif ($promedio >= 3) {
        $estado = "Aprobado ✅";
        $color = "green";
    } else {
        $estado = "Reprobado ❌";
        $color = "crimson";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas simple</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f7971e, #ffd200);
            padding: 30px;
        }
        .caja {
            background: white;
            max-width: 450px;
            margin: auto;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        h1 { text-align: center; color: #b36b00; }
        input {
            width: 100%;
            padding: 10px;
            margin: 6px 0 14px 0;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #f7971e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
        }
        .resultado {
            margin-top: 20px;
            padding: 15px;
            background: #fff8e6;
            border-left: 6px solid #f7971e;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="caja">
    <h1>📚 Notas</h1>

    <!-- FORMULARIO -->
    <form method="POST">
        <label>Nombre del estudiante:</label>
        <input type="text" name="nombre" required>

        <label>Nota 1 (0 a 5):</label>
        <input type="number" name="nota1" min="0" max="5" step="0.1" required>

        <label>Nota 2 (0 a 5):</label>
        <input type="number" name="nota2" min="0" max="5" step="0.1" required>

        <label>Nota 3 (0 a 5):</label>
        <input type="number" name="nota3" min="0" max="5" step="0.1" required>

        <button type="submit">Calcular</button>
    </form>

    <?php if ($mostrar) { ?>
        <div class="resultado">
            <b>Estudiante:</b> <?php echo $nombre; ?><br><br>

            <?php
            // FOR: muestra cada nota con sus estrellas
            for ($i = 1; $i <= 3; $i++) {
                $nota = (float) $_POST["nota" . $i];
                echo "Nota $i: <b>$nota</b> ";

                // FOR dentro del FOR: una estrella por punto
                for ($e = 1; $e <= round($nota); $e++) {
                    echo "⭐";
                }
                echo "<br>";
            }
            ?>

            <br>
            <b>Promedio:</b> <?php echo $promedio; ?><br>
            <b style="color: <?php echo $color; ?>"><?php echo $estado; ?></b>
        </div>
    <?php } ?>
</div>
</body>
</html>