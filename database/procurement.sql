CREATE TABLE IF NOT EXISTS suppliers (
    supplier_id INT NOT NULL AUTO_INCREMENT,
    supplier_name VARCHAR(120) NOT NULL,
    PRIMARY KEY (supplier_id),
    UNIQUE KEY suppliers_name_unique (supplier_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO suppliers (supplier_name)
SELECT 'Vault Card Supplies'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Vault Card Supplies');
INSERT INTO suppliers (supplier_name)
SELECT 'Northstar TCG Distribution'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Northstar TCG Distribution');
INSERT INTO suppliers (supplier_name)
SELECT 'Eclipse Collectibles Wholesale'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Eclipse Collectibles Wholesale');

CREATE TABLE IF NOT EXISTS procurement_orders (
    procurement_order_id INT NOT NULL AUTO_INCREMENT,
    supplier_id INT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    order_status ENUM('Pending','Processing','In Transit','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (procurement_order_id),
    KEY procurement_orders_supplier_idx (supplier_id),
    CONSTRAINT procurement_orders_supplier_fk FOREIGN KEY (supplier_id)
        REFERENCES suppliers (supplier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS procurement_order_items (
    procurement_order_item_id INT NOT NULL AUTO_INCREMENT,
    procurement_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (procurement_order_item_id),
    KEY procurement_items_order_idx (procurement_order_id),
    KEY procurement_items_product_idx (product_id),
    CONSTRAINT procurement_items_order_fk FOREIGN KEY (procurement_order_id)
        REFERENCES procurement_orders (procurement_order_id) ON DELETE CASCADE,
    CONSTRAINT procurement_items_product_fk FOREIGN KEY (product_id)
        REFERENCES products (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
