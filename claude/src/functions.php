<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Madrid');

// ------------------------------------------------------------
// 1. OPCIONES (un solo sitio: generan el HTML y la validación)
// ------------------------------------------------------------
const RUTA_RESERVAS = __DIR__ . '/../datos/reservas.csv';
const CABECERA = ['nombre', 'email', 'pista', 'fecha', 'hora', 'material', 'normas'];

const PISTAS = [
    'padel1'     => 'Pádel 1',
    'padel2'     => 'Pádel 2',
    'tenis'      => 'Tenis',
    'baloncesto' => 'Baloncesto',
];

const HORAS = [
    '16:00' => '16:00',
    '17:00' => '17:00',
    '18:00' => '18:00',
    '19:00' => '19:00',
    '20:00' => '20:00',
    '21:00' => '21:00',
];

const MATERIAL = [
    'raquetas' => 'Raquetas',
    'balon'    => 'Balón',
    'petos'    => 'Petos',
];

// ------------------------------------------------------------
// 2. SALIDA SEGURA
// ------------------------------------------------------------
function dump($var)
{
    echo "<br><pre>";
    var_dump($var);
    echo "</pre>";
}

// Escapa un texto antes de mostrarlo en HTML
function e(string $t): string
{
    return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------
// 3. LECTURA SEGURA DE $_POST y $_GET
// ------------------------------------------------------------
// Texto enviado por POST. Si no llegó, o llegó como array (email[]), devuelve ''
function campoTexto(string $k): string
{
    $v = $_POST[$k] ?? '';
    return is_string($v) ? trim($v) : '';
}

// Lista enviada por POST (name="x[]"). Si no llegó, devuelve []
function campoLista(string $k): array
{
    $v = $_POST[$k] ?? [];
    return is_array($v) ? $v : [];
}

// Texto enviado por GET (URL). Misma idea que campoTexto()
function campoGet(string $k): string
{
    $v = $_GET[$k] ?? '';
    return is_string($v) ? trim($v) : '';
}

// ------------------------------------------------------------
// 4. FECHAS
// ------------------------------------------------------------
// Devuelve un objeto fecha si el texto es una fecha real AAAA-MM-DD; si no, null
function fechaValida(string $texto): ?DateTime
{
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto) !== 1) {
        return null;
    }
    $fecha = DateTime::createFromFormat('!Y-m-d', $texto);
    // Comprueba que no sea algo como 2026-02-31
    if ($fecha === false || $fecha->format('Y-m-d') !== $texto) {
        return null;
    }
    return $fecha;
}

// ------------------------------------------------------------
// 5. FICHERO DE RESERVAS
// ------------------------------------------------------------
// Devuelve todas las reservas. La clave de cada una es su número (1, 2, 3...)
function leerReservas(): array
{
    $reservas = [];

    if (!file_exists(RUTA_RESERVAS)) {
        return $reservas;
    }

    $stream = fopen(RUTA_RESERVAS, 'r');
    if ($stream === false) {
        return $reservas;
    }

    $cabecera = fgetcsv($stream, 0, ',', '"', '\\');
    $numero = 0;
    while (($fila = fgetcsv($stream, 0, ',', '"', '\\')) !== false) {
        if ($cabecera !== false && count($cabecera) === count($fila)) {
            $numero++;
            $reservas[$numero] = array_combine($cabecera, $fila);
        }
    }
    fclose($stream);

    return $reservas;
}

// Añade una reserva al final del fichero y devuelve su número
function guardarReserva(array $datos): int
{
    $numero = count(leerReservas()) + 1;
    $esNuevo = !file_exists(RUTA_RESERVAS) || filesize(RUTA_RESERVAS) === 0;

    $stream = fopen(RUTA_RESERVAS, 'a');
    if ($stream === false) {
        return 0;
    }
    flock($stream, LOCK_EX);
    if ($esNuevo) {
        fputcsv($stream, CABECERA, ',', '"', '\\');
    }
    fputcsv($stream, [
        $datos['nombre'],
        $datos['email'],
        $datos['pista'],
        $datos['fecha'],
        $datos['hora'],
        implode(';', $datos['material']),
        'si',
    ], ',', '"', '\\');
    flock($stream, LOCK_UN);
    fclose($stream);

    return $numero;
}

// ------------------------------------------------------------
// 6. VALIDACIÓN
// ------------------------------------------------------------
// Recibe los datos del formulario y las reservas ya hechas.
// Devuelve un array con un mensaje por cada campo que falle ([] si todo está bien)
function validarReserva(array $datos, array $reservas): array
{
    $errores = [];

    // Nombre: 2 a 60 caracteres, letras (con tildes y ñ), espacios, apóstrofo y guion
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Escribe tu nombre';
    } elseif (preg_match("/^[\p{L} '-]{2,60}$/u", $datos['nombre']) !== 1) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 60 letras (solo letras, espacios, apóstrofo y guion)';
    }

    // Email
    if (filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errores['email'] = 'Escribe un email válido';
    }

    // Pista: tiene que ser una de las del array
    if (!array_key_exists($datos['pista'], PISTAS)) {
        $errores['pista'] = 'Elige una pista de la lista';
    }

    // Fecha: real, desde hoy hasta dentro de 14 días
    $fecha = fechaValida($datos['fecha']);
    if ($fecha === null) {
        $errores['fecha'] = 'Escribe una fecha válida';
    } else {
        $hoy = new DateTime('today');
        $limite = new DateTime('today +14 days');
        if ($fecha < $hoy || $fecha > $limite) {
            $errores['fecha'] = 'La fecha debe estar entre hoy y los próximos 14 días';
        }
    }

    // Hora: una de las franjas
    if (!array_key_exists($datos['hora'], HORAS)) {
        $errores['hora'] = 'Elige una hora de la lista';
    }

    // Material: opcional, pero solo valores permitidos
    foreach ($datos['material'] as $m) {
        if (!is_string($m) || !array_key_exists($m, MATERIAL)) {
            $errores['material'] = 'Hay material que no está en la lista';
            break;
        }
    }

    // Normas: obligatorio aceptarlas
    if (!$datos['normas']) {
        $errores['normas'] = 'Debes aceptar las normas';
    }

    // Pista ya reservada esa fecha y hora (solo si los tres campos son correctos)
    if (!isset($errores['pista']) && !isset($errores['fecha']) && !isset($errores['hora'])) {
        foreach ($reservas as $r) {
            if ($r['pista'] === $datos['pista']
                && $r['fecha'] === $datos['fecha']
                && $r['hora'] === $datos['hora']) {
                $errores['hora'] = 'Esa pista ya está reservada ese día a esa hora';
                break;
            }
        }
    }

    return $errores;
}
