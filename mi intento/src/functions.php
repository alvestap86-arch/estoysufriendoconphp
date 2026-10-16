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

?>