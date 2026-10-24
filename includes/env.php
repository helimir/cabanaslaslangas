<?php
/**
 * includes/env.php
 * -----------------------------------------------------------------
 * Cargador mínimo de variables de entorno (.env) sin dependencias.
 *
 * Reglas:
 *  - El archivo .env nunca se versiona (está en .gitignore).
 *  - Las variables definidas en el entorno real (servidor / CLI) tienen
 *    prioridad sobre las del archivo, lo que permite sobreescribirlas
 *    para pruebas:  DB_NAME=otra_base php -S localhost:8080
 *  - Soporta comentarios (#), export, comillas simples/dobles y
 *    valores con espacios.
 * -----------------------------------------------------------------
 */

if (!function_exists('dsh_env_load')) {

    /**
     * Carga un archivo .env en el proceso actual (una sola vez por ruta).
     */
    function dsh_env_load(string $file): void
    {
        static $loaded = [];

        if (isset($loaded[$file])) {
            return;
        }
        $loaded[$file] = true;

        if (!is_file($file) || !is_readable($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            if ($key === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                continue;
            }

            // Quitar comentario al final SOLO si el valor no está entre comillas.
            $len = strlen($value);
            if ($len >= 2) {
                $first = $value[0];
                $last  = $value[$len - 1];

                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                    if ($first === '"') {
                        $value = str_replace(
                            ['\\n', '\\r', '\\t', '\\"', '\\\\'],
                            ["\n", "\r", "\t", '"', '\\'],
                            $value
                        );
                    }
                } else {
                    $hash = strpos($value, ' #');
                    if ($hash !== false) {
                        $value = rtrim(substr($value, 0, $hash));
                    }
                }
            }

            // El entorno real manda: no sobreescribir variables ya definidas.
            if (getenv($key) !== false) {
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Devuelve el valor de una variable de entorno.
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? false;
        }

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * Devuelve el valor booleano de una variable de entorno.
     */
    function env_bool(string $key, bool $default = false): bool
    {
        $value = env($key);

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on', 'si', 'sí'], true);
    }

    /**
     * Devuelve el valor entero de una variable de entorno.
     */
    function env_int(string $key, int $default = 0): int
    {
        $value = env($key);

        if ($value === null || !is_numeric($value)) {
            return $default;
        }

        return (int) $value;
    }
}
