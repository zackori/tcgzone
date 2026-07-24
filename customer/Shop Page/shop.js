/* ------------------------------------------------------
    GLOBAL NAVBAR SEARCH HANDLER
------------------------------------------------------ */
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.querySelector('.search-input');
    var searchIcon = document.querySelector('.search img');

    function performSearch() {
        var query = searchInput.value.trim();
        // Redirect to shop page with the search parameter
        window.location.href = '/tcgzone/customer/Shop Page/shop.php?search=' + encodeURIComponent(query);
    }

    if (searchInput) {
        // Trigger search on pressing 'Enter'
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    if (searchIcon) {
        // Trigger search on clicking the search magnifying glass icon
        searchIcon.addEventListener('click', performSearch);
    }
});

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

  /* HELPER FUNCTION: Format currency with commas and peso sign */
  function formatCurrency(amount) {
    return '₱ ' + Number(amount).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  /* ------------------------------------------------------
    2) STATE
  ------------------------------------------------------ */
  var state = {
    filters: { category: 'all', cardType: 'all', price: 'all', rarity: 'all', condition: 'all', search:''},
    sort: 'relevance',
    page: 1,
    pageSize: 18
  };

  var FILTER_LABELS = {
    category: { 'Pokémon': 'Pokémon', 'Magic: The Gathering': 'Magic: The Gathering', 'One Piece': 'One Piece' },
    condition: { mint: 'Mint', 'near-mint': 'Near Mint', 'lightly-played': 'Lightly Played', damaged: 'Damaged' },
    price: { '0-100': 'Under  ₱100', '100-500': '₱100–₱500', '500-1,000': '₱500–₱1,000', '1000-5000': '₱1,000–₱5,000', '5000-50000': '₱5,000–₱50,000', '50000-500000': '₱50,000–₱500,000', '500000-9999999': '₱500,000+' }
    // cardType/rarity labels aren't here anymore — they depend on
    // which category is selected, so they live in
    // CATEGORY_FILTER_CONFIG below instead.
  };

  /* Card Type + Rarity options are entirely category-specific.
    Selecting a category rebuilds both dropdowns from this map;
    with no category selected, both stay hidden. */
  var CATEGORY_FILTER_CONFIG = {
    'Pokémon': {
      cardType: [
        { value: 'Cards', label: 'Singles' },
        { value: 'Sealed', label: 'Sealed Product' },
        { value: 'Collections', label: 'Collections' }
      ],
      rarity: [
        { value: 'common', label: 'Common' },
        { value: 'uncommon', label: 'Uncommon' },
        { value: 'rare', label: 'Rare' },
        { value: 'ultra-rare', label: 'Ultra Rare' },
        { value: 'secret-rare', label: 'Secret Rare' }
      ]
    },
    'Magic: The Gathering': {
      cardType: [
        { value: 'Creature', label: 'Creature' },
        { value: 'Legendary Creature', label: 'Legendary Creature' },
        { value: 'Enchantment', label: 'Enchantment' },
        { value: 'Artifact', label: 'Artifact' },
        { value: 'Legendary Artifact', label: 'Legendary Artifact' },
        { value: 'Instant', label: 'Instant' }
      ],
      rarity: [
        { value: 'C', label: 'C - Common' },
        { value: 'U', label: 'U - Uncommon' },
        { value: 'R', label: 'R - Rare' },
        { value: 'M', label: 'M - Mythic Rare' },
        { value: 'S', label: 'S - Special' },
        { value: 'P', label: 'P - Promo' }
      ]
    },
    'One Piece': {
      cardType: [
        { value: 'Character', label: 'Character' },
        { value: 'Leader', label: 'Leader' }
      ],
      rarity: [
        { value: 'C', label: 'C - Common' },
        { value: 'UC', label: 'UC - Uncommon' },
        { value: 'R', label: 'R - Rare' },
        { value: 'SR', label: 'SR - Super Rare' },
        { value: 'SEC', label: 'SEC - Secret Rare' },
        { value: 'L', label: 'L - Leader' },
        { value: 'P', label: 'P - Promo' },
        { value: 'SP', label: 'SP - Special Rare' },
        { value: 'AA', label: 'AA - Alternate Art' },
        { value: 'TR', label: 'TR - Treasure Rare' },
        { value: 'MR', label: 'MR - Manga Rare' }
      ]
    }
  };


  var params = new URLSearchParams(window.location.search);

  var category = params.get('category');
  const cardType = params.get("cardType");
  var productId = params.get("product");
  var searchQuery = params.get('search');

  if (category) {
      state.filters.category = category;
  }
  // Add this block below it:
  if (cardType) {
      state.filters.cardType = cardType;
  }
  if (searchQuery) {
      state.filters.search = searchQuery; 
  }
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
  function updateCardTypeAndRarityOptions() {
    var config = CATEGORY_FILTER_CONFIG[state.filters.category];

    // No category selected -> hide both, reset their state so a
    // stale value from a previous category can't silently apply.
    if (!config) {
      filterCardType.classList.add('d-none');
      filterRarity.classList.add('d-none');
      filterCardType.innerHTML = '<option value="all" selected>Select Product Type</option>';
      filterRarity.innerHTML = '<option value="all" selected>Select Rarity</option>';
      state.filters.cardType = 'all';
      state.filters.rarity = 'all';
      return;
    }

    filterCardType.innerHTML = '<option value="all" selected>Select Product Type</option>' +
      config.cardType.map(function (opt) {
        return '<option value="' + opt.value + '">' + opt.label + '</option>';
      }).join('');

    filterRarity.innerHTML = '<option value="all" selected>Select Rarity</option>' +
      config.rarity.map(function (opt) {
        return '<option value="' + opt.value + '">' + opt.label + '</option>';
      }).join('');

    filterCardType.classList.remove('d-none');
    filterRarity.classList.remove('d-none');

    // Switching category invalidates whatever was previously picked
    state.filters.cardType = 'all';
    state.filters.rarity = 'all';
  }

  function priceInRange(price, rangeKey) {
    if (rangeKey === 'all') return true;
    var parts = rangeKey.split('-').map(Number);
    return price >= parts[0] && price <= parts[1];
  }

  function getFilteredSortedProducts() {
    var result = PRODUCTS.filter(function (p) {
      var categoryMatch = state.filters.category === 'all' ||
        (p.category || '').toLowerCase() === state.filters.category.toLowerCase();

        var searchMatch = true;
      if (state.filters.search) {
        var term = state.filters.search.toLowerCase();
        var title = (p.title || '').toLowerCase();
        searchMatch = title.includes(term);
      }

            return categoryMatch &&
            searchMatch &&  
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
    // noResultsMsg.classList.toggle('d-none', filtered.length !== 0);
    if (filtered.length === 0) {
      if (state.filters.search) {
        noResultsMsg.textContent = 'No cards match "' + state.filters.search + '". Try clearing a few.';
      } else {
        noResultsMsg.textContent = 'No cards match those filters. Try clearing a few.';
      }
      noResultsMsg.classList.remove('d-none');
    } else {
      noResultsMsg.classList.add('d-none');
    };
    grid.classList.toggle('d-none', filtered.length === 0);
    paginationRow.classList.toggle('d-none', filtered.length === 0);

    grid.innerHTML = pageItems.map(function (p) {
      var imgStyle = p.image ? ' style="background-image:url(\'' + p.image + '\')"' : '';
      var artClass = p.image ? '' : (' ' + (p.art || 'card-art-1'));
      var stockLabel = p.stock <= 0
    ? 'Out of Stock'
    : 'Stock: ' + p.stock;
      return (
        '<div class="col-6 col-md-4 col-lg-card-5">' +
          '<article class="shop-card" data-id="' + p.id + '" role="button" tabindex="0" aria-label="View details for ' + p.title + '">' +
            '<div class="shop-card-img' + artClass + '"' + imgStyle + '>' +
              (p.stock <= 0 ? '<span class="out-of-stock-badge">Out of Stock</span>' : '') +
            '</div>' +
            '<div class="shop-card-body">' +
              '<h3 class="shop-card-title">' + p.title + '</h3>' +
              '<p class="shop-card-subtitle">' + p.subtitle + '</p>' +
              '<div class="shop-card-prices">' +
                '<div class="shop-card-price-column"><span class="price-label">Listing Price:</span><p class="listing-price"><strong>' + formatCurrency(p.price) + '</strong></p></div>' +
                '<div class="shop-card-price-column"><span class="price-label">Market Price:</span><p class="market-price"><strong>' + formatCurrency(p.marketPrice) + '</strong></p></div>' +
              '</div>' +
              '<span class="stock' + (p.stock <= 0 ? ' stock--sold-out' : '') + '">' + stockLabel + '</span>' +
            '</div>' +
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
  function getChipLabel(group, value) {
    if (group === 'search') {
      return 'Search: "' + value + '"'; // Pretty label for active text searches
    }
    if (group === 'cardType' || group === 'rarity') {
      var config = CATEGORY_FILTER_CONFIG[state.filters.category];
      if (!config) return value;
      var match = config[group].filter(function (opt) { return opt.value === value; })[0];
      return match ? match.label : value;
    }
    return (FILTER_LABELS[group] && FILTER_LABELS[group][value]) || value;
  }

  function renderChips() {
    var chips = [];

    Object.keys(state.filters).forEach(function (group) {
      var value = state.filters[group];
      if (value !== 'all' && value !== '') {
        chips.push({ group: group, label: getChipLabel(group, value) });
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
    if (group === 'search') {
      state.filters.search = '';
      var mainSearchInput = document.querySelector('.search-input');
      if (mainSearchInput) mainSearchInput.value = '';
    } else {
      state.filters[group] = 'all';
    }
    var selectMap = { category: filterCategory, cardType: filterCardType, price: filterPrice, rarity: filterRarity, condition: filterCondition };
    if (selectMap[group]) selectMap[group].value = 'all';

    if (group === 'category') {
      updateCardTypeAndRarityOptions();
    }

    state.page = 1;
    renderChips();
    renderGrid();
  }

  /* ------------------------------------------------------
    8) FILTER / SORT / CLEAR EVENT WIRING
  ------------------------------------------------------ */
  filterCategory.addEventListener('change', function () {
    state.filters.category = filterCategory.value;
    updateCardTypeAndRarityOptions();
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
    state.filters = { category: 'all', cardType: 'all', price: 'all', rarity: 'all', condition: 'all', search:''};
    var mainSearchInput = document.querySelector('.search-input');
    if (mainSearchInput) mainSearchInput.value = '';
    state.sort = 'relevance';
    state.page = 1;
    filterCategory.value = 'all';
    updateCardTypeAndRarityOptions();
    filterPrice.value = 'all';
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
  var modalSoldOutBadge = document.getElementById('modal-out-of-stock-badge');
  var modalTitle        = document.getElementById('productModalTitle');
  var modalSubtitle     = document.getElementById('modal-product-subtitle');
  var modalDescription  = document.getElementById('modal-description');
  var modalPrice        = document.getElementById('modal-price');
  var modalComparePrice = document.getElementById('modal-compare-price');
  var modalRequirements = document.getElementById('modal-requirements');
  var modalCategory     = document.getElementById('modal-category');
  var modalCondition    = document.getElementById('modal-condition');
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
    modalSoldOutBadge.classList.toggle('d-none', product.stock > 0);
    modalTitle.textContent = product.title;
    modalSubtitle.textContent = product.subtitle;
    modalDescription.textContent = product.description || '';
    modalPrice.textContent = formatCurrency(product.price);

    if (product.marketPrice > product.price) {
      modalComparePrice.textContent = formatCurrency(product.marketPrice);
      modalComparePrice.classList.remove('d-none');
    } else {
      modalComparePrice.classList.add('d-none');
    }

    var requirements = Array.isArray(product.requirements) ? product.requirements.filter(function (requirement) {
      return requirement && (requirement.label || requirement.value);
    }) : [];

    if (modalRequirements) {
      modalRequirements.innerHTML = requirements.map(function (requirement) {
        var label = requirement.label || '';
        var value = requirement.value || '';
        return `
        <li class="requirement-item">
            <span class="requirement-label">${label}${label && value ? ': ' : ''}<span class="requirement-value">${value}</span></span>
        </li>
    `;
      }).join('');
      modalRequirements.classList.toggle('d-none', requirements.length === 0);
    }
    modalCategory.textContent = product.category;
    modalCondition.textContent = product.conditionLabel || FILTER_LABELS.condition[product.condition] || product.condition || 'Not specified';
    modalQtyValue.textContent = modalQty;

    var soldOut = Number(product.stock) <= 0;
    modalAddToCartBtn.disabled = soldOut;
    modalAddToCartBtn.innerHTML = soldOut
      ? '<span class="text-light">Out of Stock</span>'
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

  function showToast(message, type = "success") {
    let container = document.getElementById("toast-container");
    if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        container.className = "app-toast-container";
        document.body.appendChild(container);
    }

    container.innerHTML = "";

    const toast = document.createElement("div");
    toast.className = "app-toast " + type;
    toast.innerHTML = '<span class="toast-icon">' + (type === 'success' ? '✓' : '!') + '</span><span>' + message + '</span>';
    container.appendChild(toast);

    setTimeout(function () {
        toast.classList.add('toast-out');
        setTimeout(function () { toast.remove(); }, 300);
    }, 2500);
}
  modalQtyStepper.querySelector('.qty-increase').addEventListener('click', function () {

    if (!activeProduct) return;

    var maxStock = Number(activeProduct.stock);

    if (isNaN(maxStock) || maxStock <= 0) {
        showToast('Out of Stock', 'error');
        return;
    }

    if (modalQty >= maxStock) {
        showToast('Only ' + maxStock + ' left in stock.', 'error');
        return;
    }

    modalQty++;
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
    if (!activeProduct || Number(activeProduct.stock) <= 0) return;

    modalAddToCartBtn.disabled = true;
    var originalLabel = modalAddToCartBtn.innerHTML;
    modalAddToCartBtn.innerHTML = 'Adding…';

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ productId: activeProduct.id, quantity: modalQty })
    })
        .then(function (response) { return response.json(); })
        .then(function (result) {
            if (!result.success) {
                showToast(result.message || 'Could not add this to your cart.', 'error');
                modalAddToCartBtn.innerHTML = originalLabel;
                modalAddToCartBtn.disabled = false;
                return;
            }

            var badge = document.getElementById('nav-cart-badge');
            if (badge) badge.textContent = result.cartCount;

            showToast('Added to Cart', 'success');
            modalAddToCartBtn.innerHTML = originalLabel;
            modalAddToCartBtn.disabled = false;
            productModal.hide();
        })
        .catch(function (err) {
            console.error('Add to cart failed:', err);
            showToast('Something went wrong adding this to your cart.', 'error');
            modalAddToCartBtn.innerHTML = originalLabel;
            modalAddToCartBtn.disabled = false;
        });
  }

  modalAddToCartBtn.addEventListener('click', addActiveProductToCart);

  /* ------------------------------------------------------
    11) INITIAL LOAD + RENDER
    Filters/sort/pagination controls can be interacted with
    immediately, but the grid itself only renders once
    products.json has actually loaded.
  ------------------------------------------------------ */
  loadProducts().then(function () {

  filterCategory.value = state.filters.category;
    updateCardTypeAndRarityOptions();

    // --- ADD THIS PIECE HERE ---
    // If cardType was passed via URL, re-assign it and update the dropdown selection
    if (cardType) {
        state.filters.cardType = cardType;
        filterCardType.value = cardType;
    }
    // ----------------------------
    if (state.filters.search) {
        var mainSearchInput = document.querySelector('.search-input');
        if (mainSearchInput) mainSearchInput.value = state.filters.search;
    }

    renderChips();
    renderGrid();

    if (productId) {
        openProductModal(productId);
    }
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
    
