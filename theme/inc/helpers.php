<?php
defined('ABSPATH') || exit;

/**
 * Funciones de ayuda y utilidades generales
 */

/**
 * Genera un encabezado HTML con clases y atributos personalizados.
 *
 * @param int $h Nivel del encabezado. Debe estar entre 0 y 3, donde 0 es h1 y 3 es p.
 * @param string $titulo Texto del encabezado.
 * @param string $class Clase CSS principal para el encabezado.
 * @param string $class2 Clase CSS secundaria, opcional.
 * @return string Código HTML del encabezado.
 */
function tagTitle($h, $titulo, $class, $class2 = '')
{
    $tags = array('h1', 'h2', 'h3', 'p');
    $h = is_numeric($h) && $h >= 0 && $h <= 3 ? (int) $h : 2;
    return sprintf('<%1$s class="%3$s %4$s">%2$s</%1$s>', $tags[$h], esc_html($titulo), esc_attr($class), esc_attr($class2));
}

// resto de funciones...
