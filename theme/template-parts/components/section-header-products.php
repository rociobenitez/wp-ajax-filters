<?php
defined( 'ABSPATH' ) || exit;

/**
 * Componente: Encabezado de sección
 */

$fields = get_query_var( 'section_header_fields', array() );

if ( ! empty( $fields['title'] ) ) {
    echo tagTitle( $fields['htag_title'], $fields['title'], 'heading-3', '' );
}
if ( ! empty( $fields['text'] ) ) {
    echo '<div class="term-description">' . wp_kses_post( $fields['text'] ) . '</div>';
}
