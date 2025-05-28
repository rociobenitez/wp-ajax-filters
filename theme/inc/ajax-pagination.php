<?php
defined('ABSPATH') || exit;

/**
 * Genera el HTML para un paginador AJAX.
 *
 * @param int $total_pages  Total de páginas.
 * @param int $current_page Página actual.
 * @return string HTML del paginador.
 */
if (! function_exists('ajax_pagination')) {
    function ajax_pagination(int $total_pages, int $current_page): string
    {
        // Sólo si hay al menos 2 páginas
        if ($total_pages < 2) {
            return '';
        }

        $html = '<ul class="d-flex gap-1 p-0">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $class = ($i === $current_page) ? 'page-numbers current' : 'page-numbers';
            $html .= sprintf(
                '<li><a href="javascript:void(0);" class="%1$s" data-page="%2$d">%2$d</a></li>',
                esc_attr($class),
                $i
            );
        }
        $html .= '</ul>';

        return $html;
    }
}

// Desactivar paginación nativa de WooCommerce.
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
