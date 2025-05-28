# WooCommerce AJAX Filters

Implementación de **filtros AJAX para WooCommerce** y plantilla personalizada de _“Búsquedas Custom”_ usando **ACF Pro**, que permite crear facetas combinadas (landings) basadas en dos o más taxonomías de producto (por ejemplo: “gafas de sol” + “gafas infantiles”).

## Casos de uso

1. **Página de categorías/taxonomías de producto**: Gestión dinámica de filtros en `archive-product.php`.
2. **Plantilla _“Búsquedas Custom”_** (`page-custom-search.php`): Página que inicia con un conjunto de IDs definido por campos ACF e incluye filtros AJAX.

## Requisitos

- WordPress ≥ 6.7.2
- Bootstrap ≥ 5.3.2
- [WooCommerce](https://woocommerce.com/es/)
- [ACF Pro](https://www.advancedcustomfields.com/pro/)
- Editor clásico (Classic Editor)

## Estructura del repositorio

```
wp-ajax-filters/
├── README.md
├── functions.php
├── page-custom-search.php    # Plantilla “Búsquedas Custom”
├── assets/
│   ├── css/
│   │    └── woocommerce.css  # Estilos personalizados para WooCommerce
│   └── js/
│        └── ajax-filters.js  # Maneja filtros y paginación AJAX
└── inc/
│   ├── ajax-filters.php      # Registra scripts y callback AJAX
│   ├── ajax-pagination.php   # Genera paginador sin recarga
│   ├── custom-query.php      # Extrae IDs iniciales vía ACF
│   └── custom-search.php     # Lógica AJAX para “Búsquedas Custom”
├── template-parts
│   └── sidebar-shop.php      # Plantilla que incluye los filtros AJAX
└── woocommerce
    └── archive-product.php   # Plantilla de categorías/taxonomías
```

> [!NOTE] > _El resto de funciones del theme (configuración, CPTs, helpers genéricos, etc.) se omiten aquí. Solo se presenta la lógica y archivos relacionados con los filtros AJAX._

## Funcionalidades

- **Filtros dinámicos**: checkboxes en sidebar reinician productos vía `fetch()` a `admin-ajax.php`.
- **Paginación AJAX**: enlaces `<a data-page="X">` sin `href`, interceptados por JS.
- **Búsquedas Custom**: backend ACF define filtros iniciales, tu JS pagina sobre IDs pre-calc.

## Instalación

1. Clona el repositorio en tu carpeta de themes:
   ```bash
   git clone git@github.com:rociobenitez/wp-ajax-filters.git
   mv wp-ajax-filters /wp-content/themes/your-theme
   ```
2. Activa el theme en _Apariencia → Themes_.
3. Asegúrate de tener instalados y activos los [plugins requeridos](#requisitos).

## Configuración

1. En ACF Pro crea un grupo de campos (ubicado en _“Búsquedas Custom”_) con selectores de taxonomías:

   - `product_cat`
   - `product_brand`
   - `material`
   - `genero`
   - `uso`

2. Asigna el grupo a la plantilla de página `page-custom-search.php`.
3. Puedes personalizar los estilos de la tienda en `assets/css/woocommerce.css`.
4. Añade una imagen por defecto para los productos sin imagen en `assets/img/woocommerce-default-product.jpg`.

> [!NOTE] > _Las **taxonomías** utilizadas en este repositorio son de **ejemplo**. Pueden ser reemplazadas por las tuyas._
