<?php
defined('ABSPATH') || exit;

/**
 * Enqueue y localize script para Custom Search
 */
add_action('wp_enqueue_scripts', 'ct_enqueue_custom_search_scripts');
function ct_enqueue_custom_search_scripts()
{
    if ( ! is_page_template( 'page-custom-search.php' ) ) {
        return;
    }

    wp_enqueue_script(
        'custom-ajax-request',
        get_template_directory_uri() . '/assets/js/ajax-filters.js',
        array(),
        null,
        true
    );

    wp_localize_script('custom-ajax-request', 'ajaxobject', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('custom_nonce'),
    ));
}

/**
 * Construye una WP_Query para obtener los productos filtrados según los valores ACF
 *
 * @param array $acf_filters Arreglo de filtros obtenidos de ACF.
 * @param int   $paged       Número de página actual (para paginación).
 * @param array $initial_product_ids Opcional. Conjunto de IDs de productos de la query inicial.
 * @return WP_Query Objeto con la query de productos.
 */
function build_filtered_query( $acf_filters = [], $paged = 1, $initial_product_ids = [] )
{
    // Mapeo de taxonomías: clave taxonomía => nombre campo ACF
    $map_taxonomies = [
        'product_cat'   => 'product_cat',
        'product_brand' => 'marca',
        'material'      => 'material',
        'genero'        => 'genero',
        'uso'           => 'uso'
    ];

    // Recorrer el mapeo y crear un array de filtros taxonómicos si hay valores en ACF
    $tax_filters = [];
    foreach ( $map_taxonomies as $taxonomy => $field_key ) {
        if ( !empty( $acf_filters[$field_key] ) ) {
            $terms = (array) $acf_filters[$field_key];
            $tax_filters[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator' => 'IN',
            ];
        }
    }

    // Construir los argumentos para la query
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 20,  // Límite por página
        'paged'          => $paged,
        'no_found_rows'  => false,
        'tax_query'      => !empty($tax_filters) ? array_merge(['relation' => 'AND'], $tax_filters) : []
    ];

    // Si se especifica un subconjunto inicial de IDs, limitar la consulta a esos productos
    if ( !empty( $initial_product_ids ) ) {
        $args['post__in'] = $initial_product_ids;
        $args['orderby']  = 'post__in';
    }

    return new WP_Query($args);
}

/**
 * Extraer el conjunto inicial de IDs.
 *
 * @param array $acf_filters Arreglo de filtros obtenidos de ACF.
 * @return WP_Query Objeto con la query sin paginación.
 */
function build_initial_query_page_custom($acf_filters = [])
{
    // Mapeo de taxonomías (igual que en build_filtered_query)
    $map_taxonomies = [
        'product_cat'   => 'product_cat',
        'product_brand' => 'marca',
        'material'      => 'material',
        'genero'        => 'genero',
        'uso'           => 'uso'
    ];

    $tax_filters = [];
    foreach ($map_taxonomies as $taxonomy => $field_key) {
        if (!empty($acf_filters[$field_key])) {
            $terms = (array)$acf_filters[$field_key];
            $tax_filters[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator' => 'IN',
            ];
        }
    }

    // Construir los argumentos para WP_Query sin paginación
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => -1, // todos los productos
        'no_found_rows'  => false,
        'tax_query'      => !empty($tax_filters) ? array_merge(['relation' => 'AND'], $tax_filters) : []
    ];

    return new WP_Query($args);
}

/**
 * Endpoint AJAX para la Paginación
 */
add_action('wp_ajax_filtrar_productos', 'ct_pagination_products_callback');
add_action('wp_ajax_nopriv_filtrar_productos', 'ct_pagination_products_callback');
function ct_pagination_products_callback()
{
    // Verificar el nonce para seguridad
    check_ajax_referer('custom_nonce', 'nonce');

    // Obtener el número de página enviado
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

    // Recuperar los IDs iniciales enviados desde el frontend
    $initial_ids = isset($_POST['initial_ids']) ? json_decode(wp_unslash($_POST['initial_ids']), true) : array();

    // Definir número de productos por página
    $per_page = 20;
    $offset = ($paged - 1) * $per_page;
    $paged_ids = array_slice($initial_ids, $offset, $per_page);

    // Construir la query para obtener los productos
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'post__in'       => $paged_ids,
        'orderby'        => 'post__in',
    ];
    $query = new WP_Query($args);

    // Generar el HTML de productos
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            set_query_var( 'product_obj', $product );
            set_query_var( 'product_id', get_the_ID() );
            get_template_part( 'template-parts/components/card', 'product' );
        }
    } else {
        get_template_part( 'template-parts/components/not-found', 'products' );
    }
    $products_html = ob_get_clean();

    // Calcular el total de productos y páginas basado en el array inicial
    $total = count($initial_ids);
    $total_pages = ceil($total / $per_page);

    // Generar el HTML del paginador
    $pagination_html = ajax_pagination( $total_pages, $paged );

    $response = [
        'html'       => $products_html,
        'pagination' => $pagination_html,
        'count'      => $total,
        'no_results' => ( $total === 0 ),
    ];

    wp_reset_postdata();
    wp_send_json($response);
}
