<?php
/**
 * Template para archivos de productos (Shop, categorías…)
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

get_header();

/// Obtener campos de ACF según contexto (shop o categoría)
if ( is_product_category() || is_tax( 'product_cat' ) ) {
    $term   = get_queried_object(); 
    $fields = get_fields( 'term_' . absint( $term->term_id ) ) ?: array();
} else if ( is_shop() ) {
    $shop_id = wc_get_page_id( 'shop' );
    $fields  = get_fields( $shop_id ) ?: array();
} else {
    $fields = get_fields();
}
?>

<div class="container">
    <?php
    // Breadcrumbs (sobrescrito en template-parts/woocommerce/global/breadcrumb.php)
    do_action('woocommerce_before_main_content');
    ?>
</div>

<div class="woocommerce-shop my-5">
    <div class="row">

        <!-- Sidebar -->
        <aside class="col-12 col-lg-3 mb-4 mb-lg-0">
            <?php get_template_part('template-parts/sidebar', 'shop'); ?>
        </aside>

        <!-- Contenido principal -->
        <div class="col-12 col-lg-9">

            <?php // Cabecera de sección (título + descripción)
            get_template_part( 'template-parts/components/section-header-products' );
            ?>

            <?php if ( woocommerce_product_loop() ) : ?>

                <!-- Filtros y orden -->
                <div class="shop-filters d-flex flex-column justify-content-end align-items-end mb-4">
                    <?php do_action( 'woocommerce_before_shop_loop' ); ?>
                </div>

                <!-- Grid de productos -->
                <div class="grid-products row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5 text-center">
                    <?php
                    while ( have_posts() ) : the_post();
                        $product = wc_get_product( get_the_ID() );
                        set_query_var( 'product_obj', $product );
                        set_query_var( 'product_id', get_the_ID() );
                        get_template_part( 'template-parts/components/card', 'product' );
                    endwhile;
                    ?>
                </div>

                <?php
                // Paginador AJAX (reemplaza al estándar de WooCommerce)
                $total_pages  = (int) wc_get_loop_prop( 'total_pages' );
                $current_page = (int) wc_get_loop_prop( 'current_page' );
                if ( $total_pages > 1 ) : ?>
                    <div class="pagination-container mb-5">
                        <?php echo ajax_pagination( $total_pages, $current_page ); ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <?php
                // Componente reutilizable de “sin productos”
                get_template_part( 'template-parts/components/not-found', 'products-soon' );
                ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php  
// Bloques flexibles ACF
if ( ! empty( $fields['flexible_content'] ) && is_array( $fields['flexible_content'] ) ) {
    require_once get_template_directory() . '/template-parts/load-flexible-blocks.php';
    load_flexible_blocks($fields['flexible_content']);
}

get_footer();
