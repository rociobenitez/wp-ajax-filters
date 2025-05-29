<?php
defined('ABSPATH') || exit;

/**
 * Enqueue JS y localize script para filtros dinámicos con AJAX
 */
add_action('wp_enqueue_scripts', 'ct_enqueue_ajax_filters');
function ct_enqueue_ajax_filters()
{
  // Sólo en shop, categorías o plantilla custom.
  if ( ! ( is_shop() || is_product_category() || is_page_template('page-busqueda-custom.php') ) ) return;

  // Encolado JS filtros AJAX
  wp_enqueue_script(
    'custom-ajax',
    get_template_directory_uri() . '/assets/js/ajax-filters.js',
    array(),
    _THEME_VERSION,
    true
  );

  // Taxonomía inicial si es categoría
  $initial_taxonomy = '';
  $initial_term_id  = 0;
  if ( is_product_category() ) {
    $queried = get_queried_object();
    if ( $queried && ! is_wp_error( $queried ) ) {
      $initial_taxonomy = $queried->taxonomy;  // product_cat
      $initial_term_id  = absint($queried->term_id);
    }
  }

  // IDs iniciales para Custom Search
  $initial_ids = [];
  if (is_page_template('page-custom-search.php')) {
    $fields      = get_fields();                             // campos ACF
    $initial_q   = build_initial_query_page_custom( $fields ); // todos los posts que cumplen filtros ACF
    $initial_ids = wp_list_pluck( $initial_q->posts, 'ID' );   // sus IDs
  }

  wp_localize_script('custom-ajax-filters', 'ajaxFiltersData', [
    'ajaxurl'           => admin_url('admin-ajax.php'),
    'nonce'             => wp_create_nonce('ajax_filter'),
    'initialTaxonomy'   => sanitize_key($initial_taxonomy),
    'initialTermId'     => $initial_term_id,
    'initialProductIDs' => $initial_ids,
  ]);
}

/**
 * AJAX callback: filtrar productos.
 */
add_action('wp_ajax_ajax_filter_products', 'ajax_filter_products');
add_action('wp_ajax_nopriv_ajax_filter_products', 'ajax_filter_products');
function ajax_filter_products()
{
  check_ajax_referer('ajax_filter', 'nonce');

  // Parámetros
  $page     = max(1, intval($_POST['page'] ?? 1));
  $per_page = 20;
  $filters  = json_decode( stripslashes($_POST['filters'] ?? '{}'), true );
  $initial_ids = !empty( $_POST['initial_ids'] )
    ? array_map( 'absint', (array) json_decode( wp_unslash( $_POST['initial_ids'] ), true ) )
    : array();

  // Montar tax_query
  $tax_query = ['relation' => 'AND'];
  if ( is_array( $filters ) ) {
    foreach ( $filters as $tax => $terms ) {
      if ( is_array($terms) && ! empty($terms) ) {
        $tax_query[] = [
          'taxonomy' => sanitize_key($tax),
          'field'    => 'term_id',
          'terms'    => array_map('absint', $terms),
          'operator' => 'IN',
        ];
      }
    }
  }

  // WP_Query args
  $args = [
    'post_type'      => 'product',
    'posts_per_page' => $per_page,
    'paged'          => $page
  ];

  // Si hay IDs iniciales (Custom Search)
  if ( !empty( $initial_ids ) ) {
    $args['post__in'] = $initial_ids;
    $args['orderby']  = 'post__in';
  }

  if (count($tax_query) > 1) {
    $args['tax_query'] = $tax_query;
  }

  $q = new WP_Query($args);

  // Renderizar productos
  ob_start();
    if ( $q->have_posts() ) {
      while ( $q->have_posts() ) {
        $q->the_post();
        $product = wc_get_product( get_the_ID() );
        set_query_var( 'product_obj', $product );
        set_query_var( 'product_id', get_the_ID() );
        get_template_part( 'template-parts/components/card', 'product' );
      }
    } else {
       get_template_part( 'template-parts/components/not-found', 'products' );
    }
  $html = ob_get_clean();
  wp_reset_postdata();

  // Generar Paginación
  $pagination = ajax_pagination( $q->max_num_pages, $page );

  wp_send_json([
    'html'       => $html,
    'pagination' => $pagination,
    'count'      => $q->found_posts,
    'no_results' => ( $q->found_posts === 0 ),
  ]);
}
