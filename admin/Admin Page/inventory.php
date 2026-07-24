<?php
$pageTitle = 'Inventory';
$currentPage = 'inventory';
session_start();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | <?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="admin-shared.css">
  <link rel="stylesheet" href="inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
</head>

<body>
  <div class="container">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php include 'includes/header.php'; ?>
      <h1 class="page-title text-center">Inventory</h1>
      <section class="chart-card">
        <h2 class="panel-title">Card Management</h2>
        <div class="panel-toolbar">
          <!-- <button class="btn-add-card" type="button" id="addCardBtn"><i class="fa-solid fa-plus"></i> Add New
            Card</button> -->
          <div class="toolbar-right">
            <select class="filter-select" id="stockFilter" aria-label="Filter by stock status">
              <option value="all">Filter: All</option>
              <option value="in-stock">In Stock</option>
              <option value="low-stock">Low on Stock</option>
              <option value="out-of-stock">Out of Stock</option>
            </select>
            <select class="filter-select" id="productIdSort" aria-label="Sort by product ID">
              <option value="desc">Product ID: Descending</option>
              <option value="asc">Product ID: Ascending</option>
            </select>
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="search"
                id="cardSearchInput" placeholder="Search"></div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="inventory-table">
            <thead>
              <tr>
                <th>Product ID</th>
                <th>Image</th>
                <th>Card Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Stock Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody"></tbody>
          </table>
        </div>
        <div class="pagination-row" id="paginationRow"></div>
      </section>
    </main>
  </div>

  <div class="admin-modal-backdrop" id="cardModalBackdrop">
    <div class="admin-modal card-modal" role="dialog" aria-modal="true" aria-label="Card editor">
      <div class="card-modal-image-col">
        <div class="existing-image-wrap d-none" id="existingImageWrap"><img src="" alt="Card preview"
            id="existingImagePreview"><button type="button" class="remove-image-btn" id="removeImageBtn"
            aria-label="Remove image">&times;</button></div>
        <div class="upload-field" id="uploadField"><label for="cardImageInput" class="btn-choose-file"><i
              class="fa-solid fa-image"></i> Choose File</label><span class="upload-filename" id="uploadFilename">No
            file chosen</span><span class="upload-hint">JPG, JPEG, PNG less than 1MB</span></div>
        <input type="file" id="cardImageInput" accept=".jpg,.jpeg,.png" hidden>
      </div>
      <div class="card-modal-form-col">
        <form id="cardForm">
          <input type="hidden" id="productId">
          <label class="form-label" for="cardName">Card Name:</label><input class="form-control" id="cardName" required>
          <label class="form-label" for="cardCategory">Category:</label><select class="form-select" id="cardCategory"
            required>
            <option value="Pokémon">Pokémon</option>
            <option value="Magic: The Gathering">Magic: The Gathering</option>
            <option value="One Piece">One Piece</option>
          </select>
          <label class="form-label" for="cardProductType">Product Type:</label><select class="form-select" id="cardProductType" required>
            <option value="Cards">Cards</option>
            <option value="Sealed">Sealed</option>
            <option value="Collections">Collections</option>
            <option value="Character">Character</option>
            <option value="Leader">Leader</option>
            <option value="Artifact">Artifact</option>
            <option value="Legendary Creature">Legendary Creature</option>
            <option value="Legendary Artifact">Legendary Artifact</option>
            <option value="Enchantment">Enchantment</option>
            <option value="Instant">Instant</option>
            <option value="Creature">Creature</option>
          </select>
          <label class="form-label" for="cardRarity">Rarity:</label><select class="form-select" id="cardRarity" required>
            <option value="">Select rarity</option>
            <option value="Common">Common</option>
            <option value="Uncommon">Uncommon</option>
            <option value="Rare">Rare</option>
            <option value="Ultra Rare">Ultra Rare</option>
            <option value="Secret Rare">Secret Rare</option>
            <option value="C">C</option>
            <option value="UC">UC</option>
            <option value="R">R</option>
            <option value="SR">SR</option>
            <option value="SEC">SEC</option>
            <option value="L">L</option>
            <option value="P">P</option>
            <option value="SP">SP</option>
            <option value="AA">AA</option>
            <option value="TR">TR</option>
            <option value="MR">MR</option>
            <option value="M">M</option>
            <option value="S">S</option>
            <option value="U">U</option>
          </select>
          <label class="form-label" for="cardCondition">Condition:</label><select class="form-select" id="cardCondition" required>
            <option value="">Select condition</option>
            <option value="Mint">Mint</option>
            <option value="Near Mint">Near Mint</option>
            <option value="Lightly Played">Lightly Played</option>
            <option value="Damaged">Damaged</option>
          </select>
          <label class="form-label" for="cardPrice">Price:</label>
          <div class="peso-input"><span class="peso-sign">₱</span><input type="number" step="0.01" min="0"
              class="form-control" id="cardPrice" required></div>
          <label class="form-label" for="cardStock">Stock Quantity:</label><input type="number" min="0"
            class="form-control" id="cardStock" required>
          <label class="form-label" for="cardDescription">Description:</label><textarea class="form-control"
            id="cardDescription" rows="3" placeholder="Customer-facing product description"></textarea>
          <label class="form-label">Product Details:</label>
          <div id="requirementsEditor" class="requirements-editor"></div><button class="add-requirement-btn"
            type="button" id="addRequirementBtn"><i class="fa-solid fa-plus"></i> Add detail</button>
          <div class="modal-actions"><button class="btn-save" type="submit" id="cardFormSaveBtn">Save</button><button
              class="btn-cancel" type="button" id="cardFormCancelBtn">Cancel</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="admin-modal-backdrop" id="deleteModalBackdrop">
    <div class="admin-modal confirm-modal">
      <p class="confirm-text">Are you sure you want to delete <strong id="deleteCardName"></strong>?</p>
      <div class="confirm-actions"><button class="btn-yes" type="button" id="confirmDeleteBtn">Yes</button><button
          class="btn-no" type="button" id="cancelDeleteBtn">No</button></div>
    </div>
  </div>
  <div class="admin-modal-backdrop" id="successModalBackdrop">
    <div class="admin-modal success-modal">
      <p class="success-text">Successfully <span id="successActionWord" class="success-action"></span> <strong
          id="successCardName"></strong> <span id="successActionSuffix"></span></p><button class="btn-back"
        type="button" id="successBackBtn">Back</button>
    </div>
  </div>
  <script src="inventory.js?v=<?= filemtime(__DIR__ . '/inventory.js') ?>"></script>
</body>

</html>
