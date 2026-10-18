<?php

require 'src/functions.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pista = $_POST['pista'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $materiales = $_POST['material'] ?? [];
    $normas = $_POST['normas'] ?? '';


    if (!validarNombre($nombre)) {
        $errores['nombre'] = 'Nombre incorrecto';
    }

    if (!validarEmail($email)) {
        $errores['email'] = 'Email incorrecto';
    }

    if (!validarPista($pista)) {
        $errores['pista'] = 'Pista incorrecta';
    }

    if (!validarFecha($fecha)) {
        $errores['fecha'] = 'Fecha incorrecta';
    }

    if (!validarHora($hora)) {
        $errores['hora'] = 'Hora incorrecta';
    }

    if (!validarMaterial($materiales)) {
        $errores['material'] = 'Material incorrecto';
    }

    if (!validarNormas($normas)) {
        $errores['normas'] = 'Normas incorrectas';
    }

    $rutaFichero = __DIR__ .'/datos/reservas.csv';
    

    if (empty($errores)) {
        $archivo = fopen(__DIR__ . '/datos/reservas.csv', 'a');
        guardarReserva($archivo, $nombre, $email, $pista, $fecha, $hora, $materiales, $normas);
        fclose($archivo);
        header('Location: confirmacion.php');
        exit;

    } else {
        foreach ($errores as $error) {
            echo "<p>" . $error . "</p>";
        }
    }
}

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
<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label>Nombre</label>
        <input type="text" name="nombre"><br>
        <label>email</label>
        <input type="email" name="email"><br>
        <label>pista</label>
        <select name="pista"><br>
            <option value="padel1">Pádel 1</option>
            <option value="padel2">Pádel 2</option>
            <option value="tenis">Tenis</option>
            <option value="baloncesto">Baloncesto</option>
        </select>
        <label>fecha</label>
        <input type="date" name="fecha"><br>
        <label>hora</label>
        <input type="time" name="hora"><br>
        <label>material</label>
        <input type="checkbox" name="material[]" value="balon">Balón
        <input type="checkbox" name="material[]" value="raquetas">Raquetas
        <input type="checkbox" name="material[]" value="petos">Petos <br>
        <label>normas</label>
        <input type="checkbox" name="normas" value="si">Acepto las normas<br>
        <input type="submit" value="Reservar">
</form>
    
</body>
</html>
