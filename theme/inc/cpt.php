<?php
defined('ABSPATH') || exit;

/**
 * Registro de CPT y Taxonomías
 */

add_action( 'init', 'register_custom_post_types' );
function register_custom_post_types() {

   // otros CPT aquí ('post', 'servicios', etc.)

   // Taxonomías producto
   // Material
   register_taxonomy('material', 'product', array(
      'labels'            => array(
         'name'              => __('Materiales', 'theme'),
         'singular_name'     => __('Material', 'theme'),
         'search_items'      => __('Buscar Material', 'theme'),
         'all_items'         => __('Todos los Materiales', 'theme'),
         'parent_item'       => __('Material Padre', 'theme'),
         'parent_item_colon' => __('Material Padre:', 'theme'),
         'edit_item'         => __('Editar Material', 'theme'),
         'update_item'       => __('Actualizar Material', 'theme'),
         'add_new_item'      => __('Añadir Material', 'theme'),
         'new_item_name'     => __('Nuevo Material', 'theme'),
         'menu_name'         => __('Materiales', 'theme')
      ),
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'material'),
   ));
   // Género
   register_taxonomy('genero', 'product', array(
      'labels'            => array(
         'name'              => __('Géneros', 'theme'),
         'singular_name'     => __('Género', 'theme'),
         'search_items'      => __('Buscar Género', 'theme'),
         'all_items'         => __('Todos los Géneros', 'theme'),
         'parent_item'       => __('Género Padre', 'theme'),
         'parent_item_colon' => __('Género Padre:', 'theme'),
         'edit_item'         => __('Editar Género', 'theme'),
         'update_item'       => __('Actualizar Género', 'theme'),
         'add_new_item'      => __('Añadir Género', 'theme'),
         'new_item_name'     => __('Nuevo Género', 'theme'),
         'menu_name'         => __('Géneros', 'theme')
      ),
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'genero'),
   ));
   // Uso recomendado
   register_taxonomy('uso', 'product', array(
      'labels'            => array(
         'name'              => __('Usos', 'theme'),
         'singular_name'     => __('Uso', 'theme'),
         'search_items'      => __('Buscar Uso', 'theme'),
         'all_items'         => __('Todos los Usos', 'theme'),
         'parent_item'       => __('Usos Padre', 'theme'),
         'parent_item_colon' => __('Usos Padre:', 'theme'),
         'edit_item'         => __('Editar Uso', 'theme'),
         'update_item'       => __('Actualizar Uso', 'theme'),
         'add_new_item'      => __('Añadir Uso', 'theme'),
         'new_item_name'     => __('Nuevo Uso', 'theme'),
         'menu_name'         => __('Usos', 'theme')
      ),
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'uso'),
   ));
}

// Habilitar soporte para miniaturas en los tipos de publicación personalizados
// add_theme_support('post-thumbnails', array('post', 'servicios'));  // Cambia 'servicios' por el nombre de tu CPT

// Función para ajustar la paginación del blog
add_action( 'init', 'wpa_fix_blog_pagination' );
function wpa_fix_blog_pagination() {
   add_rewrite_rule(
      'blog/page/([0-9]+)/?$',
      'index.php?pagename=blog&paged=$matches[1]',
      'top'
   );
   add_rewrite_rule(
      'blog/([^/]*)$',
      'index.php?name=$matches[1]',
      'top'
   );
   add_rewrite_tag('%blog%', '([^/]*)');
}

// Añadir soporte para archivos en formularios de edición
add_action('post_edit_form_tag', 'post_edit_form_tag');
function post_edit_form_tag() {
    echo ' enctype="multipart/form-data"';
}

// Ajuste de URL para enlaces personalizados en el CPT Blog
add_filter( 'post_link', 'append_query_string', 10, 3 );
function append_query_string( $url, $post, $leavename ) {
    if ($post->post_type == 'post') {     
        $url = home_url(user_trailingslashit("blog/$post->post_name"));
    }
    return $url;
}
