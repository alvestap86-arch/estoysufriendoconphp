<?php

require __DIR__ . '/src/functions.php';

$rutaFichero = __DIR__ . '/datos/reservas.csv';

$reservas = cargarReservas($rutaFichero);

if (empty($reservas)) {
    header('Location: reservar.php');
    exit;
}

$reserva = $reservas[count($reservas) - 1];

$pistas = [
    'padel1' => 'Pádel 1',
    'padel2' => 'Pádel 2',
    'tenis' => 'Tenis',
    'baloncesto' => 'Baloncesto'
];

$materiales = $reserva['material'] !== ''
    ? explode(',', $reserva['material'])
    : [];

$nombresMaterial = [];

foreach ($materiales as $material) {
    $nombresMaterial[] = match ($material) {
        'balon' => 'Balón',
        'raquetas' => 'Raquetas',
        'petos' => 'Petos',
        default => $material
    };
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <h1>Reserva confirmada</h1>

    <ul>
        <li>Nombre: <?= e($reserva['nombre']) ?></li>
        <li>Email: <?= e($reserva['email']) ?></li>
        <li>Pista: <?= e($pistas[$reserva['pista']] ?? $reserva['pista']) ?></li>
        <li>Fecha: <?= e($reserva['fecha']) ?></li>
        <li>Hora: <?= e($reserva['hora']) ?></li>
        <li>Material:
            <?= empty($nombresMaterial)
                ? 'Ninguno'
                : e(implode(', ', $nombresMaterial)) ?>
        </li>
    </ul>

    <p>
        <a href="index.php">Volver al listado</a>
        ·
        <a href="reservar.php">Nueva reserva</a>
    </p>

</body>
</html>