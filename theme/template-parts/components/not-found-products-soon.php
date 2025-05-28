<?php
defined( 'ABSPATH' ) || exit;

/**
 * Componente: Mensaje de aviso “actualmente no hay productos en esta categoría”
 */
$contact_page = get_field( 'p_contacto', 'option' );

// Si ACF no devuelve nada, usamos la página de contacto por defecto
if ( empty( $contact_page ) ) {
    $contact_page = home_url( '/contacto/' );
}
?>
<div class="no-products col-md-10 col-lg-6 mx-auto no-products text-md-center p-4 bg-light my-5">
    <p class="heading-5 mb-3">
        <?php esc_html_e( '¡Próximamente!', THEME_TEXTDOMAIN ); ?>
    </p>
    <p class="mb-4">
        <?php
            /* translators: %s: nombre de la categoría */
            printf(
            esc_html__( 'Actualmente estamos preparando nuestra selección de productos para %s. Mientras tanto, puedes explorar otras categorías o ponerte en contacto con nosotros si necesitas ayuda.', THEME_TEXTDOMAIN ),
            '<strong>' . single_term_title( '', false ) . '</strong>'
            );
        ?>
    </p>
    <div class="d-flex justify-content-md-center gap-2">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-lg btn-primary">
            <?php esc_html_e( 'Ver catálogo completo', THEME_TEXTDOMAIN ); ?>
        </a>
        <?php if ( $contact_page ) : ?>
            <a href="<?php echo esc_url( $contact_page ); ?>" class="btn btn-lg btn-outline">
                <?php esc_html_e( 'Contáctanos', THEME_TEXTDOMAIN ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>