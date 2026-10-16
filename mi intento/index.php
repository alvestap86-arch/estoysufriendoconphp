<?php
    declare(strict_types= 1);
    
    $rutaFichero = __DIR__ .'/datos/reservas.csv';

    $pistaSeleccionada = $_GET['filtroP'] ?? '';
    $fechaSeleccionada = $_GET['filtroF'] ?? '';

    require __DIR__ . '/src/functions.php';

    $reservas = cargarReservas($rutaFichero);

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
    <?php mostrarReservasFiltradas($reservas,$pistaSeleccionada,$fechaSeleccionada); ?>
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