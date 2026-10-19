<?php
// ============================================
//  CAJA DE HERRAMIENTAS (funciones + if, for, while)
// ============================================

// ---------- FUNCIONES ----------
// Una función es un bloque de código con nombre que podemos usar muchas veces.
// Recibe datos (parámetros) y devuelve un resultado con "return".

// 1. ¿Es primo? (usa FOR + IF)
function esPrimo($n) {
    if ($n < 2) {
        return false;
    }
    for ($i = 2; $i < $n; $i++) {
        if ($n % $i == 0) {   // si se puede dividir exacto, no es primo
            return false;
        }
    }
    return true;
}

// 2. Factorial (usa WHILE). Ejemplo: 5! = 5 x 4 x 3 x 2 x 1 = 120
function factorial($n) {
    $resultado = 1;
    $i = 1;
    while ($i <= $n) {
        $resultado = $resultado * $i;
        $i++;
    }
    return $resultado;
}

// 3. Tabla de multiplicar (usa FOR + IF)
function tabla($n) {
    $texto = "";
    for ($i = 1; $i <= 10; $i++) {
        $res = $n * $i;
        if ($res % 2 == 0) {
            $texto = $texto . "$n x $i = <b>$res</b> (par)<br>";
        } else {
            $texto = $texto . "$n x $i = $res (impar)<br>";
        }
    }
    return $texto;
}

// 4. Clasificar edad (usa IF / ELSEIF / ELSE)
function clasificarEdad($edad) {
    if ($edad < 12) {
        return "niño o niña 🧒";
    } elseif ($edad < 18) {
        return "adolescente 🧑";
    } elseif ($edad < 60) {
        return "adulto 🧔";
    } else {
        return "adulto mayor 👴";
    }
}

// ---------- RECIBIR DATOS DEL FORMULARIO ----------
$mostrar = false;

if (isset($_POST["opcion"])) {
    $mostrar = true;
    $opcion = (int) $_POST["opcion"];
    $numero = (int) $_POST["numero"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Funciones en PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4776e6, #8e54e9);
            padding: 30px;
        }
        .caja {
            background: white;
            max-width: 450px;
            margin: auto;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        h1 { text-align: center; color: #4a3a8a; margin-top: 0; }
        select, input {
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
            background: #4776e6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
        }
        button:hover { background: #3358c4; }
        .resultado {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0ff;
            border-left: 6px solid #8e54e9;
            border-radius: 8px;
            font-size: 17px;
        }
        .error { color: crimson; font-weight: bold; }
    </style>
</head>
<body>
<div class="caja">
    <h1>🧰 Caja de herramientas</h1>

    <!-- FORMULARIO -->
    <form method="POST">
        <label>Elige una herramienta:</label>
        <select name="opcion">
            <option value="1">1. ¿Es un número primo?</option>
            <option value="2">2. Factorial</option>
            <option value="3">3. Tabla de multiplicar</option>
            <option value="4">4. Clasificar edad</option>
        </select>

        <label>Escribe un número:</label>
        <input type="number" name="numero" min="0" required>

        <button type="submit">Ejecutar</button>
    </form>

    <?php if ($mostrar) { ?>
        <div class="resultado">
            <?php
            // Menú: según la opción, llamamos a la función correspondiente
            if ($opcion == 1) {

                if (esPrimo($numero)) {
                    echo "El $numero <b>SÍ</b> es primo ✅";
                } else {
                    echo "El $numero <b>NO</b> es primo ❌";
                }

            } elseif ($opcion == 2) {

                if ($numero > 15) {
                    echo "<span class='error'>Usa un número de 15 o menos</span>";
                } else {
                    echo "$numero! = <b>" . factorial($numero) . "</b>";
                }

            } elseif ($opcion == 3) {

                echo tabla($numero);

            } elseif ($opcion == 4) {

                echo "Con $numero años eres " . clasificarEdad($numero);
            }
            ?>
        </div>
    <?php } ?>
</div>
</body>
</html>