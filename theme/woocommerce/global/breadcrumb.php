<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plantilla de breadcrumbs para WooCommerce adaptada al theme.
 * Reemplaza la versión por defecto y usa el mismo diseño que generate_breadcrumbs().
 */

if ( empty( $breadcrumb ) ) {
    return;
}

// Clases y separador SVG
$nav_class  = 'breadcrumbs d-flex fw300 mb-0';
$wrap_open  = '<div class="breadcrumbs-container pt-3 pb-2 border-bottom"><nav aria-label="Breadcrumbs" class="' . esc_attr( $nav_class ) . '">';
$wrap_close = '</nav></div>';
$sep_svg    = '<svg height="24" viewBox="0 -960 960 960" width="24" fill="#767676"><path d="M400-280v-400l200 200-200 200Z"/></svg>';

// Abrir breadcrumb wrapper
echo $wrap_open;

// Recorrer cada crumb
$total = count( $breadcrumb );
foreach ( $breadcrumb as $index => $crumb ) {
    // Título y URL
    list( $label, $url ) = $crumb;
    $is_last = ( $index === $total - 1 );

    // Si tiene URL y no es el último elemento, renderizamos enlace
    if ( ! empty( $url ) && ! $is_last ) {
        // Enlaces ofuscados (javascript:) conservan rel="nofollow"
        if ( strpos( $url, 'javascript:' ) === 0 ) {
            printf(
                '<a href="%s" rel="nofollow" class="fs14 fw500 text-muted">%s</a>',
                $url,
                esc_html( $label )
            );
        } else {
            // URL normal, la saneamos
            printf(
                '<a href="%s">%s</a>',
                esc_url( $url ),
                esc_html( $label )
            );
        }
    } else {
        // Último elemento o sin href: solo texto
        echo '<span class="fs14 fw500 c-dark">' . esc_html( $label ) . '</span>';
    }

    // Añadir separador si no es el último
    if ( ! $is_last ) {
        echo $sep_svg;
    }
}

// Cerrar wrapper
echo $wrap_close;
