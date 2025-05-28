<?php
/**
 * Carga bloques flexibles dinámicamente desde ACF
 *
 * Se espera que el campo flexible_content contenga la estructura de bloques.
 *
 * @param array $flexible_content Array de bloques flexibles.
 * @param bool $return_content Si es true, devuelve el contenido generado en lugar de mostrarlo.
 * @return string|null El contenido generado si $return_content es true, de lo contrario null.
 */
function load_flexible_blocks($flexible_content, $return_content = false) {
   if (is_array($flexible_content)) {
      $output = '';

      foreach ($flexible_content as $block) {
         if ( isset( $block['acf_fc_layout'] ) && !empty( $block['acf_fc_layout'] ) ) {
            $block_file = 'template-parts/blocks/' . $block['acf_fc_layout'] . '.php';
   
            if ( file_exists( get_stylesheet_directory() . '/' . $block_file ) ) {
               if ($return_content) {
                  ob_start();
                  include locate_template( $block_file );
                  $output .= ob_get_clean();
               } else {
                  include locate_template( $block_file );
               }
            }
         }
      }

      if ( $return_content ) {
         return $output;
      }
   }

   return null;
}
