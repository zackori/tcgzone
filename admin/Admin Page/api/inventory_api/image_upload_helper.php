<?php
/* ============================================================
   Shared image upload/delete handler for the Inventory admin
   page. Used by add_product.php, update_product.php, and
   delete_product.php — so the file location only lives here,
   in ONE place, instead of being duplicated across files.


   >>> EDIT THESE TWO LINES TO MATCH YOUR SERVER <<<

   PRODUCT_IMAGE_UPLOAD_DIR
     The real folder ON DISK where uploaded card images get
     saved. Built from __DIR__ (this file's own folder) so it
     works no matter how PHP is invoked — just edit the
     '/../../../../assets/images/products/' part if your assets
     folder lives somewhere else relative to this file
     (Admin Page/api/inventory_api/).

   PRODUCT_IMAGE_WEB_PATH
     The URL prefix your <img src="..."> tags need to actually
     load that same folder in a browser — this is what gets
     stored in the database and used on both the admin table
     and the customer-facing shop page.
   ============================================================ */
define('PRODUCT_IMAGE_UPLOAD_DIR', __DIR__ . '/../../../../assets/images/products/');
define('PRODUCT_IMAGE_WEB_PATH', '/tcgzone/assets/images/products/');


function handleImageUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxBytes = 1 * 1024 * 1024; // 1MB, matches the upload field's stated limit

    if (!in_array($file['type'], $allowedTypes, true)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, or PNG images are allowed.'];
    }
    if ($file['size'] > $maxBytes) {
        return ['success' => false, 'message' => 'Image must be less than 1MB.'];
    }

    if (!is_dir(PRODUCT_IMAGE_UPLOAD_DIR)) {
        mkdir(PRODUCT_IMAGE_UPLOAD_DIR, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename  = uniqid('card_', true) . '.' . $extension;

    if (!move_uploaded_file($file['tmp_name'], PRODUCT_IMAGE_UPLOAD_DIR . $filename)) {
        return ['success' => false, 'message' => 'Failed to save the uploaded image.'];
    }

    // This is the value that gets stored in products.image, and is what
    // both the admin table and the customer shop page's <img src> use.
    return ['success' => true, 'path' => PRODUCT_IMAGE_WEB_PATH . $filename];
}

/* ---- Shared delete, used when replacing an image or removing a product ---- */
function deleteProductImageFile($webPath) {
    if (!$webPath) return;

    // Only the filename matters here — rebuilding it against
    // PRODUCT_IMAGE_UPLOAD_DIR means this always points at the same
    // real folder as handleImageUpload() just saved it to, even if
    // PRODUCT_IMAGE_WEB_PATH and the actual disk path don't share the
    // same prefix (e.g. behind a CDN or a symlinked folder).
    $filename = basename($webPath);
    $localPath = PRODUCT_IMAGE_UPLOAD_DIR . $filename;

    if (is_file($localPath)) {
        @unlink($localPath);
    }
}
