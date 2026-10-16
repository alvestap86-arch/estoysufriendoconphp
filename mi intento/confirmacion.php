<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <?php if ($reserva === null): ?>
        <h1>Reserva no encontrada</h1>
        <p>No existe ninguna reserva con ese número.</p>
    <?php else: ?>
        <h1>Reserva n.º <?= (int) $n ?> confirmada</h1>
        <ul>
            <li>Nombre: <?= e($reserva['nombre']) ?></li>
            <li>Email: <?= e($reserva['email']) ?></li>
            <li>Pista: <?= e(PISTAS[$reserva['pista']] ?? $reserva['pista']) ?></li>
            <li>Fecha: <?= e($reserva['fecha']) ?></li>
            <li>Hora: <?= e($reserva['hora']) ?></li>
            <li>Material: <?= $nombresMaterial === [] ? 'Ninguno' : e(implode(', ', $nombresMaterial)) ?></li>
        </ul>
    <?php endif; ?>
    <p><a href="index.php">Volver al listado</a> · <a href="reservar.php">Nueva reserva</a></p>
</body>
</html>