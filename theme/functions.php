<?php
defined('ABSPATH') || exit;

/**
 * Incluir archivos de funciones
 * Carga únicamente la lógica relacionada con AJAX + WooCommerce + ACF Pro
 */
$includes = [
    '/inc/config.php',           // Configuración del tema
    '/inc/enqueue.php',          // Scripts y estilos
    '/inc/cpt.php',              // Tipos de contenido personalizados
    '/inc/acf-options.php',      // Configuración de ACF Pro
    '/inc/helpers.php',          // Funciones auxiliares
    '/inc/woocommerce.php',      // Funciones específicas de WooCommerce
    '/inc/custom-query.php',     // Query inicial de IDs basada en ACF
    '/inc/custom-search.php',    // Endpoint AJAX para plantillas “Búsquedas Custom”
    '/inc/ajax-filters.php',     // Maneja filtros dinámicos con AJAX (productos)
    '/inc/ajax-pagination.php'   // Genera el paginador de productos con AJAX
    // otros archivos...
];

foreach ($includes as $file) {
    $path = get_template_directory() . $file;
    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log(sprintf('Error al incluir: %s', $path));
    }
}

/**
 * Mostrar aviso en admin si faltan plugins requeridos
 */
add_action('admin_notices', function () {
    if (! current_user_can('activate_plugins')) {
        return;
    }
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $required = [
        'classic-editor/classic-editor.php'  => 'Classic Editor',
        'woocommerce/woocommerce.php'        => 'WooCommerce',
        'advanced-custom-fields-pro/acf.php' => 'ACF PRO',
    ];

    foreach ($required as $plugin => $name) {
        if (! is_plugin_active($plugin)) {
            printf(
                '<div class="notice notice-error is-dismissible"><p>Instala y activa %s para que el tema funcione correctamente.</p></div>',
                esc_html($name)
            );
        }
    }
});
