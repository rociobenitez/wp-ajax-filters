<?php
defined( 'ABSPATH' ) || exit;

/**
 * Soporte para WooCommerce
 */
add_action( 'after_setup_theme', 'custom_theme_add_woocommerce_support' );
function custom_theme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

/**
 * Desactivar assets de WooCommerce fuera de sus páginas
 */
add_action( 'wp_enqueue_scripts', 'custom_theme_dequeue_woocommerce_assets', 99 );
function custom_theme_dequeue_woocommerce_assets() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    if ( ! is_woocommerce()
      && ! is_cart()
      && ! is_checkout()
      && ! is_account_page()
    ) {
        // Estilos a remover
        foreach ( array(
            'woocommerce-general',
            'woocommerce-layout',
            'woocommerce-smallscreen',
            'woocommerce-inline',
        ) as $handle ) {
            wp_dequeue_style( $handle );
        }
        // Scripts a remover
        foreach ( array(
            'wc-cart-fragments',
            'woocommerce',
            'wc-add-to-cart',
            'jquery-blockui',
            'jquery-placeholder',
        ) as $handle ) {
            wp_dequeue_script( $handle );
        }
    }
}

/**
 * Eliminar la pestaña "Valoraciones" de la ficha de producto
 */
add_filter('woocommerce_product_tabs', 'custom_theme_remove_reviews_tab', 98);
function custom_theme_remove_reviews_tab($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}

/**
 * Añadir icono de carrito al menú “primary”
 */
add_filter( 'wp_nav_menu_items', 'custom_theme_add_cart_icon_to_menu', 10, 2 );
function custom_theme_add_cart_icon_to_menu( $items, $args ) {
    if ( 'primary' === $args->theme_location && class_exists( 'WooCommerce' ) ) {
        $count    = WC()->cart->get_cart_contents_count();
        $cart_url = esc_url( wc_get_cart_url() );
        ob_start(); ?>
        <li class="menu-item menu-item-cart mx-2">
          <a class="cart-contents" href="<?php echo $cart_url; ?>" 
             title="<?php esc_attr_e( 'Ver tu carrito', 'woocommerce' ); ?>">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
              <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5…"/>
            </svg>
            <span class="cart-count"><?php echo esc_html( $count ); ?></span>
          </a>
        </li>
        <?php
        $items .= ob_get_clean();
    }
    return $items;
}

/**
 * Contador del carrito por AJAX
 */
add_filter('woocommerce_add_to_cart_fragments', 'custom_theme_update_cart_count_fragment');
function custom_theme_update_cart_count_fragment( $fragments ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return $fragments;
    }
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
}

/**
 * Filtrar breadcrumbs de WooCommerce: ofuscar enlaces según meta 'show_link'
 */
add_filter( 'woocommerce_get_breadcrumb', 'yourtheme_filter_wc_breadcrumbs', 20, 2 );
function yourtheme_filter_wc_breadcrumbs( $crumbs, $breadcrumb ) {
    static $show_link_cache = [];

    foreach ( $crumbs as $i => & $crumb ) {
        if ( 0 === $i || empty( $crumb[1] ) ) {
            continue;
        }
        $term = yourtheme_get_term_from_url( $crumb[1] );
        if ( ! $term ) {
            continue;
        }
        $term_id = $term->term_id;
        if ( ! isset( $show_link_cache[ $term_id ] ) ) {
            $show_link_cache[ $term_id ] = get_term_meta( $term_id, 'show_link', true );
        }
        if ( '0' === $show_link_cache[ $term_id ] ) {
            $encoded = base64_encode( $crumb[1] );
            $crumb[1] = "javascript:void(0);\" onclick=\"window.location.href=atob('{$encoded}');";
        }
    }
    return $crumbs;
}

function yourtheme_get_term_from_url( $url ) {
    $path     = untrailingslashit( parse_url( $url, PHP_URL_PATH ) );
    $segments = explode( '/', ltrim( $path, '/' ) );
    $slug     = array_pop( $segments );
    return get_term_by( 'slug', $slug, 'product_cat' );
}

/**
 * Filtrar enlaces meta de product_cat en ficha de producto
 */
add_filter( 'term_links-product_cat', 'yourtheme_filter_term_links', 10, 1 );
function yourtheme_filter_term_links( $links ) {
    static $show_link_cache = [];

    $terms = get_the_terms( get_the_ID(), 'product_cat' ) ?: [];
    foreach ( $terms as $index => $term ) {
        $term_id = $term->term_id;
        if ( ! isset( $show_link_cache[ $term_id ] ) ) {
            $show_link_cache[ $term_id ] = get_term_meta( $term_id, 'show_link', true );
        }
        if ( '0' === $show_link_cache[ $term_id ] ) {
            $url     = get_term_link( $term );
            if ( is_wp_error( $url ) ) {
                continue;
            }
            $encoded = base64_encode( $url );
            $links[ $index ] = sprintf(
                '<a href="javascript:void(0);" onclick="window.location.href=atob(\'%1$s\');" rel="nofollow">%2$s</a>',
                esc_js( $encoded ),
                esc_html( $term->name )
            );
        }
    }
    return $links;
}
