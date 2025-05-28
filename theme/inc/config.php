<?php
defined('ABSPATH') || exit;

/**
 * Configuración del theme y definición de constantes
 */

// Definir el nombre del theme
if (! defined('THEME_NAME')) {
    define('THEME_NAME', 'Custom Theme');
}

// Definir el dominio de texto del theme
if (! defined('THEME_TEXTDOMAIN')) {
    define('THEME_TEXTDOMAIN', 'custom_theme');
}

// Versión del theme
if (! defined('_THEME_VERSION')) {
    define('_THEME_VERSION', '1.0.0');
}
