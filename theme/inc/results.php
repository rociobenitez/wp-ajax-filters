<?php
defined('ABSPATH') || exit;

/**
 * Muestra resultados de Custom Search
 *
 * Query vars:
 * @param object $initial_query
 * @param array $initial_product_ids
 */

 $custom_query = get_query_var( 'initial_query' );
 $initial_ids  = get_query_var( 'initial_product_ids', array() );
 ?>
 <section class="container my-5">
    <div class="row">
 
       <!-- Sidebar -->
       <aside class="col-12 col-lg-3 mb-4 mb-lg-0">
          <?php get_template_part('template-parts/sidebar', 'shop'); ?>
       </aside>
 
       <!-- Contenido principal -->
       <div class="col-12 col-lg-9">
 
          <?php // Cabecera de sección (título + descripción)
          set_query_var( 'section_header_fields', $fields );
          get_template_part( 'template-parts/components/section-header-products' );
 
          // Si no hay productos, mostrar mensaje
          if ( ! $custom_query || ! $custom_query->have_posts() ) {
             get_template_part( 'template-parts/components/not-found', 'products' );
          }
          ?>
 
          <div class="d-flex flex-column justify-content-between">
             <div class="total-products fs15 fw500 py-1 px-3 mb-4 bg-light-subtle">
                <?php
                   /* translators: %d: número de productos */
                   printf(
                      esc_html__( 'Total de productos: %d', THEME_TEXTDOMAIN ),
                      $custom_query->found_posts
                   );
                ?>
             </div>
 
             <!-- IDs iniciales para el JS -->
             <input type="hidden" id="initial-product-ids" value='<?php echo esc_attr( wp_json_encode( $initial_product_ids ) ); ?>'>
             
             <!-- Grid de Productos -->
             <div class="grid-products row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-5 text-center">
                <?php
                while ( $custom_query->have_posts() ) : $custom_query->the_post();
                   $product = wc_get_product( get_the_ID() );
                   set_query_var( 'product_obj', $product );
                   set_query_var( 'product_id', get_the_ID() );
                   get_template_part( 'template-parts/components/card', 'product' );
                endwhile; ?>
             </div>
 
             <?php // Paginación
             $total_pages  = $custom_query->max_num_pages;
             $current_page = max(1, $custom_query->get('paged'));
             if ( $total_pages > 1 ) :
                echo '<div class="pagination-container">';
                echo ajax_pagination( $total_pages, $current_page );
                echo '</div>';
             endif;
 
             wp_reset_postdata();
             ?>
          </div>
       </div>
    </div>
 </section>
 