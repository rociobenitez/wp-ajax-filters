<?php
defined( 'ABSPATH' ) || exit;

/**
 * Componente: Product Card
 * 
 * Variables esperadas (vía set_query_var en el loop):
 *  - $product_obj         : WC_Product
 *  - $product_id          : int
 *  - $htag_products       : int (0 es 'H1', 1 es 'H2', 2 es 'H3' y 3 es 'p')
 *  - $default_product_img : string (URL imagen fallback)
 *  - $show_price          : bool
 *  - $add_to_cart         : bool
 */

if ( empty( $product_obj ) || empty( $product_id ) ) {
    return;
}

// Valores por defecto si no se han pasado
$show_price          = isset( $show_price ) ? (bool) $show_price : true;
$add_to_cart         = isset( $add_to_cart ) ? (bool) $add_to_cart : false;
$htag_products       = ! empty( $htag_products ) ? $htag_products : 3;
$default_product_img = ! empty( $default_product_img )
    ? $default_product_img
    : get_stylesheet_directory_uri() . '/assets/img/default-product.jpg';

// Alias
$_product = $product_obj;
?>

<div class="product-item card border-0">
  <?php if ( $_product->is_on_sale() ) : ?>
    <span class="badge badge-sale position-absolute">
      <?php echo esc_html__( 'Oferta', THEME_TEXTDOMAIN ); ?>
    </span>
  <?php endif; ?>

  <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="product-link">
    <div class="product-image">
      <?php
      if ( has_post_thumbnail( $product_id ) ) {
         echo wp_get_attachment_image(
            get_post_thumbnail_id( $product_id ),
            'woocommerce_thumbnail',
            false,
            array(
               'class'    => 'card-img-top',
               'loading'  => 'lazy',
               'decoding' => 'async',
               'alt'      => get_the_title( $product_id ),
            )
         );
      } else {
         // Fallback
         printf(
               '<img src="%1$s" class="card-img-top" loading="lazy" decoding="async" alt="%2$s">',
               esc_url( $default_product_img ),
               esc_attr( get_the_title( $product_id ) )
         );
      }
      ?>
    </div>
  </a>

  <div class="card-body px-0">
    <?php
    // Título del producto
    echo tagTitle( $htag_products, get_the_title( $product_id ), 'product-title text-transform-uppercase fs15 text-center', '' );
    ?>

   <?php if( $show_price ) : ?>
      <div class="product-price mb-1 fw600 c-black text-center">
         <?php echo wp_kses_post( $_product->get_price_html() ); ?>
      </div>
   <?php endif; ?>

    <?php if ( wc_review_ratings_enabled() ) : ?>
      <div class="product-rating mb-2 text-center">
        <?php
        // Muestra estrellas y número de valoraciones
        echo wc_get_rating_html( $_product->get_average_rating() );
        ?>
      </div>
    <?php endif; ?>

    <?php if ( $add_to_cart ) : ?>
      <div class="product-add-to-cart">
         <?php
         // También se puede utilizar: woocommerce_template_loop_add_to_cart()
         echo apply_filters(
         'woocommerce_loop_add_to_cart_link',
         sprintf(
            '<a href="%s" data-quantity="1" class="button %s" %s>%s</a>',
            esc_url( $_product->add_to_cart_url() ),
            esc_attr( $_product->is_purchasable() ? 'add_to_cart_button' : '' ),
            wc_implode_html_attributes( [
               'data-product_id'  => $product_id,
               'data-product_sku' => $_product->get_sku(),
            ] ),
            esc_html( $_product->add_to_cart_text() )
         ),
         $_product
         );
         ?>
      </div>
    <?php endif; ?>
  </div>
</div>
