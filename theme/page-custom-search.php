<?php
/**
 * Template Name: Búsquedas Custom
 *
 * @package custom_theme
 */

// Header + ACF + Contenido
get_header();
$fields = get_fields();
$contenido = get_the_content();

// Query inicial
$full_query = build_initial_query_page_custom($fields);
$initial_product_ids = wp_list_pluck( $full_query->posts, 'ID' );

// Query con paginación (solo primera página)
$paged = max( 1, get_query_var('paged') );
$custom_query = build_filtered_query( $fields, $paged, $initial_product_ids );
?>

<div class="container">
   <?php get_template_part('template-parts/components/breadcrumbs'); ?>
</div>

<?php
// Resultados
set_query_var('initial_query', $custom_query);
set_query_var('initial_product_ids', $initial_product_ids);
include(get_template_directory() . '/inc/results.php');

// Bloques flexibles ACF
if (!empty($fields['flexible_content']) && is_array($fields['flexible_content'])) {
   require_once get_template_directory() . '/template-parts/load-flexible-blocks.php';
   load_flexible_blocks($fields['flexible_content']);
}

get_footer();
