<?php
// ============================================
//  SUPERMERCADO MUY SIMPLE (if, for, while)
// ============================================

// Dos listas: el producto 0 va con el precio 0, y así sucesivamente
$productos = ["Arroz", "Leche", "Pan"];
$precios   = [4000, 3000, 2000];

$mostrar = false;

// Si presionaron el botón, calculamos el total
if (isset($_POST["comprar"])) {

    $mostrar = true;
    $total = 0;

    // WHILE: suma lo que cuesta cada producto
    $i = 0;
    while ($i < 3) {
        $total = $total + ($_POST["cant" . $i] * $precios[$i]);
        $i++; // no olvidar: si no, el while nunca termina
    }

    // IF: si gastó 20.000 o más, tiene 10% de descuento
    if ($total >= 20000) {
        $total = $total * 0.90;
        $mensaje = "¡Tienes 10% de descuento! 🎉";
    } else {
        $mensaje = "Compra 20.000 o más para tener descuento";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Supermercado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #a8e063;
            padding: 30px;
        }
        .caja {
            background: white;
            max-width: 400px;
            margin: auto;
            padding: 25px;
            border-radius: 15px;
        }
        h1 { text-align: center; color: #2e7d32; margin-top: 0; }
        input { width: 60px; padding: 5px; font-size: 16px; }
        button {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: #56ab2f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
        }
        .resultado {
            margin-top: 20px;
            padding: 15px;
            background: #f1fbe8;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="caja">
    <h1>🛒 Supermercado</h1>

    <form method="POST">
        <?php
        // FOR: muestra cada producto con su casilla de cantidad
        for ($i = 0; $i < 3; $i++) {
            echo $productos[$i] . " ($" . $precios[$i] . ") ";
            echo "<input type='number' name='cant$i' min='0' value='0'><br><br>";
        }
        ?>
        <button type="submit" name="comprar">Comprar</button>
    </form>

    <?php if ($mostrar) { ?>
        <div class="resultado">
            <?php echo $mensaje; ?><br><br>
            <b>Total a pagar: $<?php echo $total; ?></b>
        </div>
    <?php } ?>
</div>
</body>
</html>