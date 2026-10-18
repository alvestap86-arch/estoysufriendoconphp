<?php

    function dump($var)
    {
        echo "<br><pre>";
        var_dump($var);
        echo "</pre>";
    }
    function mostrarReservasFiltradas($reservas, $pistaSeleccionada, $fechaSeleccionada) {
        echo "<table>";

        foreach ($reservas as $fila) {
            if (
                ($pistaSeleccionada == '' || $fila["pista"] == $pistaSeleccionada) &&
                ($fechaSeleccionada == '' || $fila["fecha"] == $fechaSeleccionada)
            ) {
                echo "<tr>";

                foreach ($fila as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }

                echo "</tr>";
            }
        }

        echo "</table>";
    }

    function cargarReservas($rutaFichero) {
        $reservas = [];

        if (($stream = fopen($rutaFichero, 'r')) !== false) {

            $cabecera = fgetcsv($stream, 0, ',');

            while (($fila = fgetcsv($stream, 0, ',')) !== false) {

                if (count($cabecera) === count($fila)) {
                    $reservas[] = array_combine($cabecera, $fila);
                }
            }

            fclose($stream);
        }

        return $reservas;
    }

    function validarNombre($nombre) {
        return preg_match("/^[\p{L} '-]{2,60}$/u", $nombre);
    }

    function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validarPista($pista) {
        return in_array($pista, ["padel1", "padel2", "tenis", "baloncesto"]);
    }

    function validarFecha($fecha) {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)){
            return false;
        };
        $hoy = new DateTime('today');
        $fecha = new DateTime($fecha);
        $fechalim = (clone $hoy)->modify('+14 days');
        return $fecha >= $hoy && $fecha <= $fechalim;
    }

    function validarHora($hora) {
        return preg_match("/^(1[6-9]|20):[0-5][0-9]$/", $hora);
    }

    function validarMaterial($materiales) {
        $permitidos = ["balon", "raquetas", "petos"];

        foreach ($materiales as $material) {
            if (!in_array($material, $permitidos)) {
                return false;
            }
        }

        return true;
    }

    function validarNormas($normas) {
        if ($normas === "si") {
            return true;
        }
        return false;
    }

    function guardarReserva($rutaFichero, $nombre, $email, $pista, $fecha, $hora, $materiales, $normas) {
        fputcsv($rutaFichero, [$nombre, $email, $pista, $fecha, $hora, implode(',', $materiales), $normas]);
    }

    function e($texto) {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }

?>