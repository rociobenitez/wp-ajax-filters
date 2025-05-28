<?php
defined('ABSPATH') || exit;

/**
 * Enqueue scripts and styles
 */
function custom_theme_enqueue_scripts() {
   // Encolar CSS externos
   wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), null );
   wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), null );
   wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), null );

   // CSS Principal del theme, admin y Woocommerce
   wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array('bootstrap-css'), _THEME_VERSION );
   wp_enqueue_style( 'my-admin-style', get_template_directory_uri() . '/assets/css/admin-styles.css', array('bootstrap-css'), _THEME_VERSION );
   wp_enqueue_style( 'woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce.css', array('bootstrap-css'), _THEME_VERSION );

   // JS Principal del theme
   wp_enqueue_script( 'theme-scripts', get_template_directory_uri() . '/assets/js/main.js', array(), _THEME_VERSION, true );

   // Encolar JS externos
   wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true );

   // Incluir Swiper.js y CSS en WordPress
   wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', array(), null);
   wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), null, true);
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_scripts' );
