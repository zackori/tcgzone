/* ==========================================================
   tcg.name — Shop Overview page JS
   Everything here is self-contained: sample data, filtering,
   sorting, pagination, and the product modal all live in this
   one file so the page works standalone once you drop your own
   navbar/footer around it.
   ========================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ------------------------------------------------------
     1) PRODUCT DATA — merged from two sources by id:
     - products.php: the live/authoritative source for anything
       that changes (price, stock, category, etc.) — pulled
       straight from the products_test table.
     - products.json: static per-card details that don't live
       in the database yet (e.g. description/lore text).
     On any field both sources define, products.php always wins —
     json is only used to fill in fields the DB doesn't have.
  ------------------------------------------------------ */
  var PRODUCTS = [];

  function mergeProducts(dbProducts, staticProducts) {
    var staticById = {};
    staticProducts.forEach(function (p) { staticById[p.id] = p; });

    return dbProducts.map(function (dbProduct) {
      var staticMatch = staticById[dbProduct.id] || {};
      // staticMatch first, dbProduct second — dbProduct's keys
      // overwrite any matching keys from staticMatch, so live data
      // always takes priority; fields only staticMatch has (like
      // description) pass through untouched.
      return Object.assign({}, staticMatch, dbProduct);
    });
  }

  function loadProducts() {
    return Promise.all([
      fetch('products.php').then(function (response) {
        if (!response.ok) throw new Error('Failed to load products.php (' + response.status + ')');
        return response.json();
      }),
      fetch('products.json').then(function (response) {
        if (!response.ok) throw new Error('Failed to load products.json (' + response.status + ')');
        return response.json();
      })
    ])
      .then(function (results) {
        var dbProducts = results[0];
        var staticProducts = results[1];
        PRODUCTS = mergeProducts(dbProducts, staticProducts);
      })
      .catch(function (err) {
        console.error('Could not load product data:', err);
        grid.innerHTML = '<p class="text-muted text-center w-100 py-5">Couldn\'t load products. Check that products.php and products.json are both reachable.</p>';
      });
  }

  /* ------------------------------------------------------
     2) STATE
  ------------------------------------------------------ */
  var state = {
    filters: { category: 'all', cardType: 'all', price: 'all', rarity: 'all', condition: 'all' },
    sort: 'relevance',
    page: 1,
    pageSize: 16
  };

  var FILTER_LABELS = {
    category: { 'Pokémon': 'Pokémon', 'Magic': 'Magic', 'One Piece': 'One Piece' },
    cardType: { singles: 'Singles', sealed: 'Sealed Product', accessories: 'Accessories' },
    rarity: { common: 'Common', uncommon: 'Uncommon', rare: 'Rare', 'ultra-rare': 'Ultra Rare', 'secret-rare': 'Secret Rare' },
    condition: { mint: 'Mint', 'near-mint': 'Near Mint', 'lightly-played': 'Lightly Played', damaged: 'Damaged' },
    price: { '0-25': 'Under $25', '25-100': '$25–$100', '100-500': '$100–$500', '500-99999': '$500+' }
  };

  /* ------------------------------------------------------
     3) DOM REFS
  ------------------------------------------------------ */
  var grid            = document.getElementById('product-grid');
  var noResultsMsg     = document.getElementById('noResultsMsg');
  var resultsCount     = document.getElementById('resultsCount');
  var chipTrack        = document.getElementById('filter-chip-track');
  var paginationDots   = document.getElementById('pagination-dots');
  var pagePrevBtn       = document.getElementById('pagePrev');
  var pageNextBtn       = document.getElementById('pageNext');
  var paginationRow     = document.getElementById('pagination-row');

  var filterCategory   = document.getElementById('filterCategory');
  var filterCardType   = document.getElementById('filterCardType');
  var filterPrice      = document.getElementById('filterPrice');
  var filterRarity     = document.getElementById('filterRarity');
  var filterCondition  = document.getElementById('filterCondition');
  var sortBySelect      = document.getElementById('sortBy');
  var clearFiltersBtn  = document.getElementById('clearFiltersBtn');

  /* ------------------------------------------------------
     4) FILTER + SORT + PAGINATE
  ------------------------------------------------------ */
  function priceInRange(price, rangeKey) {
    if (rangeKey === 'all') return true;
    var parts = rangeKey.split('-').map(Number);
    return price >= parts[0] && price <= parts[1];
  }

  function getFilteredSortedProducts() {
    var result = PRODUCTS.filter(function (p) {
      var categoryMatch = state.filters.category === 'all' ||
        (p.category || '').toLowerCase() === state.filters.category.toLowerCase();

      return categoryMatch &&
             (state.filters.cardType === 'all' || p.cardType === state.filters.cardType) &&
             priceInRange(p.price, state.filters.price) &&
             (state.filters.rarity === 'all' || p.rarity === state.filters.rarity) &&
             (state.filters.condition === 'all' || p.condition === state.filters.condition);
    });

    switch (state.sort) {
      case 'price-asc':  result.sort(function (a, b) { return a.price - b.price; }); break;
      case 'price-desc': result.sort(function (a, b) { return b.price - a.price; }); break;
      case 'name-asc':   result.sort(function (a, b) { return a.title.localeCompare(b.title); }); break;
      default: break; // 'relevance' keeps original order
    }

    return result;
  }

  /* ------------------------------------------------------
     5) RENDER: PRODUCT GRID
  ------------------------------------------------------ */
  function renderGrid() {
    var filtered = getFilteredSortedProducts();
    var totalPages = Math.max(1, Math.ceil(filtered.length / state.pageSize));
    state.page = Math.min(state.page, totalPages);

    var start = (state.page - 1) * state.pageSize;
    var pageItems = filtered.slice(start, start + state.pageSize);

    resultsCount.textContent = filtered.length.toLocaleString() + ' Result' + (filtered.length === 1 ? '' : 's') + ' Found';
    noResultsMsg.classList.toggle('d-none', filtered.length !== 0);
    grid.classList.toggle('d-none', filtered.length === 0);
    paginationRow.classList.toggle('d-none', filtered.length === 0);

    grid.innerHTML = pageItems.map(function (p) {
      var imgStyle = p.image ? ' style="background-image:url(\'' + p.image + '\')"' : '';
      var artClass = p.image ? '' : (' ' + (p.art || 'card-art-1'));
      return (
        '<div class="col-6 col-md-4 col-lg-3">' +
          '<article class="shop-card" data-id="' + p.id + '" role="button" tabindex="0" aria-label="View details for ' + p.title + '">' +
            '<div class="shop-card-img' + artClass + '"' + imgStyle + '>' +
              (p.stock === 'sold-out' ? '<span class="sold-out-badge">Sold Out</span>' : '') +
            '</div>' +
            '<h3 class="shop-card-title">' + p.title + '</h3>' +
            '<p class="shop-card-subtitle">' + p.subtitle + '</p>' +
            '<p class="stock">' + p.stock + '</p>'+
            '<p class="shop-card-price">Market Price: <strong>$' + p.marketPrice.toFixed(2) + '</strong></p>' +
          '</article>' +
        '</div>'
      );
    }).join('');

    renderPagination(totalPages);
  }

  /* ------------------------------------------------------
     6) RENDER: PAGINATION (dots + prev/next arrows)
  ------------------------------------------------------ */
  function renderPagination(totalPages) {
    paginationDots.innerHTML = '';

    for (var i = 1; i <= totalPages; i++) {
      (function (pageNum) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'page-dot' + (pageNum === state.page ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to page ' + pageNum);
        dot.addEventListener('click', function () {
          state.page = pageNum;
          renderGrid();
          grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        paginationDots.appendChild(dot);
      })(i);
    }

    pagePrevBtn.disabled = state.page <= 1;
    pageNextBtn.disabled = state.page >= totalPages;
  }

  pagePrevBtn.addEventListener('click', function () {
    if (state.page > 1) {
      state.page -= 1;
      renderGrid();
      grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
  pageNextBtn.addEventListener('click', function () {
    state.page += 1; // renderGrid() clamps this to the real last page
    renderGrid();
    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  /* ------------------------------------------------------
     7) RENDER: ACTIVE FILTER CHIPS
  ------------------------------------------------------ */
  function renderChips() {
    var chips = [];

    Object.keys(state.filters).forEach(function (group) {
      var value = state.filters[group];
      if (value !== 'all') {
        var label = FILTER_LABELS[group][value] || value;
        chips.push({ group: group, label: label });
      }
    });
    
    var activeFiltersLabel = document.getElementById('active-filters-label');
    if (activeFiltersLabel) activeFiltersLabel.classList.toggle('d-none', chips.length === 0);

    chipTrack.innerHTML = chips.map(function (chip) {
      return '<span class="filter-chip" data-group="' + chip.group + '">' + chip.label +
             '<button type="button" aria-label="Remove ' + chip.label + ' filter">×</button></span>';
    }).join('');

    chipTrack.querySelectorAll('.filter-chip button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var group = btn.parentElement.getAttribute('data-group');
        resetFilterGroup(group);
      });
    });
  }

  function resetFilterGroup(group) {
    state.filters[group] = 'all';
    var selectMap = { category: filterCategory, cardType: filterCardType, price: filterPrice, rarity: filterRarity, condition: filterCondition };
    if (selectMap[group]) selectMap[group].value = 'all';
    state.page = 1;
    renderChips();
    renderGrid();
  }

  /* ------------------------------------------------------
     8) FILTER / SORT / CLEAR EVENT WIRING
  ------------------------------------------------------ */
  filterCategory.addEventListener('change', function () {
    state.filters.category = filterCategory.value;
    state.page = 1;
    renderChips();
    renderGrid();
  });
  filterCardType.addEventListener('change', function () {
    state.filters.cardType = filterCardType.value;
    state.page = 1;
    renderChips();
    renderGrid();
  });
  filterPrice.addEventListener('change', function () {
    state.filters.price = filterPrice.value;
    state.page = 1;
    renderChips();
    renderGrid();
  });
  filterRarity.addEventListener('change', function () {
    state.filters.rarity = filterRarity.value;
    state.page = 1;
    renderChips();
    renderGrid();
  });
  filterCondition.addEventListener('change', function () {
    state.filters.condition = filterCondition.value;
    state.page = 1;
    renderChips();
    renderGrid();
  });
  sortBySelect.addEventListener('change', function () {
    state.sort = sortBySelect.value;
    state.page = 1;
    renderGrid();
  });
  clearFiltersBtn.addEventListener('click', function () {
    state.filters = { category: 'all', cardType: 'all', price: 'all', rarity: 'all', condition: 'all' };
    state.sort = 'relevance';
    state.page = 1;
    filterCategory.value = 'all';
    filterCardType.value = 'all';
    filterPrice.value = 'all';
    filterRarity.value = 'all';
    filterCondition.value = 'all';
    sortBySelect.value = 'relevance';
    renderChips();
    renderGrid();
  });

  /* ------------------------------------------------------
     9) PRODUCT DETAIL MODAL
  ------------------------------------------------------ */
  var productModalEl = document.getElementById('productModal');
  var productModal = new bootstrap.Modal(productModalEl);
  var modalQty = 1;

  var modalImage        = document.getElementById('modal-product-image');
  var modalSoldOutBadge = document.getElementById('modal-sold-out-badge');
  var modalTitle        = document.getElementById('productModalTitle');
  var modalSubtitle     = document.getElementById('modal-product-subtitle');
  var modalDescription  = document.getElementById('modal-description');
  var modalPrice        = document.getElementById('modal-price');
  var modalComparePrice = document.getElementById('modal-compare-price');
  var modalRequirements = document.getElementById('modal-requirements');
  var modalCategory     = document.getElementById('modal-category');
  var modalQtyValue     = document.getElementById('modal-qty-value');
  var modalAddToCartBtn = document.getElementById('modal-add-to-cart');
  var modalQtyStepper   = document.getElementById('modal-qty-stepper');

  var activeProduct = null;

  function openProductModal(id) {
    var product = PRODUCTS.filter(function (p) { return p.id === id; })[0];
    if (!product) return;
    activeProduct = product;
    modalQty = 1;

    if (product.image) {
      modalImage.className = 'modal-product-image';
      modalImage.style.backgroundImage = "url('" + product.image + "')";
    } else {
      modalImage.className = 'modal-product-image ' + (product.art || 'card-art-1');
      modalImage.style.backgroundImage = '';
    }
    modalSoldOutBadge.classList.toggle('d-none', product.stock !== 'sold-out');
    modalTitle.textContent = product.title;
    modalSubtitle.textContent = product.subtitle;
    modalDescription.textContent = product.description || '';
    modalPrice.textContent = '$' + product.price.toFixed(2);

    if (product.marketPrice > product.price) {
      modalComparePrice.textContent = '$' + product.marketPrice.toFixed(2);
      modalComparePrice.classList.remove('d-none');
    } else {
      modalComparePrice.classList.add('d-none');
    }

    modalRequirements.innerHTML = product.requirements.map(function (r) {
    return `
        <li class="requirement-item">
            <span class="requirement-label">${r.label}: <span class="requirement-value">${r.value}</span></span>
            
        </li>
    `;
}).join('');

    modalCategory.textContent = product.category;
    modalQtyValue.textContent = modalQty;

    var soldOut = product.stock === 'sold-out';
    modalAddToCartBtn.disabled = soldOut;
    modalAddToCartBtn.innerHTML = soldOut
      ? 'Sold Out'
      : '<i class="bi bi-cart-plus"></i> Add To Cart';
    modalQtyStepper.classList.toggle('d-none', soldOut);

    productModal.show();
  }

  // Open modal on card click or Enter/Space (keyboard accessible)
  grid.addEventListener('click', function (e) {
    var card = e.target.closest('.shop-card');
    if (card) openProductModal(card.getAttribute('data-id'));
  });
  grid.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    var card = e.target.closest('.shop-card');
    if (card) {
      e.preventDefault();
      openProductModal(card.getAttribute('data-id'));
    }
  });

  modalQtyStepper.querySelector('.qty-decrease').addEventListener('click', function () {
    modalQty = Math.max(1, modalQty - 1);
    modalQtyValue.textContent = modalQty;
  });
  modalQtyStepper.querySelector('.qty-increase').addEventListener('click', function () {
    modalQty += 1;
    modalQtyValue.textContent = modalQty;
  });

  /* ------------------------------------------------------
     10) ADD TO CART
     Uses the same localStorage key/shape ('tcgCart') as
     cart.html / checkout.html, so items added here already
     show up correctly on those pages.
  ------------------------------------------------------ */
  var CART_KEY = 'tcgCart';

  function addActiveProductToCart() {
    if (!activeProduct || activeProduct.stock === 'sold-out') return;

    var cart = [];
    try {
      cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch (e) {
      cart = [];
    }

    var existing = cart.filter(function (item) { return item.id === activeProduct.id; })[0];
    if (existing) {
      existing.qty += modalQty;
    } else {
      cart.push({
        id: activeProduct.id,
        name: activeProduct.title,
        price: activeProduct.price,
        qty: modalQty,
        art: activeProduct.art
      });
    }

    localStorage.setItem(CART_KEY, JSON.stringify(cart));

    var originalLabel = modalAddToCartBtn.innerHTML;
    modalAddToCartBtn.innerHTML = 'Added ✓';
    modalAddToCartBtn.disabled = true;
    setTimeout(function () {
      modalAddToCartBtn.innerHTML = originalLabel;
      modalAddToCartBtn.disabled = false;
    }, 1200);
  }

  modalAddToCartBtn.addEventListener('click', addActiveProductToCart);

  /* ------------------------------------------------------
     11) INITIAL LOAD + RENDER
     Filters/sort/pagination controls can be interacted with
     immediately, but the grid itself only renders once
     products.json has actually loaded.
  ------------------------------------------------------ */
  loadProducts().then(function () {
    renderChips();
    renderGrid();
  });

    /* ------------------------------------------------------
        4) NAVBAR: close the mobile menu after a link is tapped
        (Bootstrap doesn't do this automatically for in-page
        anchors, so the collapsed menu would otherwise stay open).
    ------------------------------------------------------ */
    var navCollapseEl = document.getElementById('mainNav');
    if (navCollapseEl) {
        var bsCollapse = new bootstrap.Collapse(navCollapseEl, { toggle: false });
        navCollapseEl.querySelectorAll('.nav-link, .dropdown-item').forEach(function (link) {
        link.addEventListener('click', function () {
            if (navCollapseEl.classList.contains('show') && window.innerWidth < 992) {
            bsCollapse.hide();
            }
        });
        });
    }

    });