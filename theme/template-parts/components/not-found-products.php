<?php
defined( 'ABSPATH' ) || exit;

/**
 * Componente: Mensaje de “productos no encontrados”
 */
$contact_page = get_field( 'p_contacto', 'option' );

// Si ACF no devuelve nada, usamos la página de contacto por defecto
if ( empty( $contact_page ) ) {
    $contact_page = home_url( '/contacto/' );
}
?>
<section class="full-width c-bg-white pt-0 resultados">
  <div class="container my-5">
    <div class="text-center my-5 not-found-message mx-auto">
      <p class="lead c-black fw500">
        <?php esc_html_e( 'Lo sentimos, no encontramos productos que coincidan con tu búsqueda.', THEME_TEXTDOMAIN ); ?>
      </p>
      <?php if ( $contact_page ) : ?>
        <a href="<?php echo esc_url( $contact_page ); ?>" class="btn btn-md btn-dark">
          <?php esc_html_e( 'Contáctanos', THEME_TEXTDOMAIN ); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>
