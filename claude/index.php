<?php
declare(strict_types=1);

require __DIR__ . '/src/functions.php';

// 1. Leer los filtros de la URL (si no llegan o no son válidos, no se filtra)
$filtroPista = campoGet('pista');
if (!array_key_exists($filtroPista, PISTAS)) {
    $filtroPista = '';
}

$filtroFecha = campoGet('fecha');
if (fechaValida($filtroFecha) === null) {
    $filtroFecha = '';
}

// 2. Leer las reservas y quedarnos con las que cumplen los filtros
$reservas = [];
foreach (leerReservas() as $numero => $r) {
    if ($filtroPista !== '' && $r['pista'] !== $filtroPista) {
        continue;
    }
    if ($filtroFecha !== '' && $r['fecha'] !== $filtroFecha) {
        continue;
    }
    $reservas[$numero] = $r;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de pistas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Reservas de pistas</h1>

    <form method="get">
        <label>Pista
            <select name="pista">
                <option value="">Todas</option>
                <?php foreach (PISTAS as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>" <?= $filtroPista === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Fecha
            <input type="date" name="fecha" value="<?= e($filtroFecha) ?>">
        </label>
        <button type="submit">Filtrar</button>
        <a href="index.php">Quitar filtros</a>
    </form>

    <p><a href="reservar.php">Hacer una reserva</a></p>

    <?php if ($reservas === []): ?>
        <p>No hay reservas</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Nº</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Pista</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Material</th>
            </tr>
            <?php foreach ($reservas as $numero => $r): ?>
                <?php
                $nombresMaterial = [];
                foreach (explode(';', $r['material']) as $m) {
                    if ($m !== '') {
                        $nombresMaterial[] = MATERIAL[$m] ?? $m;
                    }
                }
                ?>
                <tr>
                    <td><a href="confirmacion.php?n=<?= $numero ?>"><?= $numero ?></a></td>
                    <td><?= e($r['nombre']) ?></td>
                    <td><?= e($r['email']) ?></td>
                    <td><?= e(PISTAS[$r['pista']] ?? $r['pista']) ?></td>
                    <td><?= e($r['fecha']) ?></td>
                    <td><?= e($r['hora']) ?></td>
                    <td><?= e(implode(', ', $nombresMaterial)) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
