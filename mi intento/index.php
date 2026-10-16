<?php
    declare(strict_types= 1);
    
    
    $rutaFichero = __DIR__ .'/datos/reservas.csv';
    function dump($var)
    {
        echo "<br><pre>";
        var_dump($var);
        echo "</pre>";
    }

    $pistaSeleccionada = $_GET['filtroP'] ?? '';
    $fechaSeleccionada = $_GET['filtroF'] ?? '';


    $reservas = [];
    if (($stream = fopen($rutaFichero, 'r')) !== false) {
    // 3. Recorrer línea por línea con fgetcsv
    // Parametros: $stream, tamaño max por línea (0 = sin límite), delimitador (habitualmente ',' o ';')
    $cabecera = fgetcsv($stream, 0, ','); 
    while (($fila = fgetcsv($stream, 0, ',')) !== false) {
        if (count($cabecera) === count($fila)) {
            $reservas[] = array_combine($cabecera, $fila);
        }
    }
    // 4. Cerrar siempre el archivo
    fclose($stream);
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Document</title>
</head>
<style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px; 
        }
</style>
<body>
    <table>
        <?php foreach ($reservas as $fila): ?>
        <?php
            if (
                ($pistaSeleccionada == '' || $fila["pista"] == $pistaSeleccionada) &&
                ($fechaSeleccionada == '' || $fila["fecha"] == $fechaSeleccionada)
            ):?>
            <tr>
                <?php foreach ($fila as $valor): ?>
                    <td><?php echo htmlspecialchars($valor); ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="get">
        <select id="filtrarPista" name="filtroP">
            <option value=''>por defecto</option>
            <option value="padel1">Pádel 1</option>
            <option value="padel2">Pádel 2</option>
            <option value="tenis">Tenis</option>
            <option value="baloncesto">Baloncesto</option>
        </select>
        <input type="date" id="filtrarFecha" name="filtroF">
        <button type="submit1">submit</button>
    </form>
    <a href="reservar.php">hacer una reserva</a>
    
</body>
</html>