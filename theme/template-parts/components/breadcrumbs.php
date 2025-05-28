<?php
defined( 'ABSPATH' ) || exit;

/**
 * Genera los breadcrumbs dinámicos para WooCommerce y plantillas.
 */
function generate_breadcrumbs()
{
    // Detectar contextos
    $is_custom_search  = is_page_template( 'page-custom-search.php' );
    $is_product_cat    = is_product_category();
    $is_single_product = is_product();

    // Texto y clases base
    $home_text  = esc_html__('Inicio', THEME_TEXTDOMAIN);
    $nav_class  = 'breadcrumbs d-flex c-white fw300 mb-0';
    $link_class = 'c-white';

    // Solo añadimos 'c-white' al nav si NO estamos en custom-search, cat o producto
    $nav_class = $nav_base . ( ! ( $is_custom_search || $is_product_cat || $is_single_product ) ? ' c-white' : '' );

    // Para los enlaces, idem
    $link_class = ! ( $is_custom_search || $is_product_cat || $is_single_product )
        ? 'c-white'
        : 'text-muted';

    // Separadores SVG (blanco / gris)
    $sep_white = '<svg height="24" viewBox="0 -960 960 960" width="24" fill="#fff"><path d="M400-280v-400l200 200-200 200Z"/></svg>';
    $sep_gray  = '<svg height="24" viewBox="0 -960 960 960" width="24" fill="#767676"><path d="M400-280v-400l200 200-200 200Z"/></svg>';

    // Abrir contenedor y nav
    echo '<div class="breadcrumbs-container pt-3 pb-2 border-bottom">';
    echo '<nav aria-label="Breadcrumbs" class="' . esc_attr( $nav_class ) . '">';

    // Enlace al inicio
    echo '<a href="' . esc_url( home_url() ) . '" class="' . esc_attr( $link_class ) . '">' . esc_html( $home_text ) . '</a>';

    // Plantilla Custom Search
    if ( $is_custom_search ) {
        echo $sep_gray;

        // Campos ACF
        $fields = get_fields();
        $cats   = !empty( $fields['product_cat'] ) ? (array) $fields['product_cat'] : [];

        // Categoría con más productos
        if ( $cats ) {
            $best = null;
            foreach ( $cats as $cat_id ) {
                $term = get_term( absint( $cat_id ), 'product_cat' );
                if ( ! is_wp_error( $term ) && ( ! $best || $term->count > $best->count ) ) {
                    $best = $term;
                }
            }
            if ( $best ) {
                // Mostrar u ofuscar enlace según meta
                $url  = get_term_link( $best );
                $show = get_term_meta( $best->term_id, 'show_link', true );
                if ( $show === '0' ) {
                    $encHref = base64_encode( $url );
                    $href    = "javascript:void(0);\" onclick=\"window.location.href=atob('{$encHref}')";
                    $rel     = ' rel="nofollow"';
                } else {
                    $href = esc_url( $url );
                    $rel  = '';
                }
                echo '<a href="' . $href . '" class="fs14 fw500 text-muted"' . $rel . '>'
                    . esc_html( $best->name )
                    . '</a>' . $sep_gray;
            }
        }
        // Título de la página actual (sin enlace)
        echo '<span class="fs14 fw500 c-dark">' . esc_html( get_the_title() ) . '</span>';
        echo '</nav></div>';
        return; // No se ejecuta el resto del código
    }

    // Resto de casos: separador blanco tras "Inicio"
    echo $sep_white;

    // Single product
    if ( is_product() ) {
        $terms = wc_get_product_terms( get_the_ID(), 'product_cat' );
        $term      = ! empty( $terms ) ? $terms[0] : null;
        if ( $term ) {
            $ancestors = array_reverse( get_ancestors( $term->term_id, 'product_cat' ) );
            foreach ($ancestors as $ancestor_id) {
                $parent_term = get_term( $ancestor_id, 'product_cat' );
                echo '<a href="' . esc_url( get_term_link( $parent_term ) ) . '" class="' . esc_attr( $link_class ) . '">'
                    . esc_html( $parent_term->name ) . '</a>' . $sep_gray;
            }
            echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="' . esc_attr( $link_class ) . '">'
                . esc_html( $term->name ) . '</a>' . $sep_gray;
        }
        echo esc_html( get_the_title() );

    // Product category archive
    } elseif ( is_product_category() ) {
        $current = get_queried_object();
        $ancestors = array_reverse( get_ancestors( $current->term_id, 'product_cat' ) );
        foreach ( $ancestors as $ancestor_id ) {
            $parent = get_term( $ancestor_id, 'product_cat' );
            echo '<a href="' . esc_url( get_term_link( $parent ) ) . '" class="' . esc_attr( $link_class ) . '">'
                . esc_html( $parent->name ) . '</a>' . $sep_gray;
        }
        echo esc_html($current->name);

    // Página de la Tienda
    } elseif (is_shop()) {
        echo esc_html__('Tienda', 'woocommerce');

    // Single post (blog)
    } elseif ( is_single() && 'post' === get_post_type()) {
        $blog_page = get_fields( 'page_blog', 'option' );
        $url = $blog_page ? get_permalink( $blog_page ) : home_url( '/blog/' );
        echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $link_class ) . '">Blog</a>';
        //echo $sep_white . esc_html( get_the_title() );  // Descomentar si se desea mostrar el título
    
    // Single CPT
    } elseif ( is_single() ) {
        $pt_obj = get_post_type_object( get_post_type() );
        echo '<a href="' . esc_url( get_post_type_archive_link( $pt_obj->name ) ) . '" class="' . esc_attr( $link_class ) . '">'
            . esc_html( $pt_obj->labels->name ) . '</a>';
        //echo $sep_white . esc_html( get_the_title() );  // Descomentar si se desea mostrar el título

    // Static page
    } elseif ( is_page() ) {
        global $post;
        if ( $post->post_parent ) {
            $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
            foreach ($ancestors as $ancestor_id) {
                echo '<a href="' . esc_url( get_permalink( $ancestor_id ) ) . '" class="' . esc_attr( $link_class ) . '">'
                    . esc_html( get_the_title( $ancestor_id) ) . '</a>' . $sep_white;
            }
        }
        echo esc_html(get_the_title());

    // Blog category
    } elseif ( is_category() ) {
        $cat = get_queried_object(); 
        echo esc_html( $cat->name ); 

    // Arhive page
    } elseif ( is_archive() ) {
        echo esc_html__('Archivo', THEME_TEXTDOMAIN);

    // Search results
    } elseif ( is_search() ) {
        echo esc_html__('Resultados de búsqueda para: ', THEME_TEXTDOMAIN) . esc_html( get_search_query() );

    // 404 page
    } elseif (is_404()) {
        echo esc_html__('Página no encontrada', THEME_TEXTDOMAIN);

    // Fallback
    } else {
        echo esc_html( get_the_title() );
    }

    // Cerrar nav y contenedor
    echo '</nav></div>';
}

// Ejecutar breadcrumbs
generate_breadcrumbs();
