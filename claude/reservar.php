<?php
declare(strict_types=1);

require __DIR__ . '/src/functions.php';

// 1. Valores iniciales (primera carga del formulario)
$datos = [
    'nombre'   => '',
    'email'    => '',
    'pista'    => '',
    'fecha'    => '',
    'hora'     => '',
    'material' => [],
    'normas'   => false,
];
$errores = [];

// 2. Si se ha enviado el formulario: recoger, validar y guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre'   => campoTexto('nombre'),
        'email'    => campoTexto('email'),
        'pista'    => campoTexto('pista'),
        'fecha'    => campoTexto('fecha'),
        'hora'     => campoTexto('hora'),
        'material' => campoLista('material'),
        'normas'   => isset($_POST['normas']),
    ];

    $errores = validarReserva($datos, leerReservas());

    if ($errores === []) {
        $numero = guardarReserva($datos);
        if ($numero > 0) {
            header('Location: confirmacion.php?n=' . $numero, true, 303);
            exit;
        }
        $errores['general'] = 'No se ha podido guardar la reserva. Inténtalo más tarde';
        http_response_code(500);
    } else {
        http_response_code(422);
    }
}

// Límites del calendario (solo ayuda al usuario; la validación real está en el servidor)
$hoy = date('Y-m-d');
$maximo = date('Y-m-d', strtotime('+14 days'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar pista</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Reservar pista</h1>
    <p><a href="index.php">Volver al listado</a></p>

    <?php if ($errores !== []): ?>
        <p class="errores-resumen">Revisa el formulario: hay errores.</p>
    <?php endif; ?>
    <?php if (isset($errores['general'])): ?>
        <p class="error"><?= e($errores['general']) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Nombre
                <input type="text" name="nombre" value="<?= e($datos['nombre']) ?>" required>
            </label>
            <?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?>
        </p>

        <p>
            <label>Email
                <input type="email" name="email" value="<?= e($datos['email']) ?>" required>
            </label>
            <?php if (isset($errores['email'])): ?><span class="error"><?= e($errores['email']) ?></span><?php endif; ?>
        </p>

        <p>
            <label>Pista
                <select name="pista" required>
                    <option value="">-- Elige --</option>
                    <?php foreach (PISTAS as $valor => $texto): ?>
                        <option value="<?= e($valor) ?>" <?= $datos['pista'] === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errores['pista'])): ?><span class="error"><?= e($errores['pista']) ?></span><?php endif; ?>
        </p>

        <p>
            <label>Fecha
                <input type="date" name="fecha" value="<?= e($datos['fecha']) ?>" min="<?= e($hoy) ?>" max="<?= e($maximo) ?>" required>
            </label>
            <?php if (isset($errores['fecha'])): ?><span class="error"><?= e($errores['fecha']) ?></span><?php endif; ?>
        </p>

        <p>Hora:
            <?php foreach (HORAS as $valor => $texto): ?>
                <label>
                    <input type="radio" name="hora" value="<?= e($valor) ?>" <?= $datos['hora'] === $valor ? 'checked' : '' ?>>
                    <?= e($texto) ?>
                </label>
            <?php endforeach; ?>
            <?php if (isset($errores['hora'])): ?><span class="error"><?= e($errores['hora']) ?></span><?php endif; ?>
        </p>

        <p>Material (opcional):
            <?php foreach (MATERIAL as $valor => $texto): ?>
                <label>
                    <input type="checkbox" name="material[]" value="<?= e($valor) ?>" <?= in_array($valor, $datos['material'], true) ? 'checked' : '' ?>>
                    <?= e($texto) ?>
                </label>
            <?php endforeach; ?>
            <?php if (isset($errores['material'])): ?><span class="error"><?= e($errores['material']) ?></span><?php endif; ?>
        </p>

        <p>
            <label>
                <input type="checkbox" name="normas" value="si" <?= $datos['normas'] ? 'checked' : '' ?>>
                Acepto las normas de uso de las pistas
            </label>
            <?php if (isset($errores['normas'])): ?><span class="error"><?= e($errores['normas']) ?></span><?php endif; ?>
        </p>

        <button type="submit">Reservar</button>
    </form>
</body>
</html>
