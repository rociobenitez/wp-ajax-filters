<?php
defined( 'ABSPATH' ) || exit;

/**
 * Sidebar con filtros dinámicos (checkbox) para los productos
 */

// Taxonomías a mostrar
$taxonomies = array( 'product_brand', 'material', 'genero', 'uso' );

echo '<div class="bg-light-subtle w-100 p-4">';

foreach ( $taxonomies as $tax ) {
    $tax_obj = get_taxonomy( $tax );
    if ( ! $tax_obj ) {
        continue;
    }

    $terms = get_terms( array(
        'taxonomy'   => $tax,
        'hide_empty' => true,
    ) );
    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        continue;
    }

    printf( '<details class="filter-group mb-3"><summary class="fw700 mb-2">%s</summary><ul class="list-unstyled ps-3">',
        esc_html( $tax_obj->labels->name )
    );

    foreach ( $terms as $term ) {
        printf(
            '<li><label><input type="checkbox" class="ajax-filter me-1" data-tax="%1$s" data-term="%2$d"> %3$s</label></li>',
            esc_attr( $tax ),
            absint( $term->term_id ),
            esc_html( $term->name )
        );
    }

    echo '</ul></details>';
}

// Botón limpiar filtros
?>
<button id="clear-filters" class="btn btn-md btn-outline py-1">
  <?php esc_html_e( 'Limpiar filtros', THEME_TEXTDOMAIN ); ?>
</button>
<?php
echo '</div>';
