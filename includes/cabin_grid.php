<?php
/**
 * includes/cabin_grid.php
 * -----------------------------------------------------------------
 * Cálculo de la grilla de "Nuestras Cabañas" del index.
 *
 * La cantidad de tarjetas por fila se adapta a cuántas cabañas
 * activas existan, evitando que quede una tarjeta "huérfana" sola
 * en una fila. Usa la grilla de 12 columnas de Bootstrap.
 *
 * Ejemplos:
 *   1 cabaña  -> 1 por fila (centrada, tamaño normal)
 *   2 cabañas -> 2 por fila
 *   3 cabañas -> 3 por fila  (situación actual)
 *   4 cabañas -> 4 por fila
 *   5 cabañas -> 3 + 2
 *   6 cabañas -> 3 + 3
 *   7 cabañas -> 4 + 3
 *   8 cabañas -> 4 + 4
 *   9 cabañas -> 3 + 3 + 3
 *  12 cabañas -> 4 + 4 + 4
 * -----------------------------------------------------------------
 */

if (!function_exists('cabin_grid_columns')) {

    /**
     * Devuelve cuántas tarjetas se muestran por fila.
     */
    function cabin_grid_columns(int $count): int
    {
        if ($count <= 0) {
            return 0;
        }

        // Con 1 a 4 cabañas, todas caben en una sola fila.
        if ($count <= 4) {
            return $count;
        }

        // 5 cabañas: 3 + 2 (mejor que 4 + 1).
        if ($count === 5) {
            return 3;
        }

        // Múltiplos de 4: filas completas de 4.
        if ($count % 4 === 0) {
            return 4;
        }

        // Múltiplos de 3: filas completas de 3.
        if ($count % 3 === 0) {
            return 3;
        }

        // Resto (7, 10, 11...): filas de 4.
        return 4;
    }

    /**
     * Devuelve las clases Bootstrap para cada tarjeta.
     */
    function cabin_grid_class(int $columns): string
    {
        switch ($columns) {
            case 1:
                // Una sola cabaña: tamaño normal y centrada.
                return 'col-12 col-md-8 col-lg-4 mx-auto';
            case 2:
                return 'col-12 col-md-6 col-lg-6';
            case 3:
                return 'col-12 col-md-6 col-lg-4';
            case 4:
                return 'col-12 col-md-6 col-lg-3';
            case 5:
                return 'col-12 col-md-6 col-lg-5';
            case 6:
                return 'col-12 col-md-4 col-lg-2';
            default:
                return 'col-12 col-md-6 col-lg-4';
        }
    }
}
