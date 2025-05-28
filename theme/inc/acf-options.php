<?php
defined('ABSPATH') || exit;

/**
 * Si ACF Pro está activo, añadir una página de Opciones Globales
 */
if ( function_exists( 'acf_add_options_page' ) ) {
    // Página principal de Opciones del Theme
    acf_add_options_page(array(
        'page_title'    => 'Opciones Generales',
        'menu_title'    => 'Opciones',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false,
    ));
}

/**
 * Sincronización de campos ACF mediante acf-json
 */
add_filter('acf/settings/save_json', 'custom_acf_json_save_point');
function custom_acf_json_save_point($path)
{
    // Guarda los archivos JSON en la carpeta acf-json del theme
    return get_template_directory() . '/acf-json';
}

add_filter('acf/settings/load_json', 'custom_acf_json_load_point');
function custom_acf_json_load_point($paths)
{
    // Elimina la ruta por defecto
    unset($paths[0]);
    // Agrega la ruta personalizada
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
}
