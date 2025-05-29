<?php
defined('ABSPATH') || exit;

// Obtener todos los campos personalizados de ACF
$fields = get_fields();

// Inicialización de variables de filtro con valores predeterminados
$selected_category = $fields['product_cat'] ?? null;
$selected_marca = $fields['product_brand'] ?? null;
$selected_material = $fields['material'] ?? null;
$selected_genero = $fields['genero'] ?? null;
$selected_uso = $fields['uso'] ?? null;

// Mapeo de taxonomías con valores de filtro
$taxonomies = [
   'product_cat' => $selected_category,
   'marca' => $selected_marca,
   'material' => $selected_material,
   'genero' => $selected_genero,
   'uso' => $selected_uso,
];

// Configuración inicial de argumentos de consulta
$args = [
   'post_type' => 'product',
   'posts_per_page' => -1,
   'tax_query' => [
      'relation' => 'AND', // Se requiere que todas las condiciones se cumplan
   ]
];

// Construcción dinámica de tax_query según filtros activos
foreach ($taxonomies as $taxonomy => $value) {
   if (!empty($value)) {
      $args['tax_query'][] = [
         'taxonomy' => $taxonomy,
         'field'    => 'term_id',
         'terms'    => $value
      ];
   }
}

$query = new WP_Query($args);

// Almacenar los resultados iniciales
$product_ids = wp_list_pluck($query->posts, 'ID');
update_option('custom_query_all_product_ids', $product_ids); // IDs iniciales
update_option('filtros_iniciales', $taxonomies); // Filtros iniciales

return $query;
