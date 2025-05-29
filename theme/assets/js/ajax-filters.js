document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".grid-products");
  const clearBtn = document.getElementById("clear-filters");
  const { ajaxurl, nonce, initialTaxonomy, initialTermId, initialProductIDs } = ajaxFiltersData;

  // Las clases responsivas que queremos togglear
  const respClasses = [
    "row",
    "row-cols-1",
    "row-cols-sm-2",
    "row-cols-md-3",
    "row-cols-lg-4",
  ];

  // Parsear IDs iniciales (para busquedas custom)
  let baseIDs = [];
  try {
    baseIDs = JSON.parse(initialProductIDs);
  } catch (e) {
    baseIDs = [];
  }

  let currentPage = 1;
  let filters = {};
  // Si estamos en página de categoría de producto
  if (initialTaxonomy && initialTermId) {
    filters[initialTaxonomy] = [initialTermId];
  }

  function fetchProducts(page = 1) {
    currentPage = page;
    const formData = new FormData();
    formData.append("action", "ajax_filter_products");
    formData.append("nonce", nonce);
    formData.append("page", page);
    formData.append("filters", JSON.stringify(filters));
    formData.append("initial_ids", JSON.stringify(baseIDs)); // Busquedas custom

    const grid = document.querySelector(".grid-products");
    const pagContainer = document.querySelector(".pagination-container");
    const totalProducts = document.querySelector(".total-products");

    // Spinner
    grid && grid.classList.add("opacity-50");

    fetch(ajaxurl, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((r) => r.json())
      .then((data) => {
        const { html, pagination, count, no_results } = data;

        // Reemplazar productos y paginación
        grid.innerHTML = html;
        pagContainer.innerHTML = pagination;

        // Actualizar total de productos
        if (totalProducts)
          totalProducts.textContent = "Total de productos: " + count;

        // Toggle de clases de boostrap
        if (no_results) {
          respClasses.forEach((c) => grid.classList.remove(c));
        } else {
          respClasses.forEach((c) => grid.classList.add(c));
        }
      })
      .catch(console.error)
      .finally(() => {
        grid && grid.classList.remove("opacity-50");
      });
  }

  // Listeners de filtros
  document.querySelectorAll(".ajax-filter").forEach((cb) => {
    cb.addEventListener("change", (e) => {
      const tax = e.target.dataset.tax;
      const term = parseInt(e.target.dataset.term, 10);
      filters[tax] = filters[tax] || [];
      if (e.target.checked) {
        filters[tax].push(term);
      } else {
        filters[tax] = filters[tax].filter((id) => id != term);
      }
      fetchProducts(1);
    });
  });

  // Limpiar filtros
  clearBtn &&
    clearBtn.addEventListener("click", () => {
      filters = {};
      document
        .querySelectorAll(".ajax-filter")
        .forEach((cb) => (cb.checked = false));
      fetchProducts(1);
    });

  // Paginación delegada
  document.body.addEventListener("click", (e) => {
    if (!e.target.matches(".pagination-container a")) return;
    e.preventDefault();
    const page = parseInt(e.target.dataset.page, 10);
    fetchProducts(page);
    grid.scrollIntoView({ behavior: "smooth", block: "start" }); // scroll suave
  });

  fetchProducts(1);
});
