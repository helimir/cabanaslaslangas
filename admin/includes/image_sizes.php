<?php
/**
 * admin/includes/image_sizes.php
 * -----------------------------------------------------------------
 * Tamaños de imagen recomendados por sección del sitio.
 *
 * Este archivo es la única fuente de verdad: se usa en los formularios
 * del panel (ayuda junto a cada campo) y en el Centro de Ayuda.
 * -----------------------------------------------------------------
 */

if (!function_exists('image_size_guide')) {

    /**
     * Devuelve el listado de tamaños recomendados.
     *
     * @return array<string, array{label:string,width:int,height:int,ratio:string,where:string,tip:string,autocrop:bool,format:string}>
     */
    function image_size_guide(): array
    {
        return [
            'cabin_cover' => [
                'label'    => 'Portada de cabaña',
                'width'    => 1200,
                'height'   => 900,
                'ratio'    => '4:3',
                'where'    => 'Tarjeta de la cabaña en "Nuestras Cabañas" (index)',
                'tip'      => 'Es la primera imagen que ve el visitante. Usa una foto horizontal con la cabaña completa.',
                'autocrop' => true,
                'format'   => 'JPG o WebP',
            ],
            'cabin_main' => [
                'label'    => 'Imagen principal de cabaña',
                'width'    => 1600,
                'height'   => 900,
                'ratio'    => '16:9',
                'where'    => 'Slider de la página pública de la cabaña',
                'tip'      => 'Se muestra como respaldo si la cabaña aún no tiene fotos en su galería.',
                'autocrop' => true,
                'format'   => 'JPG o WebP',
            ],
            'cabin_gallery' => [
                'label'    => 'Galería de una cabaña',
                'width'    => 1600,
                'height'   => 900,
                'ratio'    => '16:9',
                'where'    => 'Slider de fotos de la página de la cabaña',
                'tip'      => 'Sube varias fotos horizontales del mismo tamaño para que el slider se vea parejo.',
                'autocrop' => true,
                'format'   => 'JPG o WebP',
            ],
            'site_gallery' => [
                'label'    => 'Galería general',
                'width'    => 1200,
                'height'   => 900,
                'ratio'    => '4:3',
                'where'    => 'Sección "Galería" del index',
                'tip'      => 'Fotos de Puerto Natales y del entorno. Se muestran en una grilla de 4 columnas.',
                'autocrop' => false,
                'format'   => 'JPG o WebP',
            ],
            'excursion' => [
                'label'    => 'Turismo / excursiones',
                'width'    => 1200,
                'height'   => 800,
                'ratio'    => '3:2',
                'where'    => 'Sección "Turismo" del index',
                'tip'      => 'Fotos horizontales de los panoramas y excursiones.',
                'autocrop' => false,
                'format'   => 'JPG o WebP',
            ],
            'amenity' => [
                'label'    => 'Equipamiento',
                'width'    => 800,
                'height'   => 800,
                'ratio'    => '1:1',
                'where'    => 'Sección "Equipamiento" y fichas de equipamiento',
                'tip'      => 'Imagen cuadrada. Si no subes imagen puedes usar un ícono de Bootstrap Icons.',
                'autocrop' => false,
                'format'   => 'JPG, PNG o WebP',
            ],
            'slider' => [
                'label'    => 'Slider principal (portada)',
                'width'    => 1920,
                'height'   => 1080,
                'ratio'    => '16:9',
                'where'    => 'Portada del sitio (si el slider está activo)',
                'tip'      => 'Imagen grande y oscura para que el texto blanco se lea bien.',
                'autocrop' => false,
                'format'   => 'JPG o WebP',
            ],
        ];
    }

    /**
     * Devuelve la información de un tamaño por su clave.
     */
    function image_size_info(string $key): ?array
    {
        $guide = image_size_guide();
        return $guide[$key] ?? null;
    }

    /**
     * Texto corto listo para mostrar junto a un campo de subida.
     */
    function image_size_text(string $key, bool $with_tip = false): string
    {
        $info = image_size_info($key);

        if (!$info) {
            return '';
        }

        $text = $info['width'] . ' × ' . $info['height'] . ' px (' . $info['ratio'] . ') · ' . $info['format'];

        if ($info['autocrop']) {
            $text .= ' · se ajusta/recorta automáticamente al mostrarse';
        }

        if ($with_tip) {
            $text .= '. ' . $info['tip'];
        }

        return $text;
    }

    /**
     * Tamaño máximo de archivo permitido (texto).
     */
    function image_max_size_text(): string
    {
        return 'Máximo 5 MB por imagen. Ideal: menos de 500 KB (JPG/WebP).';
    }
}
